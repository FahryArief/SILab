# SILab

**SILab** adalah aplikasi web untuk pengelolaan inventaris laboratorium, peminjaman barang, booking ruangan, jadwal kuliah, audit inventaris, dan pelaporan operasional.

Aplikasi ini dibangun untuk membantu laboratorium menjaga data aset tetap terpusat, memperjelas alur persetujuan, dan menyediakan informasi inventaris yang mudah ditelusuri oleh setiap peran pengguna.

## Fitur utama

- **Manajemen inventaris**
  - Kategori, barang fisik, barcode, kondisi, kepemilikan, harga, ruangan, dan foto.
  - Import dan export data inventaris.
  - Cetak barcode barang dan QR code ruangan.
- **Peminjaman barang**
  - Pengajuan peminjaman satu atau beberapa barang.
  - Validasi teknisi, persetujuan kepala laboratorium, penolakan, dan pengembalian.
  - Riwayat peminjaman dan lampiran surat.
- **Booking ruangan**
  - Pengajuan dan pengelolaan booking ruangan.
  - Pemeriksaan bentrok jadwal.
  - Status pending, disetujui, ditolak, dan selesai.
- **Jadwal kuliah**
  - Pengelolaan jadwal berdasarkan ruangan dan tahun ajaran.
  - Pemeriksaan konflik waktu.
  - Import dan export jadwal.
- **Audit inventaris**
  - Audit barang dan fasilitas ruangan.
  - Periode audit, pelaporan teknisi, validasi kepala laboratorium, dan revisi.
- **Laporan**
  - Laporan barang, ruangan, peminjaman, dan audit dalam format siap cetak.
- **Akses berbasis peran**
  - Dashboard dan izin berbeda untuk administrator, teknisi, kepala laboratorium, koordinator prodi, dan peminjam.
- **Pemindaian publik**
  - Pencarian detail barang melalui barcode.
  - Pencarian detail ruangan melalui kode QR.

## Peran pengguna

| Peran | Tanggung jawab utama |
| --- | --- |
| `super_admin` | Mengelola pengguna dan seluruh konfigurasi aplikasi. |
| `teknisi` | Mengelola inventaris, booking, peminjaman, jadwal, dan pelaksanaan audit. |
| `kepala_lab` | Meninjau dashboard, memberi persetujuan akhir peminjaman, dan memvalidasi audit. |
| `ka_prodi` | Melihat informasi akademik, jadwal, dan laporan sesuai hak akses. |
| `peminjam` | Melihat katalog, mengajukan peminjaman barang, dan booking ruangan. |

## Teknologi

- PHP 8.2+
- Laravel 12
- Laravel Breeze
- SQLite atau MySQL
- Tailwind CSS
- Alpine.js
- Vite
- Laravel Excel
- DomPDF
- Simple Qrcode
- Milon Barcode

## Persyaratan

Pastikan perangkat pengembangan telah memiliki:

- PHP `8.2` atau lebih baru beserta ekstensi Laravel yang diperlukan.
- Composer.
- Node.js dan npm.
- Database SQLite atau MySQL.
- Git.

## Instalasi lokal

### 1. Clone repository

```bash
git clone https://github.com/FahryArief/SILab.git
cd SILab
```

### 2. Pasang dependency backend

```bash
composer install
```

### 3. Siapkan environment

```bash
cp .env.example .env
php artisan key:generate
```

Pada Windows PowerShell, gunakan:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### 4. Konfigurasikan database

#### SQLite

Buat file database jika belum tersedia:

```bash
touch database/database.sqlite
```

Lalu atur `.env`:

```dotenv
DB_CONNECTION=sqlite
```

Pada Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

#### MySQL

Atur kredensial database pada `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=silab
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Jalankan migration dan seeder

```bash
php artisan migrate --seed
```

Seeder hanya ditujukan untuk lingkungan pengembangan. Ganti password akun seed sebelum aplikasi digunakan di lingkungan selain lokal.

### 6. Siapkan storage dan frontend

```bash
php artisan storage:link
npm install
npm run build
```

### 7. Jalankan aplikasi

Untuk server Laravel saja:

```bash
php artisan serve
```

Untuk menjalankan server, queue listener, log viewer, dan Vite secara bersamaan:

```bash
composer run dev
```

Aplikasi tersedia di [http://localhost:8000](http://localhost:8000).

## Perintah pengembangan

```bash
# Menjalankan test
php artisan test

# Menjalankan test melalui script Composer
composer run test

# Memeriksa dan memperbaiki format PHP
vendor/bin/pint

# Membersihkan cache konfigurasi, route, dan view
php artisan optimize:clear

# Melihat daftar route
php artisan route:list

# Menjalankan queue worker
php artisan queue:listen --tries=1
```

## Struktur direktori penting

```text
app/
├── Http/Controllers/       Controller aplikasi dan autentikasi
├── Http/Requests/          Validasi request berbasis Form Request
├── Models/                 Model Eloquent
├── Policies/               Authorization berbasis objek
└── Providers/              Registrasi provider aplikasi

database/
├── migrations/             Struktur dan perubahan database
├── seeders/                Data awal pengembangan
└── factories/              Factory untuk kebutuhan testing

resources/
├── views/                  Blade view berdasarkan modul dan peran
├── css/                    Style aplikasi
└── js/                     Asset JavaScript dan Alpine.js

routes/
├── web.php                 Route aplikasi web
└── auth.php                Route autentikasi

tests/
├── Feature/                Pengujian alur aplikasi
└── Unit/                   Pengujian unit
```

## Alur persetujuan

### Peminjaman barang

```text
Peminjam mengajukan
        ↓
Teknisi memvalidasi
        ↓
Kepala lab menyetujui
        ↓
Barang dikembalikan
```

Pengajuan dapat berakhir sebagai `ditolak`. Status barang harus dikembalikan ke `Tersedia` hanya melalui alur bisnis yang valid.

### Audit inventaris

```text
Periode dibuka
        ↓
Teknisi melakukan audit
        ↓
Teknisi melaporkan
        ↓
Kepala lab menyetujui atau meminta revisi
```

## Konfigurasi file

File surat peminjaman dan dokumen lain yang bersifat sensitif sebaiknya disimpan pada disk private dan diakses melalui endpoint yang memeriksa authorization. Jangan menaruh kredensial produksi atau `APP_KEY` di repository.

Untuk lingkungan produksi, pastikan:

```dotenv
APP_ENV=production
APP_DEBUG=false
```

Selain itu, gunakan HTTPS, password database yang kuat, backup database terjadwal, worker queue aktif, dan konfigurasi cookie/session yang aman.

## Pengembangan dan kontribusi

1. Buat branch baru dari `main`.
2. Buat perubahan yang terfokus pada satu tujuan.
3. Jalankan test dan formatter sebelum membuat pull request.
4. Jelaskan perubahan database, konfigurasi, atau migration pada deskripsi pull request.
5. Jangan commit file `.env`, kredensial, database lokal, atau dokumen upload.

## Lisensi

Lisensi proyek belum ditetapkan secara eksplisit. Tambahkan file `LICENSE` sebelum proyek didistribusikan secara publik atau digunakan di luar lingkungan internal.
