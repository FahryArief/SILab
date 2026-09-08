<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\BookingRuangan;
use App\Models\Peminjaman;

class OperatorDashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Data Statistik Utama
        $total_barang = Barang::count(); // 1 row = 1 item fisik
        $total_ruangan = Ruangan::count();

        // 2. Hitung Pengajuan yang "Pending" (Butuh aksi Operator)
        $booking_pending = BookingRuangan::where('status', 'pending')->count();
        $peminjaman_pending = Peminjaman::where('status', 'pending')->count();
        $total_pending = $booking_pending + $peminjaman_pending;
        $peminjaman_terlambat = Peminjaman::where('status', 'disetujui')
            ->whereDate('tanggal_kembali', '<', now()->toDateString())
            ->count();

        // 3. Ambil Aktivitas/Pengajuan Terbaru (Masing-masing 3 terbaru)
        $recent_bookings = BookingRuangan::with([
            'user:id,name', 'ruangan:id,nama_ruangan',
        ])->latest()->take(3)->get();
        $recent_peminjamans = Peminjaman::with([
            'user:id,name', 'barangs:id,nama_barang,barcode',
        ])->latest()->take(3)->get();

        return view('operator.dashboard', compact(
            'total_barang', 'total_ruangan',
            'booking_pending', 'peminjaman_pending', 'total_pending',
            'peminjaman_terlambat',
            'recent_bookings', 'recent_peminjamans'
        ));
    }
}
