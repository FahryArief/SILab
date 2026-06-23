<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\BookingRuangan;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use App\Models\JadwalKuliah;
use Carbon\Carbon;

class ProdiDashboardController extends Controller
{
    public function index()
    {
        $tahunIni = date('Y');
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        // 1. STATISTIK UMUM (WIDGETS)
        $totalBarang = Barang::count(); // Setiap baris = 1 item fisik unik
        $totalRuangan = Ruangan::count();
        $peminjamanPending = Peminjaman::where('status', 'pending')->count();
        $bookingPending = BookingRuangan::where('status', 'pending')->count();
        
        $jadwalAktifCount = 0;
        if ($tahunAjaranAktif) {
            $jadwalAktifCount = JadwalKuliah::where('tahun_ajaran_id', $tahunAjaranAktif->id)->count();
        }

        // 2. DATA GRAFIK: Tren Peminjaman (12 Bulan di Tahun Ini)
        $peminjamanTahunIni = Peminjaman::whereYear('tanggal_pinjam', $tahunIni)->get();
        $dataTren = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataTren[] = $peminjamanTahunIni->filter(function ($item) use ($i) {
                return Carbon::parse($item->tanggal_pinjam)->month == $i;
            })->count();
        }

        // 3. DATA GRAFIK: Top 5 Ruangan Sering Digunakan
        $bookings = BookingRuangan::with('ruangan')->get();
        $ruangDist = $bookings->groupBy(function($item) {
            return $item->ruangan->nama_ruangan ?? 'Ruang Dihapus';
        })->map->count()->sortDesc()->take(5);
        $labelRuang = $ruangDist->keys()->toArray();
        $dataRuang = $ruangDist->values()->toArray();

        return view('prodi.dashboard', compact(
            'totalBarang', 'totalRuangan', 'peminjamanPending', 'bookingPending', 'jadwalAktifCount', 'tahunAjaranAktif',
            'dataTren', 'labelRuang', 'dataRuang', 'tahunIni'
        ));
    }
}
