<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\BookingRuangan;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistik Keseluruhan
        $total_users = User::count();
        $total_barang = Barang::count(); // 1 row = 1 item fisik
        $total_ruangan = Ruangan::count();
        $total_peminjaman = Peminjaman::count();
        $total_booking = BookingRuangan::count();

        // Ambil Data Terbaru
        $recent_users = User::latest()->take(5)->get();
        $recent_peminjamans = Peminjaman::with(['user', 'barangs'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'total_users', 
            'total_barang', 
            'total_ruangan', 
            'total_peminjaman', 
            'total_booking',
            'recent_users',
            'recent_peminjamans'
        ));
    }
}
