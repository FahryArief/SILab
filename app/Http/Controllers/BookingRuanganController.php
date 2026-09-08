<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StoreBookingRequest;
use App\Models\BookingRuangan;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\BookingConflictService;

class BookingRuanganController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', BookingRuangan::class);

        // 1. Tentukan tanggal yang sedang dipilih (Default: Hari ini)
        $selectedDate = $request->date ?? date('Y-m-d');

        // 2. Ambil data booking berdasarkan tanggal yang dipilih
        $bookings = BookingRuangan::with(['user', 'ruangan'])
                    ->whereDate('tanggal_booking', $selectedDate)
                    ->orderBy('waktu_mulai', 'asc')
                    ->get();

        // 3. Ambil data mahasiswa untuk modal
        $users = User::where('role', 'peminjam')->get();

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

    public function store(StoreBookingRequest $request)
    {

        DB::transaction(function() use ($request) {
            // Upload surat with hashed name to private disk
            $nama_surat = null;
            if ($request->hasFile('surat_peminjaman')) {
                $file = $request->file('surat_peminjaman');
                $nama_surat = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('surat_peminjaman', $nama_surat, 'local');
            }

            // CEK BENTROK — Safer overlap condition:
            // existing.start < new.end AND existing.end > new.start
            Ruangan::whereKey($request->ruangan_id)->lockForUpdate()->firstOrFail();
            $bentrok = app(BookingConflictService::class)->hasBookingConflict(
                (int) $request->ruangan_id,
                $request->tanggal_booking,
                $request->waktu_mulai,
                $request->waktu_selesai
            );

            if ($bentrok) {
                throw new \Exception('BENTROK_BOOKING');
            }

            // CEK BENTROK DENGAN JADWAL KULIAH
            $tahunAjaranAktif = \App\Models\TahunAjaran::where('is_active', true)->first();
            if ($tahunAjaranAktif) {
                $daysMap = [
                    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat',
                    'Saturday' => 'Sabtu',
                ];
                $hariBooking = $daysMap[date('l', strtotime($request->tanggal_booking))];

                    $bentrokKuliah = app(BookingConflictService::class)->findClassConflict(
                        (int) $request->ruangan_id,
                        $tahunAjaranAktif->id,
                        $hariBooking,
                        $request->waktu_mulai,
                        $request->waktu_selesai
                    );

                if ($bentrokKuliah) {
                    throw new \Exception('BENTROK_KULIAH:' . $bentrokKuliah->mata_kuliah);
                }
            }

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
        $this->authorize('markAsDone', $booking);

        if ($booking->status !== 'disetujui') {
            return redirect()->back()->with('error', 'Hanya booking yang disetujui yang bisa ditandai selesai!');
        }

        $booking->update([
            'status' => 'selesai'
        ]);

        Log::info('Booking Ruangan diselesaikan', [
            'booking_id' => $booking->id,
            'user_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Status ruangan berhasil ditandai selesai!');
    }

    public function approve($id)
    {
        $booking = BookingRuangan::findOrFail($id);
        $this->authorize('approve', $booking);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Status sudah ' . $booking->status . ', tidak bisa disetujui lagi.');
        }

        // Re-check overlap inside transaction before approving
        DB::transaction(function() use ($booking) {
            $locked = BookingRuangan::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            Ruangan::whereKey($locked->ruangan_id)->lockForUpdate()->firstOrFail();
            $bentrok = app(BookingConflictService::class)->hasBookingConflict(
                $locked->ruangan_id, $locked->tanggal_booking, $locked->waktu_mulai,
                $locked->waktu_selesai, $locked->id
            );

            if ($bentrok) {
                throw new \Exception('BENTROK_APPROVE');
            }

            $locked->update(['status' => 'disetujui']);
            
            Log::info('Booking Ruangan disetujui', [
                'booking_id' => $booking->id,
                'admin_id' => auth()->id()
            ]);
        });

        return redirect()->back()->with('success', 'Pengajuan ruangan berhasil disetujui!');
    }

    public function reject($id)
    {
        $booking = BookingRuangan::findOrFail($id);
        $this->authorize('reject', $booking);

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Status sudah ' . $booking->status . ', tidak bisa ditolak.');
        }

        $booking->update(['status' => 'ditolak']);

        Log::info('Booking Ruangan ditolak', [
            'booking_id' => $booking->id,
            'admin_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Pengajuan ruangan telah ditolak.');
    }

    /**
     * Download surat booking (authorized endpoint).
     */
    public function downloadSurat($id)
    {
        $booking = BookingRuangan::findOrFail($id);
        $this->authorize('downloadSurat', $booking);

        if (!$booking->surat_peminjaman) {
            abort(404, 'Surat peminjaman tidak ditemukan.');
        }

        $path = 'surat_peminjaman/' . $booking->surat_peminjaman;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File surat peminjaman tidak ditemukan.');
        }

        return Storage::disk('local')->download($path);
    }
}
