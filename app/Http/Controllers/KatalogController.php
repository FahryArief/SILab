<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\BookingRuangan;
use App\Services\BookingConflictService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class KatalogController extends Controller
{
    // 1. Menampilkan Katalog Barang
    public function barang()
    {
        // Ambil semua barang yang tersedia, mungkin dikelompokkan di view
        $barangs = Barang::where('status_peminjaman', 'Tersedia')->latest()->get();
        return view('user.katalog.barang', compact('barangs'));
    }

    // 2. Menyimpan Pengajuan Barang
    public function storeBarang(Request $request)
    {
        $request->validate([
            'barang_ids' => 'required|array|min:1',
            'barang_ids.*' => 'distinct|exists:barangs,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:255',
            'surat_peminjaman' => 'nullable|file|mimes:pdf,jpg,png|max:2048' // Opsional
        ]);

        $nama_surat = null;
        if ($request->hasFile('surat_peminjaman')) {
            $file = $request->file('surat_peminjaman');
            $nama_surat = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat_peminjaman', $nama_surat, 'public');
        }

        DB::transaction(function () use ($request, $nama_surat) {
            $barangs = Barang::whereIn('id', $request->barang_ids)
                ->lockForUpdate()
                ->get();

            if ($barangs->count() !== count($request->barang_ids)
                || $barangs->contains(fn (Barang $barang) => $barang->status_peminjaman !== 'Tersedia'
                    || $barang->kondisi !== 'Baik')) {
                throw ValidationException::withMessages([
                    'barang_ids' => 'Beberapa barang yang Anda pilih sudah tidak tersedia atau dalam kondisi rusak.',
                ]);
            }

            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'keperluan' => $request->keperluan,
                'surat_peminjaman' => $nama_surat,
                'status' => 'pending',
            ]);

            $peminjaman->barangs()->attach($barangs->mapWithKeys(fn (Barang $barang) => [
                $barang->id => [
                    'nama_barang_snapshot' => $barang->nama_barang,
                    'barcode_snapshot' => $barang->barcode,
                    'kondisi_snapshot' => $barang->kondisi,
                ],
            ])->all());
            $barangs->each->update(['status_peminjaman' => 'Dipinjam']);
        });

        return redirect('/peminjam/dashboard')->with('success', 'Pengajuan peminjaman alat berhasil dikirim! Silakan tunggu validasi Teknisi dan ACC Kepala Lab.');
    }

    // 3. Menampilkan Katalog Ruangan
    public function ruangan()
    {
        $ruangans = Ruangan::all();
        return view('user.katalog.ruangan', compact('ruangans'));
    }

    // 4. Menyimpan Pengajuan Booking Ruangan
    public function storeRuangan(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'tanggal_booking' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'keperluan' => 'required|string|max:255',
            'surat_peminjaman' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.'
        ]);

        $nama_surat = null;
        if ($request->hasFile('surat_peminjaman')) {
            $file = $request->file('surat_peminjaman');
            $nama_surat = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat_peminjaman', $nama_surat, 'public');
        }

        // Jadwal kuliah dan booking divalidasi ulang di dalam transaksi di bawah.
        $conflicts = app(BookingConflictService::class);
        $tahunAjaranAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
        if ($tahunAjaranAktif) {
            $daysMap = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ];
            $englishDay = date('l', strtotime($request->tanggal_booking));
            $hariBooking = $daysMap[$englishDay];

            $bentrokKuliah = $conflicts->findClassConflict(
                (int) $request->ruangan_id,
                $tahunAjaranAktif->id,
                $hariBooking,
                $request->waktu_mulai,
                $request->waktu_selesai
            );

            if ($bentrokKuliah) {
                return redirect()->back()->with('error', 'Maaf, ruangan tidak dapat dipinjam karena sedang dipakai untuk Jadwal Kuliah (' . $bentrokKuliah->mata_kuliah . ') pada jam tersebut!');
            }
        }

        try {
            DB::transaction(function () use ($request, $nama_surat, $conflicts) {
                Ruangan::whereKey($request->ruangan_id)->lockForUpdate()->firstOrFail();
                if ($conflicts->hasBookingConflict(
                    (int) $request->ruangan_id,
                    $request->tanggal_booking,
                    $request->waktu_mulai,
                    $request->waktu_selesai
                )) {
                    throw new \DomainException('Maaf, ruangan sudah dibooking / diajukan orang lain pada jam tersebut!');
                }

                BookingRuangan::create([
                    'user_id' => auth()->id(),
                    'ruangan_id' => $request->ruangan_id,
                    'tanggal_booking' => $request->tanggal_booking,
                    'waktu_mulai' => $request->waktu_mulai,
                    'waktu_selesai' => $request->waktu_selesai,
                    'keperluan' => $request->keperluan,
                    'surat_peminjaman' => $nama_surat,
                    'status' => 'pending',
                ]);
            });
        } catch (\DomainException $exception) {
            return redirect()->back()->with('error', $exception->getMessage())->withInput();
        }

        return redirect('/peminjam/dashboard')->with('success', 'Pengajuan booking ruangan berhasil dikirim! Silakan tunggu persetujuan Operator Lab.');
    }

    // 5. Mengambil Data Jadwal Ruangan via AJAX
    public function jadwalRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $tahunAjaranAktif = \App\Models\TahunAjaran::where('is_active', true)->first();

        $jadwalKuliah = [];
        if ($tahunAjaranAktif) {
            $jadwalKuliah = \App\Models\JadwalKuliah::where('ruangan_id', $id)
                ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->get()
                ->map(function ($item) {
                    return [
                        'jenis' => 'Kuliah',
                        'keterangan' => $item->mata_kuliah . ' (' . $item->dosen . ')',
                        'hari_tanggal' => $item->hari,
                        'waktu' => substr($item->waktu_mulai, 0, 5) . ' - ' . substr($item->waktu_selesai, 0, 5)
                    ];
                });
        }

        $booking = BookingRuangan::with('user')
            ->where('ruangan_id', $id)
            ->where('status', 'disetujui')
            ->where('tanggal_booking', '>=', date('Y-m-d'))
            ->get()
            ->map(function ($item) {
                return [
                    'jenis' => 'Booking',
                    'keterangan' => $item->keperluan . ' (' . $item->user->name . ')',
                    'hari_tanggal' => \Carbon\Carbon::parse($item->tanggal_booking)->translatedFormat('l, d M Y'),
                    'waktu' => substr($item->waktu_mulai, 0, 5) . ' - ' . substr($item->waktu_selesai, 0, 5)
                ];
            });

        $gabungan = collect($jadwalKuliah)->merge($booking)->sortBy('hari_tanggal')->values();

        return response()->json([
            'ruangan' => $ruangan->nama_ruangan,
            'jadwal' => $gabungan
        ]);
    }
}
