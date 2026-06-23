<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookingRuangan;
use App\Models\Peminjaman;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user_id = auth()->id();

        // Ambil data pengajuan milik user ini saja
        $my_bookings = BookingRuangan::with('ruangan')
                        ->where('user_id', $user_id)
                        ->latest()
                        ->take(5)
                        ->get();

        $my_peminjamans = Peminjaman::with('barangs')
                        ->where('user_id', $user_id)
                        ->latest()
                        ->take(5)
                        ->get();

        // Hitung total tanggungan (yang disetujui tapi belum dikembalikan/selesai)
        $tanggungan_barang = Peminjaman::where('user_id', $user_id)->where('status', 'disetujui')->count();
        $tanggungan_ruang = BookingRuangan::where('user_id', $user_id)->where('status', 'disetujui')->count();

        return view('user.dashboard', compact('my_bookings', 'my_peminjamans', 'tanggungan_barang', 'tanggungan_ruang'));
    }

    public function riwayat()
    {
        $user_id = auth()->id();

        // Ambil SEMUA data peminjaman barang milik user ini
        $peminjamans = Peminjaman::with('barangs')
                        ->where('user_id', $user_id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Ambil SEMUA data booking ruangan milik user ini
        $bookings = BookingRuangan::with('ruangan')
                        ->where('user_id', $user_id)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return view('user.riwayat', compact('peminjamans', 'bookings'));
    }
}