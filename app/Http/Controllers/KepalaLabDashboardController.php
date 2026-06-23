<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\Peminjaman;
use App\Models\BookingRuangan;
use App\Models\TahunAjaran;
use App\Models\JadwalKuliah;
use Illuminate\Support\Facades\DB;

class KepalaLabDashboardController extends Controller
{
    public function index()
    {
        $tahunIni = date('Y');
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        // ========== STATISTIK WIDGETS (consolidated queries) ==========
        $barangStats = Barang::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as baik,
            SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan,
            SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat,
            SUM(CASE WHEN status_peminjaman = 'Tersedia' THEN 1 ELSE 0 END) as tersedia,
            SUM(CASE WHEN status_peminjaman = 'Dipinjam' THEN 1 ELSE 0 END) as dipinjam
        ")->first();

        $totalBarang = $barangStats->total;
        $totalRuangan = Ruangan::count();
        $barangBaik = $barangStats->baik;
        $barangRusakRingan = $barangStats->rusak_ringan;
        $barangRusakBerat = $barangStats->rusak_berat;
        $barangTersedia = $barangStats->tersedia;
        $barangDipinjam = $barangStats->dipinjam;

        // Consolidated peminjaman counts
        $peminjamanStats = Peminjaman::selectRaw("
            SUM(CASE WHEN status = 'divalidasi_teknisi' THEN 1 ELSE 0 END) as menunggu_acc,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as aktif
        ")->first();

        $menungguAcc = $peminjamanStats->menunggu_acc;
        $peminjamanPending = $peminjamanStats->pending;
        $totalAktif = $peminjamanStats->aktif;
        $bookingPending = BookingRuangan::where('status', 'pending')->count();

        // Jadwal kuliah aktif
        $jadwalAktifCount = 0;
        if ($tahunAjaranAktif) {
            $jadwalAktifCount = JadwalKuliah::where('tahun_ajaran_id', $tahunAjaranAktif->id)->count();
        }

        // ========== DATA GRAFIK: Tren Peminjaman 12 Bulan (DB-level aggregation) ==========
        $trenRaw = Peminjaman::selectRaw('MONTH(tanggal_pinjam) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pinjam', $tahunIni)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $dataTren = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataTren[] = $trenRaw->get($i, 0);
        }

        // ========== DATA GRAFIK: Kondisi Barang Pie Chart ==========
        $kondisiData = [$barangBaik, $barangRusakRingan, $barangRusakBerat];
        $kondisiLabel = ['Baik', 'Rusak Ringan', 'Rusak Berat'];

        // ========== DATA GRAFIK: Top 5 Ruangan Sering Digunakan (DB-level) ==========
        $topRuangan = BookingRuangan::select('ruangan_id', DB::raw('COUNT(*) as total'))
            ->groupBy('ruangan_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('ruangan:id,nama_ruangan')
            ->get();

        $labelRuang = $topRuangan->map(fn($item) => $item->ruangan->nama_ruangan ?? 'Ruang Dihapus')->toArray();
        $dataRuang = $topRuangan->pluck('total')->toArray();

        // ========== PENGAJUAN TERBARU (Menunggu ACC) ==========
        $recentMenungguAcc = Peminjaman::with(['user:id,name', 'barangs:id,nama_barang'])
            ->where('status', 'divalidasi_teknisi')
            ->latest()->take(5)->get();

        // ========== PEMINJAMAN AKTIF (Sedang Dipinjam) ==========
        $recentAktif = Peminjaman::with(['user:id,name', 'barangs:id,nama_barang'])
            ->where('status', 'disetujui')
            ->latest()->take(5)->get();

        return view('kepala_lab.dashboard', compact(
            'totalBarang', 'totalRuangan',
            'barangBaik', 'barangRusakRingan', 'barangRusakBerat',
            'barangTersedia', 'barangDipinjam',
            'menungguAcc', 'peminjamanPending', 'bookingPending', 'totalAktif',
            'jadwalAktifCount', 'tahunAjaranAktif',
            'dataTren', 'kondisiData', 'kondisiLabel',
            'labelRuang', 'dataRuang', 'tahunIni',
            'recentMenungguAcc', 'recentAktif'
        ));
    }
}
