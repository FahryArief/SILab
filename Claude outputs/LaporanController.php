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
use App\Models\JadwalKuliah;
use App\Models\TahunAjaran;
use App\Models\Pemeliharaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tahunIni = $request->input('tahun', date('Y'));

        // ========== STATISTIK RINGKASAN ==========
        $totalBarang = Barang::count();
        // Hitung ruangan yang aktif pada tahun yang difilter (termasuk ruangan
        // yang sudah dinonaktifkan tapi masih aktif di tahun tsb), bukan cuma
        // ruangan yang aktif sekarang — supaya statistik historis konsisten.
        $totalRuangan = Ruangan::aktifPadaTahun($tahunIni)->count();
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

        // ========== DATA UNTUK FILTER TAHUN AJARAN DI LAPORAN ==========
        $tahunAjarans = TahunAjaran::orderByDesc('tanggal_mulai')->orderByDesc('id')->get();
        $ruanganList = Ruangan::select(['id', 'nama_ruangan'])->orderBy('nama_ruangan')->get();
        $barangList = Barang::select(['id', 'nama_barang', 'barcode'])->orderBy('nama_barang')->get();

        return view('operator.laporan.index', compact(
            'dataTren', 'labelKategori', 'dataKategori', 'labelRuang', 'dataRuang', 'tahunIni',
            'totalBarang', 'totalRuangan', 'totalPeminjaman', 'totalBooking',
            'kondisiData', 'kondisiLabel', 'statusStats', 'dataBookingTren', 'auditPeriodes',
            'tahunAjarans', 'ruanganList', 'barangList'
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
     *
     * Bisa difilter per Tahun Ajaran (?tahun_ajaran_id=). Kalau diisi, lokasi
     * (ruangan) tiap barang yang ditampilkan diambil dari histori lokasinya
     * pada Tahun Ajaran itu (lihat Barang::ruanganPadaTahunAjaran), bukan
     * ruangan_id saat ini — supaya laporan historis tetap akurat walau
     * barangnya sudah dipindah ruangan sejak saat itu.
     */
    public function cetakBarang(Request $request)
    {
        $tahunAjaran = $request->filled('tahun_ajaran_id')
            ? TahunAjaran::find($request->tahun_ajaran_id)
            : null;

        $barangs = Barang::with(['kategori:id,nama_kategori', 'ruangan:id,nama_ruangan'])
                    ->orderBy('nama_barang')
                    ->get();

        if ($tahunAjaran) {
            $barangs->each(function ($barang) use ($tahunAjaran) {
                $barang->setRelation('ruangan', $barang->ruanganPadaTahunAjaran($tahunAjaran->id));
            });
        }

        $pdf = Pdf::loadView('operator.laporan.pdf_barang', compact('barangs', 'tahunAjaran'));
        $pdf->setPaper('A4', 'landscape');

        $namaFile = $tahunAjaran ? str_replace('/', '-', $tahunAjaran->nama_tahun).'_'.$tahunAjaran->semester : date('Y-m-d');

        return $pdf->stream('Laporan_Data_Barang_'.$namaFile.'.pdf');
    }

    /**
     * Export PDF: Laporan Data Ruangan
     *
     * Bisa difilter per tahun (?tahun=2024) — akan menampilkan ruangan yang
     * aktif di tahun itu, termasuk ruangan yang sekarang sudah dinonaktifkan
     * tapi dulu masih dipakai. Berguna untuk laporan historis (akreditasi),
     * supaya kondisi tiap tahun tetap bisa ditunjukkan apa adanya.
     */
    public function cetakRuangan(Request $request)
    {
        // Dropdown di halaman Laporan sekarang mengirim tahun_ajaran_id, bukan
        // angka tahun polos lagi. Tahun kalender tetap dipakai di belakang
        // untuk logika scopeAktifPadaTahun() yang sudah teruji (supaya tidak
        // mengubah logika histori Ruangan yang sudah jalan) — tinggal
        // diturunkan dari tanggal_mulai Tahun Ajaran yang dipilih.
        $tahunAjaran = $request->filled('tahun_ajaran_id')
            ? TahunAjaran::find($request->tahun_ajaran_id)
            : null;

        $tahun = ($tahunAjaran && $tahunAjaran->tanggal_mulai)
            ? $tahunAjaran->tanggal_mulai->year
            : $request->input('tahun', date('Y'));

        $ruangans = Ruangan::aktifPadaTahun($tahun)
            ->withCount('barangs')
            ->orderBy('nama_ruangan')
            ->get();

        $pdf = Pdf::loadView('operator.laporan.pdf_ruangan', compact('ruangans', 'tahun', 'tahunAjaran'));

        return $pdf->stream('Laporan_Data_Ruangan_'.$tahun.'.pdf');
    }

    /**
     * Export PDF: Laporan Jadwal Penggunaan Lab
     *
     * Terkunci ke Tahun Ajaran (default: yang sedang aktif). Bisa diperhalus
     * dengan filter Ruangan tertentu dan/atau kata kunci Mata Kuliah.
     */
    public function cetakJadwal(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajarans,id',
            'ruangan_id' => 'nullable|exists:ruangans,id',
            'mata_kuliah' => 'nullable|string|max:255',
        ]);

        $tahunAjaran = $request->filled('tahun_ajaran_id')
            ? TahunAjaran::find($request->tahun_ajaran_id)
            : TahunAjaran::where('is_active', true)->first();

        if (!$tahunAjaran) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran yang dipilih maupun yang sedang aktif. Pilih atau aktifkan Tahun Ajaran dahulu.');
        }

        $ruanganFilter = $request->filled('ruangan_id')
            ? Ruangan::withTrashed()->find($request->ruangan_id)
            : null;

        $jadwals = JadwalKuliah::with(['ruangan' => function ($q) {
                $q->withTrashed();
            }])
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->when($ruanganFilter, fn($q) => $q->where('ruangan_id', $ruanganFilter->id))
            ->when($request->filled('mata_kuliah'), fn($q) => $q->where('mata_kuliah', 'like', '%'.$request->mata_kuliah.'%'))
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('waktu_mulai')
            ->get()
            ->groupBy(fn($jadwal) => $jadwal->ruangan->nama_ruangan ?? 'Ruangan Dihapus');

        $pdf = Pdf::loadView('operator.laporan.pdf_jadwal', compact('jadwals', 'tahunAjaran', 'ruanganFilter'));
        $pdf->setPaper('A4', 'landscape');

        $namaFile = 'Laporan_Jadwal_Kuliah_'.str_replace('/', '-', $tahunAjaran->nama_tahun).'_'.$tahunAjaran->semester;

        return $pdf->stream($namaFile.'.pdf');
    }

    /**
     * Export PDF: Laporan Pemeliharaan Laboratorium
     *
     * Log/riwayat pemeliharaan (perbaikan/perawatan) Barang & Ruangan, per
     * rentang tanggal — independen dari Tahun Ajaran (snapshot kejadian apa
     * adanya, sama seperti Laporan Peminjaman), bisa diperhalus dengan
     * filter jenis aset (Barang/Ruangan/Semua).
     */
    public function cetakPemeliharaan(Request $request)
    {
        $request->validate([
            'tgl_mulai' => 'required|date',
            'tgl_sampai' => 'required|date|after_or_equal:tgl_mulai',
            'jenis_aset' => 'nullable|in:barang,ruangan',
        ]);

        $mulai = $request->tgl_mulai;
        $sampai = $request->tgl_sampai;

        $pemeliharaans = Pemeliharaan::with(['barang:id,nama_barang', 'ruangan:id,nama_ruangan', 'teknisi:id,name'])
            ->whereBetween('tanggal_pemeliharaan', [$mulai, $sampai])
            ->when($request->filled('jenis_aset'), fn($q) => $q->where('jenis_aset', $request->jenis_aset))
            ->orderBy('tanggal_pemeliharaan', 'asc')
            ->get();

        $pdf = Pdf::loadView('operator.laporan.pdf_pemeliharaan', compact('pemeliharaans', 'mulai', 'sampai'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->stream('Laporan_Pemeliharaan_'.$mulai.'_sd_'.$sampai.'.pdf');
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
