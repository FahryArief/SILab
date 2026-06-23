<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\BookingRuangan;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\AuditPeriode;
use App\Models\AuditBarang;
use App\Models\AuditRuangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahunIni = $request->input('tahun', date('Y'));

        // ========== STATISTIK RINGKASAN ==========
        $totalBarang = Barang::count();
        $totalRuangan = Ruangan::count();
        $totalPeminjaman = Peminjaman::whereYear('tanggal_pinjam', $tahunIni)->count();
        $totalBooking = BookingRuangan::whereYear('tanggal_booking', $tahunIni)->count();

        // Kondisi barang (consolidated)
        $kondisiStats = Barang::selectRaw("
            SUM(CASE WHEN kondisi = 'Baik' THEN 1 ELSE 0 END) as baik,
            SUM(CASE WHEN kondisi = 'Rusak Ringan' THEN 1 ELSE 0 END) as rusak_ringan,
            SUM(CASE WHEN kondisi = 'Rusak Berat' THEN 1 ELSE 0 END) as rusak_berat
        ")->first();

        $kondisiData = [(int)$kondisiStats->baik, (int)$kondisiStats->rusak_ringan, (int)$kondisiStats->rusak_berat];
        $kondisiLabel = ['Baik', 'Rusak Ringan', 'Rusak Berat'];

        // Status peminjaman
        $statusStats = Peminjaman::selectRaw("
            SUM(CASE WHEN status = 'disetujui' THEN 1 ELSE 0 END) as aktif,
            SUM(CASE WHEN status = 'dikembalikan' THEN 1 ELSE 0 END) as selesai,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
        ")->whereYear('tanggal_pinjam', $tahunIni)->first();

        // ========== DATA GRAFIK: Tren Peminjaman 12 Bulan ==========
        $trenRaw = Peminjaman::selectRaw('MONTH(tanggal_pinjam) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_pinjam', $tahunIni)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $dataTren = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataTren[] = $trenRaw->get($i, 0);
        }

        // ========== DATA GRAFIK: Distribusi Kategori Barang ==========
        $kategoriDist = Barang::join('kategoris', 'barangs.kategori_id', '=', 'kategoris.id')
            ->select('kategoris.nama_kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategoris.nama_kategori')
            ->orderByDesc('total')
            ->get();

        $labelKategori = $kategoriDist->pluck('nama_kategori')->toArray();
        $dataKategori = $kategoriDist->pluck('total')->toArray();

        // ========== DATA GRAFIK: Top 5 Ruangan ==========
        $topRuangan = BookingRuangan::select('ruangan_id', DB::raw('COUNT(*) as total'))
            ->groupBy('ruangan_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('ruangan:id,nama_ruangan')
            ->get();

        $labelRuang = $topRuangan->map(fn($item) => $item->ruangan->nama_ruangan ?? 'Ruang Dihapus')->toArray();
        $dataRuang = $topRuangan->pluck('total')->toArray();

        // ========== TREN BOOKING RUANGAN 12 BULAN ==========
        $trenBookingRaw = BookingRuangan::selectRaw('MONTH(tanggal_booking) as bulan, COUNT(*) as total')
            ->whereYear('tanggal_booking', $tahunIni)
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $dataBookingTren = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBookingTren[] = $trenBookingRaw->get($i, 0);
        }

        // ========== DATA UNTUK EXPORT AUDIT ==========
        $auditPeriodes = AuditPeriode::orderByDesc('tanggal_mulai')->get();

        return view('operator.laporan.index', compact(
            'dataTren', 'labelKategori', 'dataKategori', 'labelRuang', 'dataRuang', 'tahunIni',
            'totalBarang', 'totalRuangan', 'totalPeminjaman', 'totalBooking',
            'kondisiData', 'kondisiLabel', 'statusStats', 'dataBookingTren', 'auditPeriodes'
        ));
    }

    public function cetakPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_mulai' => 'required|date',
            'tgl_sampai' => 'required|date|after_or_equal:tgl_mulai',
        ]);

        $mulai = $request->tgl_mulai;
        $sampai = $request->tgl_sampai;

        $peminjamans = Peminjaman::with(['user:id,name', 'barangs:id,nama_barang,barcode'])
                        ->whereBetween('tanggal_pinjam', [$mulai, $sampai])
                        ->orderBy('tanggal_pinjam', 'asc')
                        ->get();

        $pdf = Pdf::loadView('operator.laporan.pdf_peminjaman', compact('peminjamans', 'mulai', 'sampai'));

        return $pdf->stream('Laporan_Peminjaman_Alat_'.$mulai.'_sd_'.$sampai.'.pdf');
    }

    /**
     * Export PDF: Laporan Data Barang
     */
    public function cetakBarang()
    {
        $barangs = Barang::with(['kategori:id,nama_kategori', 'ruangan:id,nama_ruangan'])
                    ->orderBy('nama_barang')
                    ->get();

        $pdf = Pdf::loadView('operator.laporan.pdf_barang', compact('barangs'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan_Data_Barang_'.date('Y-m-d').'.pdf');
    }

    /**
     * Export PDF: Laporan Data Ruangan
     */
    public function cetakRuangan()
    {
        $ruangans = Ruangan::withCount('barangs')->orderBy('nama_ruangan')->get();

        $pdf = Pdf::loadView('operator.laporan.pdf_ruangan', compact('ruangans'));

        return $pdf->stream('Laporan_Data_Ruangan_'.date('Y-m-d').'.pdf');
    }

    /**
     * Export PDF: Laporan Audit per Periode
     */
    public function cetakAudit(Request $request)
    {
        $request->validate([
            'periode_id' => 'required|exists:audit_periodes,id',
        ]);

        $periode = AuditPeriode::findOrFail($request->periode_id);

        $auditBarangs = null;
        $auditRuangans = null;

        if (in_array($periode->tipe, ['barang', 'semua'])) {
            $auditBarangs = AuditBarang::with(['barang.ruangan', 'teknisi:id,name'])
                ->where('audit_periode_id', $periode->id)
                ->get();
        }

        if (in_array($periode->tipe, ['ruangan', 'semua'])) {
            $auditRuangans = AuditRuangan::with(['ruangan', 'teknisi:id,name'])
                ->where('audit_periode_id', $periode->id)
                ->get();
        }

        $pdf = Pdf::loadView('operator.laporan.pdf_audit', compact('periode', 'auditBarangs', 'auditRuangans'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan_Audit_'.$periode->nama_periode.'_'.date('Y-m-d').'.pdf');
    }
}
