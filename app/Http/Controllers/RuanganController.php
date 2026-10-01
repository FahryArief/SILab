<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreRuanganRequest;
use App\Http\Requests\UpdateRuanganRequest;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Exports\RuanganExport;
use App\Imports\RuanganImport;
use Maatwebsite\Excel\Facades\Excel;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::select([
            'id', 'nama_ruangan', 'kode_ruangan', 'kapasitas', 'lokasi',
            'keterangan', 'fasilitas', 'foto_ruangan', 'terakhir_diperiksa_at',
        ])->latest()->paginate(18)->withQueryString();

        // Arsip: ruangan yang sudah dinonaktifkan (soft deleted), tetap bisa
        // dilihat & diaktifkan kembali di sini. Tidak ikut tampil di listing
        // utama ataupun di dropdown pemilihan ruangan manapun.
        $ruangansArsip = Ruangan::onlyTrashed()->orderByDesc('deleted_at')->get();

        return view('operator.ruangan.index', compact('ruangans', 'ruangansArsip'));
    }

    public function create()
{
    return view('operator.ruangan.create');
}
    public function store(StoreRuanganRequest $request)
    {
        DB::transaction(function() use ($request) {
            // Logika menyimpan foto
            $nama_foto = null;
            if ($request->hasFile('foto_ruangan')) {
                $foto = $request->file('foto_ruangan');
                $nama_foto = Str::uuid() . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('foto_ruangan', $nama_foto, 'public');
            }

            $ruangan = Ruangan::create([
                'nama_ruangan' => $request->nama_ruangan,
                'kapasitas'    => $request->kapasitas,
                'lokasi'       => $request->lokasi,
                'keterangan'   => $request->keterangan,
                'fasilitas'    => $request->fasilitas,
                'foto_ruangan' => $nama_foto
            ]);

            $kode = 'RM-' . strtoupper(str_replace(' ', '', substr($ruangan->nama_ruangan, 0, 8))) . '-' . str_pad($ruangan->id, 3, '0', STR_PAD_LEFT);
            $ruangan->update(['kode_ruangan' => $kode]);
        });

        return redirect()->back()->with('success', 'Ruangan baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('operator.ruangan.edit', compact('ruangan'));
    }

    public function update(UpdateRuanganRequest $request, $id)
    {
        $ruangan = Ruangan::findOrFail($id);
        
        DB::transaction(function() use ($request, $ruangan) {
            $nama_foto = $ruangan->foto_ruangan; // Simpan nama foto lama sementara

            // Jika ada foto baru yang diupload
            if ($request->hasFile('foto_ruangan')) {
                // Hapus foto lama jika ada
                if ($nama_foto && Storage::disk('public')->exists('foto_ruangan/' . $nama_foto)) {
                    Storage::disk('public')->delete('foto_ruangan/' . $nama_foto);
                }

                // Simpan foto baru dengan UUID
                $foto = $request->file('foto_ruangan');
                $nama_foto = Str::uuid() . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('foto_ruangan', $nama_foto, 'public');
            }

            $ruangan->update([
                'nama_ruangan' => $request->nama_ruangan,
                'kapasitas'    => $request->kapasitas,
                'lokasi'       => $request->lokasi,
                'keterangan'   => $request->keterangan,
                'fasilitas'    => $request->fasilitas,
                'foto_ruangan' => $nama_foto,
                'jenis_ruangan' => $request->jenis_ruangan ?: $ruangan->jenis_ruangan,
                'lab_nonaktif_sejak' => $request->lab_nonaktif_sejak,
            ]);
        });

        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diperbarui!');
    }

    /**
     * Nonaktifkan ruangan (soft delete).
     *
     * Baris datanya TIDAK dihapus dari database — hanya ditandai nonaktif
     * (deleted_at diisi) supaya hilang dari halaman Data Ruangan & dropdown
     * pemilihan ruangan, tapi tetap muncul di laporan historis kalau
     * difilter ke tahun saat ruangan itu masih aktif. Foto juga tidak
     * dihapus, sama alasannya (dipakai untuk laporan lama).
     */
    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        $ruangan->delete();
        return redirect()->back()->with('success', 'Ruangan dinonaktifkan. Data tetap tersimpan dan akan tetap muncul di laporan tahun-tahun sebelumnya.');
    }

    /**
     * Aktifkan kembali ruangan yang sebelumnya dinonaktifkan.
     */
    public function restore($id)
    {
        $ruangan = Ruangan::onlyTrashed()->findOrFail($id);
        $ruangan->restore();

        return redirect()->back()->with('success', 'Ruangan "' . $ruangan->nama_ruangan . '" berhasil diaktifkan kembali.');
    }

    public function export()
    {
        return Excel::download(new RuanganExport, 'Data_Ruangan_'.date('Ymd_His').'.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new RuanganImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data Ruangan berhasil diimport!');
    }
}
