<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use App\Models\Barang;
use Illuminate\Http\Request;

class ScanRuanganController extends Controller
{
    /**
     * Halaman publik: Scan QR Ruangan
     * Menampilkan detail ruangan beserta daftar barang di dalamnya
     */
    public function show($kode)
    {
        $ruangan = Ruangan::where('kode_ruangan', $kode)->firstOrFail();

        // Ambil semua barang fisik yang berada di ruangan ini
        $barangs = Barang::where('ruangan_id', $ruangan->id)
            ->orderBy('nama_barang')
            ->get();

        // Statistik ringkasan
        $totalBarang = $barangs->count();
        $barangBaik = $barangs->where('kondisi', 'Baik')->count();
        $barangRusakRingan = $barangs->where('kondisi', 'Rusak Ringan')->count();
        $barangRusakBerat = $barangs->where('kondisi', 'Rusak Berat')->count();
        $barangDipinjam = $barangs->where('status_peminjaman', 'Dipinjam')->count();

        return view('scan.ruangan', compact(
            'ruangan', 'barangs', 'totalBarang',
            'barangBaik', 'barangRusakRingan', 'barangRusakBerat', 'barangDipinjam'
        ));
    }
}
