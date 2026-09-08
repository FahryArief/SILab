<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\BookingRuangan;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use App\Models\JadwalKuliah;

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
        $peminjamanTahunIni = Peminjaman::selectRaw('MONTH(tanggal_pinjam) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pinjam', $tahunIni)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');
        $dataTren = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataTren[] = $peminjamanTahunIni->get($i, 0);
        }

        // 3. DATA GRAFIK: Top 5 Ruangan Sering Digunakan
        $topRuangan = BookingRuangan::select('ruangan_id', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
            ->groupBy('ruangan_id')->orderByDesc('total')->limit(5)
            ->with('ruangan:id,nama_ruangan')->get();
        $labelRuang = $topRuangan->map(fn ($item) => $item->ruangan->nama_ruangan ?? 'Ruang Dihapus')->toArray();
        $dataRuang = $topRuangan->pluck('total')->toArray();

        return view('prodi.dashboard', compact(
            'totalBarang', 'totalRuangan', 'peminjamanPending', 'bookingPending', 'jadwalAktifCount', 'tahunAjaranAktif',
            'dataTren', 'labelRuang', 'dataRuang', 'tahunIni'
        ));
    }
}
