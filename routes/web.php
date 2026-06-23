<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\OperatorDashboardController;
use App\Http\Controllers\BookingRuanganController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});

// PENGATUR LALU LINTAS SETELAH LOGIN
Route::get('/dashboard', function () {
    $role = auth()->user()->role;

    if ($role === 'super_admin') {
        return redirect('/admin/dashboard');
    } elseif ($role === 'teknisi') {
        return redirect('/operator/dashboard');
    } elseif ($role === 'ka_prodi') {
        return redirect('/prodi/dashboard');
    } elseif ($role === 'kepala_lab') {
        return redirect('/kepala-lab/dashboard');
    } else {
        return redirect('/peminjam/dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');


// GRUP ROUTE SUPER ADMIN
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Manajemen User
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::patch('/admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.role');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});

// GRUP ROUTE TEKNISI (juga bisa diakses oleh super_admin)
Route::middleware(['auth', 'role:teknisi,super_admin'])->group(function () {
Route::get('/operator/barang/{id}/barcode', function($id) {
    $barang = \App\Models\Barang::with(['kategori', 'ruangan'])->findOrFail($id);
    return view('operator.barang.barcode', compact('barang'));
})->name('barang.barcode');

Route::get('/operator/barang-batch/barcode', function(\Illuminate\Http\Request $request) {
    if ($request->has('ids')) {
        $ids = explode(',', $request->query('ids'));
        $barangs = \App\Models\Barang::whereIn('id', $ids)->with('kategori')->get();
        $nama_barang = $barangs->first()->nama_barang ?? 'Selected Items';
    } else {
        $nama_barang = $request->query('nama_barang');
        $barangs = \App\Models\Barang::where('nama_barang', $nama_barang)->with('kategori')->get();
    }
    return view('operator.barang.batch_barcode', compact('barangs', 'nama_barang'));
})->name('barang.batch-barcode');
    // Ubah rute ini
    Route::get('/operator/dashboard', [OperatorDashboardController::class, 'index'])->name('operator.dashboard');

    // Booking Ruangan
    Route::get('/operator/booking', [BookingRuanganController::class, 'index'])->name('booking.index');
    Route::post('/operator/booking', [BookingRuanganController::class, 'store'])->name('booking.store');
    Route::patch('/operator/booking/{id}/selesai', [BookingRuanganController::class, 'markAsDone'])->name('booking.selesai');
    Route::patch('/operator/booking/{id}/approve', [BookingRuanganController::class, 'approve'])->name('booking.approve');
    Route::patch('/operator/booking/{id}/reject', [BookingRuanganController::class, 'reject'])->name('booking.reject');

    // ... (rute resource kategori, ruangan, barang biarkan di bawahnya) ...
    Route::resource('operator/kategori', KategoriController::class);
    Route::resource('operator/ruangan', RuanganController::class);
    Route::resource('operator/barang', BarangController::class);
    Route::patch('/operator/barang/{id}/inline', [BarangController::class, 'inlineUpdate'])->name('barang.inline-update');

    // Export/Import Barang & Ruangan
    Route::get('/operator/barang-export', [BarangController::class, 'export'])->name('barang.export');
    Route::post('/operator/barang-import', [BarangController::class, 'import'])->name('barang.import');
    Route::get('/operator/ruangan-export', [RuanganController::class, 'export'])->name('ruangan.export');
    Route::post('/operator/ruangan-import', [RuanganController::class, 'import'])->name('ruangan.import');

    // Cetak QR Code Ruangan
    Route::get('/operator/ruangan/{id}/qrcode', function($id) {
        $ruangan = \App\Models\Ruangan::findOrFail($id);
        return view('operator.ruangan.qrcode', compact('ruangan'));
    })->name('ruangan.qrcode');

});

// GRUP ROUTE PRODI
Route::middleware(['auth', 'role:ka_prodi,super_admin'])->group(function () {
    Route::get('/prodi/dashboard', [\App\Http\Controllers\ProdiDashboardController::class, 'index'])->name('prodi.dashboard');
});

// GRUP ROUTE PEMINJAMAN BERSAMA (Teknisi & Kepala Lab)
Route::middleware(['auth', 'role:teknisi,kepala_lab,super_admin'])->group(function () {
    Route::get('/operator/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::post('/operator/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::patch('/operator/peminjaman/{id}/approve', [PeminjamanController::class, 'approve'])->name('peminjaman.approve');
    Route::patch('/operator/peminjaman/{id}/reject', [PeminjamanController::class, 'reject'])->name('peminjaman.reject');
    Route::patch('/operator/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
});

// GRUP ROUTE KEPALA LAB
Route::middleware(['auth', 'role:kepala_lab,super_admin'])->group(function () {
    Route::get('/kepala-lab/dashboard', [\App\Http\Controllers\KepalaLabDashboardController::class, 'index'])->name('kepala_lab.dashboard');
    Route::patch('/kepala-lab/peminjaman/{id}/acc', [\App\Http\Controllers\PeminjamanController::class, 'accKepalaLab'])->name('kepala_lab.peminjaman.acc');

    // Kepala Lab bisa lihat data barang, ruangan, booking
    Route::get('/kepala-lab/barang', [BarangController::class, 'index'])->name('kepala_lab.barang.index');
    Route::get('/kepala-lab/ruangan', [RuanganController::class, 'index'])->name('kepala_lab.ruangan.index');
    Route::get('/kepala-lab/booking', [BookingRuanganController::class, 'index'])->name('kepala_lab.booking.index');
    Route::post('/operator/booking', [BookingRuanganController::class, 'store'])->name('booking.store');

});

// ROUTE MASTER TAHUN AJARAN (Diakses oleh Super Admin, Kepala Lab, Ka Prodi)
Route::middleware(['auth', 'role:super_admin,kepala_lab,ka_prodi'])->group(function () {
    Route::get('/admin/tahun-ajaran', [\App\Http\Controllers\TahunAjaranController::class, 'index'])->name('admin.tahun_ajaran.index');
    Route::post('/admin/tahun-ajaran', [\App\Http\Controllers\TahunAjaranController::class, 'store'])->name('admin.tahun_ajaran.store');
    Route::put('/admin/tahun-ajaran/{id}', [\App\Http\Controllers\TahunAjaranController::class, 'update'])->name('admin.tahun_ajaran.update');
    Route::delete('/admin/tahun-ajaran/{id}', [\App\Http\Controllers\TahunAjaranController::class, 'destroy'])->name('admin.tahun_ajaran.destroy');
    Route::patch('/admin/tahun-ajaran/{id}/activate', [\App\Http\Controllers\TahunAjaranController::class, 'activate'])->name('admin.tahun_ajaran.activate');
});

// ROUTE JADWAL KULIAH (Diakses oleh Teknisi, Kepala Lab, Ka Prodi, Super Admin)
Route::middleware(['auth', 'role:teknisi,kepala_lab,ka_prodi,super_admin'])->group(function () {
    Route::get('/admin/jadwal-kuliah', [\App\Http\Controllers\JadwalKuliahController::class, 'index'])->name('admin.jadwal_kuliah.index');
    Route::get('/admin/jadwal-kuliah/ruangan/{id}', [\App\Http\Controllers\JadwalKuliahController::class, 'showByRuangan'])->name('admin.jadwal_kuliah.ruangan');
    Route::post('/admin/jadwal-kuliah', [\App\Http\Controllers\JadwalKuliahController::class, 'store'])->name('admin.jadwal_kuliah.store');
    Route::put('/admin/jadwal-kuliah/{id}', [\App\Http\Controllers\JadwalKuliahController::class, 'update'])->name('admin.jadwal_kuliah.update');
    Route::delete('/admin/jadwal-kuliah/{id}', [\App\Http\Controllers\JadwalKuliahController::class, 'destroy'])->name('admin.jadwal_kuliah.destroy');

    // Export/Import Jadwal
    Route::get('/admin/jadwal-kuliah-export', [\App\Http\Controllers\JadwalKuliahController::class, 'export'])->name('admin.jadwal_kuliah.export');
    Route::post('/admin/jadwal-kuliah-import', [\App\Http\Controllers\JadwalKuliahController::class, 'import'])->name('admin.jadwal_kuliah.import');
});

// ROUTE AUDIT INVENTARIS — Periode Based
use App\Http\Controllers\AuditPeriodeController;

// Shared: Teknisi + Kepala Lab + Super Admin bisa lihat daftar & detail periode
Route::middleware(['auth', 'role:teknisi,kepala_lab,super_admin'])->group(function () {
    Route::get('/admin/audit/periode', [AuditPeriodeController::class, 'index'])->name('admin.audit.periode.index');
    Route::get('/admin/audit/periode/{id}', [AuditPeriodeController::class, 'show'])->name('admin.audit.periode.show');
    Route::get('/admin/audit/periode/{id}/barang', [AuditPeriodeController::class, 'showBarang'])->name('admin.audit.periode.barang');
    Route::get('/admin/audit/periode/{id}/ruangan', [AuditPeriodeController::class, 'showRuangan'])->name('admin.audit.periode.ruangan');

    // Legacy redirect (agar sidebar lama tetap berfungsi)
    Route::get('/admin/audit/barang', [\App\Http\Controllers\AuditBarangController::class, 'index'])->name('admin.audit.barang.index');
    Route::get('/admin/audit/ruangan', [\App\Http\Controllers\AuditRuanganController::class, 'index'])->name('admin.audit.ruangan.index');

    // Store audit per item & bulk
    Route::post('/admin/audit/barang', [\App\Http\Controllers\AuditBarangController::class, 'store'])->name('admin.audit.barang.store');
    Route::post('/admin/audit/barang/bulk', [\App\Http\Controllers\AuditBarangController::class, 'bulkStore'])->name('admin.audit.barang.bulkStore');
    
    Route::post('/admin/audit/ruangan', [\App\Http\Controllers\AuditRuanganController::class, 'store'])->name('admin.audit.ruangan.store');
    Route::post('/admin/audit/ruangan/bulk', [\App\Http\Controllers\AuditRuanganController::class, 'bulkStore'])->name('admin.audit.ruangan.bulkStore');
});

// Kepala Lab only: buat & tutup periode
Route::middleware(['auth', 'role:kepala_lab,super_admin'])->group(function () {
    Route::post('/admin/audit/periode', [AuditPeriodeController::class, 'store'])->name('admin.audit.periode.store');
    Route::patch('/admin/audit/periode/{id}/validate', [AuditPeriodeController::class, 'validateAudit'])->name('admin.audit.periode.validate');
});

// Teknisi only: laporkan hasil audit
Route::middleware(['auth', 'role:teknisi,super_admin'])->group(function () {
    Route::patch('/admin/audit/periode/{id}/laporkan', [AuditPeriodeController::class, 'laporkan'])->name('admin.audit.periode.laporkan');
});


// ROUTE LAPORAN (Diakses oleh Teknisi, Kepala Lab, Ka Prodi, Super Admin)
Route::middleware(['auth', 'role:teknisi,kepala_lab,ka_prodi,super_admin'])->group(function () {
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/peminjaman', [\App\Http\Controllers\LaporanController::class, 'cetakPeminjaman'])->name('laporan.peminjaman');
    Route::get('/laporan/barang', [\App\Http\Controllers\LaporanController::class, 'cetakBarang'])->name('laporan.barang');
    Route::get('/laporan/ruangan', [\App\Http\Controllers\LaporanController::class, 'cetakRuangan'])->name('laporan.ruangan');
    Route::post('/laporan/audit', [\App\Http\Controllers\LaporanController::class, 'cetakAudit'])->name('laporan.audit');
});

// GRUP ROUTE PEMINJAM
Route::middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/peminjam/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::get('/user/dashboard', [UserDashboardController::class, 'index']);
    Route::get('/peminjam/riwayat', [UserDashboardController::class, 'riwayat'])->name('peminjam.riwayat');

    // RUTE KATALOG
    Route::get('/peminjam/katalog/barang', [KatalogController::class, 'barang'])->name('peminjam.katalog.barang');
    Route::get('/peminjam/katalog/ruangan', [KatalogController::class, 'ruangan'])->name('peminjam.katalog.ruangan');
    // TAMBAHKAN BARIS INI (Rute untuk memproses form peminjaman)
    Route::post('/peminjam/katalog/barang/pinjam', [KatalogController::class, 'storeBarang'])->name('peminjam.katalog.barang.store');
    // Rute Katalog Ruangan yang sudah ada sebelumnya
    Route::get('/peminjam/katalog/ruangan', [KatalogController::class, 'ruangan'])->name('peminjam.katalog.ruangan');
    Route::get('/peminjam/katalog/ruangan/{id}/jadwal', [KatalogController::class, 'jadwalRuangan'])->name('peminjam.katalog.ruangan.jadwal');

    // TAMBAHKAN BARIS INI (Rute untuk memproses form booking ruangan)
    Route::post('/peminjam/katalog/ruangan/booking', [KatalogController::class, 'storeRuangan'])->name('peminjam.katalog.ruangan.store');
    });
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ROUTE PUBLIC: Scan QR Code Ruangan (tanpa auth)
Route::get('/scan-ruangan/{kode}', [\App\Http\Controllers\ScanRuanganController::class, 'show'])->name('scan.ruangan');

// ROUTE PUBLIC: Scan QR Code Barang (tanpa auth)
Route::get('/scan-barang/{barcode}', [\App\Http\Controllers\ScanBarangController::class, 'show'])->name('scan.barang');

require __DIR__.'/auth.php';