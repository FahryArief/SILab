<?php

namespace App\Http\Controllers;

use App\Models\AuditPeriode;
use App\Models\AuditBarang;
use App\Models\AuditRuangan;
use App\Models\Barang;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class AuditPeriodeController extends Controller
{
    /**
     * Tampilkan semua periode audit.
     * Kepala Lab: lihat semua + bisa buat baru.
     * Teknisi: lihat semua perintah audit yang masuk.
     */
    public function index()
    {
        $periodes = AuditPeriode::with('kepalaLab')
            ->latest()
            ->get();

        return view('admin.audit.periode.index', compact('periodes'));
    }

    /**
     * Kepala Lab membuat perintah audit baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_periode' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'tipe' => 'required|in:barang,ruangan,semua',
            'catatan' => 'nullable|string',
        ]);

        AuditPeriode::create([
            'nama_periode' => $request->nama_periode,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'tipe' => $request->tipe,
            'kepala_lab_id' => auth()->id(),
            'catatan' => $request->catatan,
            'status' => 'open',
        ]);

        return redirect()->back()->with('success', 'Perintah audit berhasil dibuat dan dikirim ke teknisi.');
    }

    /**
     * Lihat ringkasan detail satu periode audit.
     */
    public function show($id)
    {
        $periode = AuditPeriode::with(['kepalaLab'])->findOrFail($id);

        $totalAuditBarang = $periode->auditBarangs()->count();
        $totalAuditRuangan = $periode->auditRuangans()->count();
        
        $totalBarangTarget = in_array($periode->tipe, ['barang', 'semua']) ? Barang::count() : 0;
        $totalRuanganTarget = in_array($periode->tipe, ['ruangan', 'semua']) ? Ruangan::count() : 0;

        return view('admin.audit.periode.show', compact(
            'periode',
            'totalAuditBarang',
            'totalAuditRuangan',
            'totalBarangTarget',
            'totalRuanganTarget'
        ));
    }

    /**
     * Lihat halaman khusus Audit Barang untuk periode ini.
     */
    public function showBarang($id)
    {
        $periode = AuditPeriode::findOrFail($id);

        if (!in_array($periode->tipe, ['barang', 'semua'])) {
            return redirect()->route('admin.audit.periode.show', $id)->with('error', 'Periode ini tidak mencakup audit barang.');
        }

        $barangs = Barang::with(['kategori:id,nama_kategori', 'ruangan:id,nama_ruangan'])->get();
        
        // Single query: load once, derive both values
        $auditBarangs = $periode->auditBarangs;
        $auditedBarangIds = $auditBarangs->pluck('barang_id')->toArray();
        $auditBarangMap = $auditBarangs->keyBy('barang_id');

        return view('admin.audit.periode.barang', compact(
            'periode',
            'barangs',
            'auditedBarangIds',
            'auditBarangMap'
        ));
    }

    /**
     * Lihat halaman khusus Audit Ruangan untuk periode ini.
     */
    public function showRuangan($id)
    {
        $periode = AuditPeriode::findOrFail($id);

        if (!in_array($periode->tipe, ['ruangan', 'semua'])) {
            return redirect()->route('admin.audit.periode.show', $id)->with('error', 'Periode ini tidak mencakup audit ruangan.');
        }

        $ruangans = Ruangan::all();
        
        // Single query: load once, derive both values
        $auditRuangans = $periode->auditRuangans;
        $auditedRuanganIds = $auditRuangans->pluck('ruangan_id')->toArray();
        $auditRuanganMap = $auditRuangans->keyBy('ruangan_id');

        return view('admin.audit.periode.ruangan', compact(
            'periode',
            'ruangans',
            'auditedRuanganIds',
            'auditRuanganMap'
        ));
    }

    /**
     * Teknisi melaporkan hasil audit (ubah status → dilaporkan).
     */
    public function laporkan($id)
    {
        $periode = AuditPeriode::findOrFail($id);

        if (!in_array($periode->status, ['open', 'revisi'])) {
            return redirect()->back()->with('error', 'Periode audit ini sudah dilaporkan atau ditutup.');
        }

        $periode->update(['status' => 'dilaporkan']);

        return redirect()->route('admin.audit.periode.index')->with('success', 'Hasil audit berhasil dilaporkan ke Kepala Lab.');
    }

    /**
     * Kepala Lab memvalidasi laporan audit (setujui atau revisi).
     */
    public function validateAudit(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_revisi' => 'nullable|string|required_if:action,reject',
        ]);

        $periode = AuditPeriode::findOrFail($id);

        if ($request->action === 'approve') {
            $periode->update([
                'status' => 'disetujui',
                'catatan_revisi' => null,
            ]);
            return redirect()->back()->with('success', 'Periode audit berhasil disetujui dan ditutup.');
        } else {
            $periode->update([
                'status' => 'revisi',
                'catatan_revisi' => $request->catatan_revisi,
            ]);
            return redirect()->back()->with('success', 'Laporan audit ditolak dan dikembalikan ke teknisi untuk revisi.');
        }
    }
}
