<?php

namespace App\Http\Controllers;

use App\Models\AuditRuangan;
use App\Models\AuditPeriode;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AuditRuanganController extends Controller
{
    /**
     * Display a listing of the audits (now redirects to periode index).
     */
    public function index()
    {
        return redirect()->route('admin.audit.periode.index');
    }

    /**
     * Teknisi stores a new audit for a specific ruangan within a periode.
     */
    public function store(Request $request)
    {
        $request->validate([
            'audit_periode_id' => 'required|exists:audit_periodes,id',
            'ruangan_id' => 'required|exists:ruangans,id',
            'fasilitas_audit' => 'nullable|array',
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

        // Update or create audit entry for this ruangan in this periode
        AuditRuangan::updateOrCreate(
            [
                'audit_periode_id' => $request->audit_periode_id,
                'ruangan_id' => $request->ruangan_id,
            ],
            [
                'tahun_ajaran_id' => $tahunAjaranAktif->id,
                'teknisi_id' => auth()->id(),
                'fasilitas_audit' => $request->fasilitas_audit ?? [],
                'catatan' => $request->catatan,
                'tanggal_audit' => Carbon::now(),
            ]
        );

        // Update terakhir diperiksa di master Ruangan
        $ruangan = Ruangan::findOrFail($request->ruangan_id);
        $ruangan->update([
            'terakhir_diperiksa_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', 'Audit ruangan "' . $ruangan->nama_ruangan . '" berhasil disimpan.');
    }

    /**
     * Teknisi stores multiple audits at once (Bulk Audit).
     */
    public function bulkStore(Request $request)
    {
        $request->validate([
            'audit_periode_id' => 'required|exists:audit_periodes,id',
            'ruangan_ids' => 'required|array',
            'ruangan_ids.*' => 'exists:ruangans,id',
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

        // Eager load all selected ruangans in one query
        $ruangans = Ruangan::whereIn('id', $request->ruangan_ids)->get()->keyBy('id');

        foreach ($request->ruangan_ids as $ruanganId) {
            $ruangan = $ruangans->get($ruanganId);
            if (!$ruangan) continue;

            $fasilitasArray = array_filter(array_map('trim', explode(',', $ruangan->fasilitas ?? '')));
            $fasilitasAudit = [];
            foreach ($fasilitasArray as $item) {
                $fasilitasAudit[$item] = 'Baik';
            }

            // Update or create audit entry
            AuditRuangan::updateOrCreate(
                [
                    'audit_periode_id' => $periode->id,
                    'ruangan_id' => $ruanganId,
                ],
                [
                    'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    'teknisi_id' => $teknisiId,
                    'fasilitas_audit' => $fasilitasAudit,
                    'catatan' => null,
                    'tanggal_audit' => $now,
                ]
            );
        }

        // Batch update master in a single query
        Ruangan::whereIn('id', $request->ruangan_ids)->update([
            'terakhir_diperiksa_at' => $now,
        ]);

        return redirect()->back()->with('success', count($request->ruangan_ids) . ' ruangan berhasil diaudit secara massal dengan kondisi Baik.');
    }
}
