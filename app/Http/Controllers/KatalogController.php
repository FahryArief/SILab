<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\BookingRuangan;

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
            'barang_ids.*' => 'exists:barangs,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:255',
            'surat_peminjaman' => 'nullable|file|mimes:pdf,jpg,png|max:2048' // Opsional
        ]);

        // Cek ketersediaan lagi untuk keamanan
        $unavailableCount = Barang::whereIn('id', $request->barang_ids)
            ->where(function($q) {
                $q->where('status_peminjaman', '!=', 'Tersedia')
                  ->orWhere('kondisi', '!=', 'Baik');
            })
            ->count();
            
        if ($unavailableCount > 0) {
            return redirect()->back()->with('error', 'Beberapa barang yang Anda pilih sudah tidak tersedia atau dalam kondisi rusak!');
        }

        $nama_surat = null;
        if ($request->hasFile('surat_peminjaman')) {
            $file = $request->file('surat_peminjaman');
            $nama_surat = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat_peminjaman', $nama_surat, 'public');
        }

        $peminjaman = Peminjaman::create([
            'user_id' => auth()->id(),
            'tanggal_pinjam' => $request->tanggal_pinjam,
            'tanggal_kembali' => $request->tanggal_kembali,
            'keperluan' => $request->keperluan,
            'surat_peminjaman' => $nama_surat,
            'status' => 'pending'
        ]);

        // Attach ke tabel pivot peminjaman_barangs
        $peminjaman->barangs()->attach($request->barang_ids);

        // Update status fisik barang menjadi 'Dipinjam' sementara menunggu validasi?
        // Ataukah biarkan tersedia sampai disetujui? Biasanya di-'booked' atau langsung 'Dipinjam' supaya tidak bisa dipinjam orang lain.
        Barang::whereIn('id', $request->barang_ids)->update(['status_peminjaman' => 'Dipinjam']);

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
            'ruangan_id' => 'required',
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

        // Cek Bentrok Peminjaman Lain
        $bentrok = BookingRuangan::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal_booking', $request->tanggal_booking)
            ->whereIn('status', ['pending', 'disetujui'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                      });
            })->exists();

        if ($bentrok) {
            return redirect()->back()->with('error', 'Maaf, ruangan sudah dibooking / diajukan orang lain pada jam tersebut!');
        }

        // CEK BENTROK DENGAN JADWAL KULIAH
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

            $bentrokKuliah = \App\Models\JadwalKuliah::where('ruangan_id', $request->ruangan_id)
                ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
                ->where('hari', $hariBooking)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                          ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                                ->where('waktu_selesai', '>=', $request->waktu_selesai);
                          });
                })->first();

            if ($bentrokKuliah) {
                return redirect()->back()->with('error', 'Maaf, ruangan tidak dapat dipinjam karena sedang dipakai untuk Jadwal Kuliah (' . $bentrokKuliah->mata_kuliah . ') pada jam tersebut!');
            }
        }

        BookingRuangan::create([
            'user_id' => auth()->id(),
            'ruangan_id' => $request->ruangan_id,
            'tanggal_booking' => $request->tanggal_booking,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'keperluan' => $request->keperluan,
            'surat_peminjaman' => $nama_surat,
            'status' => 'pending'
        ]);

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
