<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use App\Models\Ruangan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Exports\JadwalKuliahExport;
use App\Imports\JadwalKuliahImport;
use Maatwebsite\Excel\Facades\Excel;

class JadwalKuliahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Hanya tampilkan tahun ajaran yang aktif secara default, atau ambil semua jika ingin filter
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $ruangans = Ruangan::all();
        
        $jadwals = JadwalKuliah::with(['ruangan', 'tahunAjaran'])->latest()->get();

        return view('admin.jadwal_kuliah.index', compact('jadwals', 'ruangans', 'tahunAjaranAktif'));
    }

    /**
     * Show jadwal for a specific ruangan
     */
    public function showByRuangan($id)
    {
        $ruangan = Ruangan::findOrFail($id);
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();

        $jadwals = JadwalKuliah::with(['ruangan', 'tahunAjaran'])
            ->where('ruangan_id', $id)
            ->when($tahunAjaranAktif, function ($query) use ($tahunAjaranAktif) {
                return $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
            })
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')")
            ->orderBy('waktu_mulai')
            ->get();

        $ruangans = Ruangan::all();

        return view('admin.jadwal_kuliah.ruangan', compact('ruangan', 'jadwals', 'ruangans', 'tahunAjaranAktif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'hari' => 'required|string',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'mata_kuliah' => 'required|string|max:255',
            'dosen' => 'nullable|string|max:255',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaranAktif) {
            return redirect()->back()->with('error', 'Tidak ada Tahun Ajaran yang aktif. Silakan set Tahun Ajaran terlebih dahulu.');
        }

        // Cek bentrok dengan jadwal kuliah lain di hari dan jam yang sama untuk ruangan ini
        $bentrok = JadwalKuliah::where('ruangan_id', $request->ruangan_id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->where('hari', $request->hari)
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                      });
            })->exists();

        if ($bentrok) {
            return redirect()->back()->with('error', 'Ruangan sudah terpakai untuk jadwal kuliah lain pada waktu tersebut!');
        }

        JadwalKuliah::create([
            'ruangan_id' => $request->ruangan_id,
            'tahun_ajaran_id' => $tahunAjaranAktif->id,
            'hari' => $request->hari,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'mata_kuliah' => $request->mata_kuliah,
            'dosen' => $request->dosen,
        ]);

        return redirect()->back()->with('success', 'Jadwal Kuliah berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'ruangan_id' => 'required|exists:ruangans,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'mata_kuliah' => 'required|string|max:255',
            'dosen' => 'nullable|string|max:255',
        ], [
            'waktu_selesai.after' => 'Waktu selesai harus lebih besar dari waktu mulai.',
        ]);

        $jadwal = JadwalKuliah::findOrFail($id);
        
        // Cek bentrok dengan jadwal kuliah lain (kecuali dirinya sendiri)
        $bentrok = JadwalKuliah::where('ruangan_id', $request->ruangan_id)
            ->where('tahun_ajaran_id', $jadwal->tahun_ajaran_id)
            ->where('hari', $request->hari)
            ->where('id', '!=', $id)
            ->where(function ($query) use ($request) {
                $query->whereBetween('waktu_mulai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhereBetween('waktu_selesai', [$request->waktu_mulai, $request->waktu_selesai])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('waktu_mulai', '<=', $request->waktu_mulai)
                            ->where('waktu_selesai', '>=', $request->waktu_selesai);
                      });
            })->exists();

        if ($bentrok) {
            return redirect()->back()->with('error', 'Ruangan sudah terpakai untuk jadwal kuliah lain pada waktu tersebut!');
        }

        $jadwal->update([
            'ruangan_id' => $request->ruangan_id,
            'hari' => $request->hari,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'mata_kuliah' => $request->mata_kuliah,
            'dosen' => $request->dosen,
        ]);

        return redirect()->back()->with('success', 'Jadwal Kuliah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);
        $jadwal->delete();

        return redirect()->back()->with('success', 'Jadwal berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new JadwalKuliahExport, 'Jadwal_Kuliah_'.date('Ymd_His').'.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        Excel::import(new JadwalKuliahImport, $request->file('file'));

        return redirect()->back()->with('success', 'Jadwal Kuliah berhasil diimport!');
    }
}
