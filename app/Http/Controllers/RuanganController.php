<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Storage; // <-- Tambahkan ini untuk kelola file
use App\Exports\RuanganExport;
use App\Imports\RuanganImport;
use Maatwebsite\Excel\Facades\Excel;

class RuanganController extends Controller
{
    public function index()
    {
        $ruangans = Ruangan::all();
        return view('operator.ruangan.index', compact('ruangans'));
    }

    public function create()
{
    return view('operator.ruangan.create');
}
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required',
            'foto_ruangan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048' // Maksimal 2MB
        ]);

        // Logika menyimpan foto
        $nama_foto = null;
        if ($request->hasFile('foto_ruangan')) {
            $foto = $request->file('foto_ruangan');
            $nama_foto = time() . '_' . $foto->getClientOriginalName();
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

        return redirect()->back()->with('success', 'Ruangan baru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        return view('operator.ruangan.edit', compact('ruangan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ruangan' => 'required',
            'foto_ruangan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $ruangan = Ruangan::findOrFail($id);
        $nama_foto = $ruangan->foto_ruangan; // Simpan nama foto lama sementara

        // Jika ada foto baru yang diupload
        if ($request->hasFile('foto_ruangan')) {
            // Hapus foto lama jika ada
            if ($nama_foto && Storage::disk('public')->exists('foto_ruangan/' . $nama_foto)) {
                Storage::disk('public')->delete('foto_ruangan/' . $nama_foto);
            }

            // Simpan foto baru
            $foto = $request->file('foto_ruangan');
            $nama_foto = time() . '_' . $foto->getClientOriginalName();
            $foto->storeAs('foto_ruangan', $nama_foto, 'public');
        }

        $ruangan->update([
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas'    => $request->kapasitas,
            'lokasi'       => $request->lokasi,
            'keterangan'   => $request->keterangan,
            'fasilitas'    => $request->fasilitas,
            'foto_ruangan' => $nama_foto
        ]);

        return redirect()->route('ruangan.index')->with('success', 'Data ruangan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ruangan = Ruangan::findOrFail($id);

        // Hapus file foto dari server jika ada
        if ($ruangan->foto_ruangan && Storage::disk('public')->exists('foto_ruangan/' . $ruangan->foto_ruangan)) {
            Storage::disk('public')->delete('foto_ruangan/' . $ruangan->foto_ruangan);
        }

        $ruangan->delete();
        return redirect()->back()->with('success', 'Ruangan berhasil dihapus!');
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
