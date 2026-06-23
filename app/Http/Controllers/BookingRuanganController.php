<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingRuangan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class BookingRuanganController extends Controller
{
    public function index(Request $request)
    {
        // 1. Tentukan tanggal yang sedang dipilih (Default: Hari ini)
        $selectedDate = $request->date ?? date('Y-m-d');

        // 2. Ambil data booking berdasarkan tanggal yang dipilih
        $bookings = BookingRuangan::with(['user', 'ruangan'])
                    ->whereDate('tanggal_booking', $selectedDate)
                    ->orderBy('waktu_mulai', 'asc')
                    ->get();

        // 3. Ambil data mahasiswa untuk modal
        $users = \App\Models\User::where('role', 'peminjam')->get();

        // 4. LOGIKA KALENDER DINAMIS
        $currentMonth = date('m', strtotime($selectedDate));
        $currentYear = date('Y', strtotime($selectedDate));

        // Nama Bulan & Tahun untuk Header (Contoh: February 2026)
        $monthName = date('F Y', strtotime($selectedDate));

        // Tanggal untuk tombol Bulan Sebelumnya (<) dan Bulan Selanjutnya (>)
        $prevMonth = date('Y-m-d', strtotime('-1 month', strtotime($currentYear.'-'.$currentMonth.'-01')));
        $nextMonth = date('Y-m-d', strtotime('+1 month', strtotime($currentYear.'-'.$currentMonth.'-01')));

        // Menghitung jumlah hari dalam bulan tersebut dan hari pertama jatuh di hari apa (0=Minggu, 6=Sabtu)
        $daysInMonth = date('t', strtotime($selectedDate));
        $startDayOfWeek = date('w', strtotime($currentYear . '-' . $currentMonth . '-01'));

        // 5. Ambil SEMUA booking untuk tabel di bawah (dengan filter opsional)
        $filterStatus = $request->filter_status ?? '';
        $searchBooking = $request->search ?? '';

        $allBookingsQuery = BookingRuangan::with(['user', 'ruangan'])
            ->orderBy('tanggal_booking', 'desc')
            ->orderBy('waktu_mulai', 'asc');

        if ($filterStatus) {
            $allBookingsQuery->where('status', $filterStatus);
        }
        if ($searchBooking) {
            $allBookingsQuery->where(function($q) use ($searchBooking) {
                $q->where('keperluan', 'like', "%{$searchBooking}%")
                  ->orWhereHas('ruangan', fn($r) => $r->where('nama_ruangan', 'like', "%{$searchBooking}%"))
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$searchBooking}%"))
                  ->orWhere('nama_peminjam', 'like', "%{$searchBooking}%");
            });
        }

        $allBookings = $allBookingsQuery->paginate(15)->withQueryString();

        return view('operator.booking.index', compact(
            'bookings', 'users', 'selectedDate', 'monthName',
            'currentMonth', 'currentYear', 'daysInMonth', 'startDayOfWeek',
            'prevMonth', 'nextMonth', 'allBookings', 'filterStatus', 'searchBooking'
        ));
    }

public function store(Request $request)
    {
        $request->validate([
            'user_id'         => 'nullable', // Sekarang boleh kosong
            'nama_peminjam'   => 'required_without:user_id', // Wajib diisi jika user_id kosong
            'ruangan_id'      => 'required',
            'tanggal_booking' => 'required|date',
            'waktu_mulai'     => 'required',
            'waktu_selesai'   => 'required|after:waktu_mulai',
            'keperluan'       => 'required|string|max:255',
            'surat_peminjaman' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
            'nama_peminjam.required_without' => 'Nama peminjam wajib diisi jika tidak memilih akun mahasiswa!'
        ]);

        $nama_surat = null;
        if ($request->hasFile('surat_peminjaman')) {
            $file = $request->file('surat_peminjaman');
            $nama_surat = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat_peminjaman', $nama_surat, 'public');
        }

// CEK BENTROK (Overlap Check) - Hanya cek yang statusnya pending atau disetujui
        $bentrok = BookingRuangan::where('ruangan_id', $request->ruangan_id)
            ->where('tanggal_booking', $request->tanggal_booking)
            ->whereIn('status', ['pending', 'disetujui']) // <-- TAMBAHKAN BARIS INI
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                    });
            })->exists();

        if ($bentrok) {
            return redirect()->back()->with('error', 'Maaf, ruangan tersebut sudah dibooking pada jam yang Anda pilih!');
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
                return redirect()->back()->with('error', 'Maaf, ruangan sedang dipakai untuk Jadwal Kuliah (' . $bentrokKuliah->mata_kuliah . ') pada waktu tersebut!');
            }
        }

        DB::transaction(function() use ($request, $nama_surat) {
            BookingRuangan::create([
                'user_id'         => $request->user_id,
                'nama_peminjam'   => $request->nama_peminjam,
                'ruangan_id'      => $request->ruangan_id,
                'tanggal_booking' => $request->tanggal_booking,
                'waktu_mulai'     => $request->waktu_mulai,
                'waktu_selesai'   => $request->waktu_selesai,
                'keperluan'       => $request->keperluan,
                'surat_peminjaman' => $nama_surat,
                'status'          => 'disetujui'
            ]);
        });

        return redirect()->route('booking.index')->with('success', 'Jadwal ruangan berhasil ditambahkan!');
    }

    public function markAsDone($id)
    {
        $booking = BookingRuangan::findOrFail($id);

        if ($booking->status === 'selesai') {
            return redirect()->back()->with('error', 'Ruangan ini sudah ditandai selesai sebelumnya!');
        }

        $booking->update([
            'status' => 'selesai'
        ]);

        return redirect()->back()->with('success', 'Status ruangan berhasil ditandai selesai!');
    }

    public function approve($id)
    {
        $booking = BookingRuangan::findOrFail($id);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Status sudah ' . $booking->status . ', tidak bisa disetujui lagi.');
        }

        $booking->update(['status' => 'disetujui']);
        return redirect()->back()->with('success', 'Pengajuan ruangan berhasil disetujui!');
    }

    public function reject($id)
    {
        $booking = BookingRuangan::findOrFail($id);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Status sudah ' . $booking->status . ', tidak bisa ditolak.');
        }

        $booking->update(['status' => 'ditolak']);
        return redirect()->back()->with('success', 'Pengajuan ruangan telah ditolak.');
    }
}
