<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\User;
use Illuminate\Support\Facades\DB;


class PeminjamanController extends Controller
{
    public function index()
    {
        // Ambil semua data peminjaman terbaru beserta relasi barangs
        $peminjamans = Peminjaman::with(['user:id,name,email', 'barangs:id,nama_barang,barcode'])->latest()->paginate(20);

        // Ambil data barang yang tersedia untuk form pilihan
        $barangs = Barang::where('status_peminjaman', 'Tersedia')->get();

        // Ambil data mahasiswa
        $users = User::where('role', 'peminjam')->get();

        return view('operator.peminjaman.index', compact('peminjamans', 'barangs', 'users'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable',
            'nama_peminjam' => 'required_without:user_id',
            'barang_ids' => 'required|array|min:1',
            'barang_ids.*' => 'exists:barangs,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali' => 'required|date|after_or_equal:tanggal_pinjam',
            'keperluan' => 'required|string|max:255',
            'surat_peminjaman' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $unavailableCount = Barang::whereIn('id', $request->barang_ids)
            ->where(function($q) {
                $q->where('status_peminjaman', '!=', 'Tersedia')
                  ->orWhere('kondisi', '!=', 'Baik');
            })
            ->count();

        if ($unavailableCount > 0) {
            return redirect()->back()->with('error', 'Beberapa barang sudah tidak tersedia atau dalam kondisi rusak!');
        }

        $nama_surat = null;
        if ($request->hasFile('surat_peminjaman')) {
            $file = $request->file('surat_peminjaman');
            $nama_surat = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('surat_peminjaman', $nama_surat, 'public');
        }

        DB::transaction(function() use ($request, $nama_surat) {
            // Simpan data peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => $request->user_id,
                'nama_peminjam' => $request->nama_peminjam,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'keperluan' => $request->keperluan,
                'surat_peminjaman' => $nama_surat,
                'status' => 'disetujui'
            ]);

            // Attach barangs & update status
            $peminjaman->barangs()->attach($request->barang_ids);
            Barang::whereIn('id', $request->barang_ids)->update(['status_peminjaman' => 'Dipinjam']);
        });

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman manual berhasil dicatat!');
    }

    // Validasi oleh Teknisi
    public function approve(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status !== 'pending') {
            return redirect()->back()->with('error', 'Aksi ini tidak dapat dilakukan. Status saat ini: ' . $peminjaman->status);
        }

        $peminjaman->update([
            'status' => 'divalidasi_teknisi',
            'catatan_admin' => $request->catatan
        ]);

        return redirect()->back()->with('success', 'Peminjaman telah divalidasi oleh Teknisi. Menunggu ACC Kepala Lab.');
    }

    // ACC Akhir oleh Kepala Lab
    public function accKepalaLab(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status !== 'divalidasi_teknisi') {
            return redirect()->back()->with('error', 'Belum divalidasi oleh Teknisi.');
        }

        $peminjaman->update([
            'status' => 'disetujui',
            'catatan_admin' => $request->catatan
        ]);

        return redirect()->back()->with('success', 'Peminjaman telah disetujui (ACC) oleh Kepala Lab.');
    }

    public function reject(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        if (!in_array($peminjaman->status, ['pending', 'divalidasi_teknisi'])) {
            return redirect()->back()->with('error', 'Aksi ini tidak dapat dilakukan. Status saat ini: ' . $peminjaman->status);
        }

        DB::transaction(function() use ($peminjaman, $request) {
            $peminjaman->update([
                'status' => 'ditolak',
                'catatan_admin' => $request->catatan
            ]);
            
            // Kembalikan barang menjadi Tersedia
            $barangIds = $peminjaman->barangs()->pluck('barangs.id');
            Barang::whereIn('id', $barangIds)->update(['status_peminjaman' => 'Tersedia']);
        });

        return redirect()->back()->with('success', 'Peminjaman telah ditolak.');
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);

        if ($peminjaman->status === 'dikembalikan') {
            return redirect()->back()->with('error', 'Barang sudah dikembalikan sebelumnya!');
        }

        DB::transaction(function() use ($peminjaman) {
            $peminjaman->update(['status' => 'dikembalikan']);

            // Kembalikan fisik barang menjadi tersedia
            $barangIds = $peminjaman->barangs()->pluck('barangs.id');
            Barang::whereIn('id', $barangIds)->update(['status_peminjaman' => 'Tersedia']);
        });

        return redirect()->back()->with('success', 'Barang fisik berhasil dikembalikan ke lab!');
    }
}
