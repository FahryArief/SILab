<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Http\Requests\StorePeminjamanRequest;
use App\Models\Peminjaman;
use App\Models\Barang;
use App\Models\User;
use App\Services\PeminjamanStatusService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PeminjamanController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $this->authorize('viewAny', Peminjaman::class);

        $search = trim((string) $request->input('search'));
        $peminjamans = Peminjaman::with(['user:id,name,email', 'barangs:id,nama_barang,barcode'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('nama_peminjam', 'like', "%{$search}%")
                        ->orWhere('keperluan', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('barangs', function ($barangQuery) use ($search) {
                            $barangQuery->where('nama_barang', 'like', "%{$search}%")
                                ->orWhere('barcode', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Ambil data barang yang tersedia untuk form pilihan
        $barangs = Barang::select(['id', 'nama_barang', 'barcode', 'kondisi'])
            ->where('status_peminjaman', 'Tersedia')->get();

        // Ambil data mahasiswa
        $users = User::select(['id', 'name', 'email'])
            ->where('role', 'peminjam')->get();

        return view('operator.peminjaman.index', compact('peminjamans', 'barangs', 'users', 'search'));
    }

    public function store(StorePeminjamanRequest $request)
    {
        return DB::transaction(function() use ($request) {
            // Cek ketersediaan barang dengan lockForUpdate untuk mencegah race condition
            $barangs = Barang::whereIn('id', $request->barang_ids)
                ->lockForUpdate()->get();

            if ($barangs->count() !== count($request->barang_ids)
                || $barangs->contains(fn (Barang $barang) => $barang->status_peminjaman !== 'Tersedia'
                    || $barang->kondisi !== 'Baik')) {
                return redirect()->back()->with('error', 'Satu atau lebih barang yang dipilih tidak tersedia untuk dipinjam.');
            }

            // Upload surat with hashed name to private disk
            $suratName = null;
            if ($request->hasFile('surat_peminjaman')) {
                $file = $request->file('surat_peminjaman');
                $suratName = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('surat_peminjaman', $suratName, 'local');
            }

            // Tentukan user_id, jika null berarti peminjaman manual/non-mahasiswa yang tidak punya akun
            $user_id = $request->user_id;

            // Simpan Peminjaman
            $peminjaman = Peminjaman::create([
                'user_id' => $user_id,
                'nama_peminjam' => $request->nama_peminjam,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_kembali' => $request->tanggal_kembali,
                'keperluan' => $request->keperluan,
                'surat_peminjaman' => $suratName,
                'status' => 'pending'
            ]);

            // Sync pivot table (otomatis handle unique insert)
            $peminjaman->barangs()->attach($barangs->mapWithKeys(fn (Barang $barang) => [
                $barang->id => [
                    'nama_barang_snapshot' => $barang->nama_barang,
                    'barcode_snapshot' => $barang->barcode,
                    'kondisi_snapshot' => $barang->kondisi,
                ],
            ])->all());

            // Karena sistem peminjaman multi-step (ACC dll), status barang tidak langsung diubah ke 'Dipinjam'
            // sampai disetujui Kepala Lab. Atau jika logic berubah, ubah disini.

            return redirect()->back()->with('success', 'Pengajuan peminjaman berhasil dibuat dan menunggu divalidasi Teknisi.');
        });
    }

    // Validasi oleh Teknisi
    public function approve(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $this->authorize('approve', $peminjaman);

        if ($peminjaman->status !== 'pending') {
            return redirect()->back()->with('error', 'Aksi ini tidak dapat dilakukan. Status saat ini: ' . $peminjaman->status);
        }

        DB::transaction(function () use ($peminjaman) {
            $locked = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            app(PeminjamanStatusService::class)->transition($locked, 'divalidasi_teknisi');
        });

        Log::info('Peminjaman divalidasi oleh Teknisi', [
            'peminjaman_id' => $peminjaman->id,
            'teknisi_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Peminjaman berhasil divalidasi dan menunggu ACC Kepala Lab.');
    }

    // ACC Akhir oleh Kepala Lab
    public function accKepalaLab(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $this->authorize('accKepalaLab', $peminjaman);

        if ($peminjaman->status !== 'divalidasi_teknisi') {
            return redirect()->back()->with('error', 'Belum divalidasi oleh Teknisi.');
        }

        DB::transaction(function () use ($peminjaman) {
            $locked = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            app(PeminjamanStatusService::class)->transition($locked, 'disetujui');
            Barang::whereIn('id', $locked->barangs()->pluck('barangs.id'))
                ->lockForUpdate()->get()->each->update(['status_peminjaman' => 'Dipinjam']);
        });

        Log::info('Peminjaman di-ACC oleh Kepala Lab', [
            'peminjaman_id' => $peminjaman->id,
            'kepala_lab_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Peminjaman berhasil disetujui (ACC Kepala Lab).');
    }

    public function reject(Request $request, $id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $this->authorize('reject', $peminjaman);

        if (!in_array($peminjaman->status, ['pending', 'divalidasi_teknisi'])) {
            return redirect()->back()->with('error', 'Aksi ini tidak dapat dilakukan. Status saat ini: ' . $peminjaman->status);
        }

        DB::transaction(function() use ($peminjaman, $request) {
            $locked = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            app(PeminjamanStatusService::class)->transition($locked, 'ditolak');

            // Kembalikan barang menjadi Tersedia
            $barangIds = $locked->barangs()->pluck('barangs.id');
            Barang::whereIn('id', $barangIds)->lockForUpdate()->get()
                ->each->update(['status_peminjaman' => 'Tersedia']);
        });

        Log::info('Peminjaman ditolak', [
            'peminjaman_id' => $peminjaman->id,
            'user_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Peminjaman berhasil ditolak.');
    }

    public function kembalikan($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $this->authorize('kembalikan', $peminjaman);

        // Only allow return from 'disetujui' status
        if ($peminjaman->status !== 'disetujui') {
            return redirect()->back()->with('error', 'Hanya peminjaman yang sedang berjalan (disetujui) yang bisa dikembalikan!');
        }

        DB::transaction(function() use ($peminjaman) {
            $locked = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();
            $hariTerlambat = today()->gt($locked->tanggal_kembali)
                ? $locked->tanggal_kembali->diffInDays(today())
                : 0;

            $locked->update([
                'dikembalikan_at' => now(),
                'hari_terlambat' => $hariTerlambat,
            ]);
            app(PeminjamanStatusService::class)->transition($locked, 'dikembalikan');

            // Update status barang kembali ke 'Tersedia'
            Barang::whereIn('id', $locked->barangs()->pluck('barangs.id'))
                ->lockForUpdate()->get()->each->update(['status_peminjaman' => 'Tersedia']);
        });

        Log::info('Peminjaman dikembalikan', [
            'peminjaman_id' => $peminjaman->id,
            'user_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Barang berhasil dikembalikan.');
    }

    /**
     * Download surat peminjaman (authorized endpoint).
     */
    public function downloadSurat($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $this->authorize('downloadSurat', $peminjaman);

        if (!$peminjaman->surat_peminjaman) {
            abort(404, 'Surat peminjaman tidak ditemukan.');
        }

        $path = 'surat_peminjaman/' . $peminjaman->surat_peminjaman;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'File surat peminjaman tidak ditemukan.');
        }

        return Storage::disk('local')->download($path);
    }
}
