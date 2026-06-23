<?php

namespace App\Http\Controllers;

use App\Models\AuditBarang;
use App\Models\AuditPeriode;
use App\Models\Barang;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditBarangController extends Controller
{
    /**
     * Display a listing of the audits (now redirects to periode index).
     */
    public function index()
    {
        return redirect()->route('admin.audit.periode.index');
    }

    /**
     * Teknisi stores a new audit for a specific barang within a periode.
     */
    public function store(Request $request)
    {
        $request->validate([
            'audit_periode_id' => 'required|exists:audit_periodes,id',
            'barang_id' => 'required|exists:barangs,id',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'catatan' => 'nullable|string',
        ]);

        $periode = AuditPeriode::findOrFail($request->audit_periode_id);
        if (!in_array($periode->status, ['open', 'revisi'])) {
            return redirect()->back()->with('error', 'Periode audit ini sudah ditutup atau dilaporkan.');
        }

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran yang aktif.');
        }

        // Update or create audit entry for this barang in this periode
        AuditBarang::updateOrCreate(
            [
                'audit_periode_id' => $request->audit_periode_id,
                'barang_id' => $request->barang_id,
            ],
            [
                'tahun_ajaran_id' => $tahunAjaranAktif->id,
                'teknisi_id' => auth()->id(),
                'kondisi' => $request->kondisi,
                'catatan' => $request->catatan,
                'tanggal_audit' => Carbon::now(),
            ]
        );

        // Update kondisi di master Barang
        $barang = Barang::findOrFail($request->barang_id);
        $barang->update([
            'terakhir_diperiksa_at' => Carbon::now(),
            'kondisi' => $request->kondisi,
        ]);

        return redirect()->back()->with('success', 'Audit barang "' . $barang->nama_barang . '" berhasil disimpan.');
    }

    /**
     * Teknisi stores multiple audits at once (Bulk Audit).
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'audit_periode_id' => 'required|exists:audit_periodes,id',
            'barang_ids' => 'required|array',
            'barang_ids.*' => 'exists:barangs,id',
        ]);

        $periode = AuditPeriode::findOrFail($request->audit_periode_id);
        if (!in_array($periode->status, ['open', 'revisi'])) {
            return redirect()->back()->with('error', 'Periode audit ini tidak dapat diubah (status: ' . $periode->status . ').');
        }

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran yang aktif.');
        }

        $now = Carbon::now();
        $teknisiId = auth()->id();

        foreach ($request->barang_ids as $barangId) {
            // Update or create audit entry
            AuditBarang::updateOrCreate(
                [
                    'audit_periode_id' => $periode->id,
                    'barang_id' => $barangId,
                ],
                [
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'teknisi_id' => $teknisiId,
                    'kondisi' => 'Baik',
                    'catatan' => null,
                    'tanggal_audit' => $now,
                ]
            );
        }

        // Batch update master barang in a single query
        Barang::whereIn('id', $request->barang_ids)->update([
            'terakhir_diperiksa_at' => $now,
            'kondisi' => 'Baik',
        ]);

        return redirect()->back()->with('success', count($request->barang_ids) . ' barang berhasil diaudit secara massal dengan kondisi Baik.');
    }
}
