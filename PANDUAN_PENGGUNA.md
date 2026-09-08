# Panduan Penggunaan SILab

**Sistem Informasi Laboratorium TRPL - Politeknik Negeri Lampung**

Dokumen ini menjelaskan penggunaan SILab untuk seluruh jenis pengguna: **Super Admin, Teknisi, Kepala Laboratorium, Ka. Prodi, dan Peminjam**.

## 1. Gambaran Umum

SILab digunakan untuk:

- Mengelola data barang inventaris, kategori, dan ruangan.
- Membuat dan memproses peminjaman alat.
- Membuat dan memproses booking ruangan.
- Mencatat audit inventaris barang dan ruangan.
- Mengelola jadwal kuliah dan tahun ajaran.
- Mencetak laporan inventaris, peminjaman, dan audit.
- Menyediakan katalog barang/ruangan serta pemindaian QR Code.
- Mengelola konten halaman depan sistem.

## 2. Persiapan dan Akses Sistem

1. Buka alamat aplikasi pada browser.
2. Pilih **Login**.
3. Masukkan email dan kata sandi.
4. Jika diminta, lakukan verifikasi email.
5. Setelah berhasil login, sistem mengarahkan pengguna ke dashboard sesuai peran.

### Halaman publik

Halaman depan dapat dilihat tanpa login. Pengunjung juga dapat membuka hasil pemindaian QR Code barang atau ruangan melalui tautan QR Code.

### Mengubah profil

Semua pengguna yang sudah login dapat membuka menu profil untuk:

- Mengubah nama dan informasi akun.
- Mengubah alamat email.
- Mengubah kata sandi.
- Menghapus akun sesuai kebijakan aplikasi.

## 3. Peran dan Hak Akses

| Peran | Fungsi utama |
|---|---|
| **Super Admin** | Mengelola pengguna, seluruh data, konfigurasi, proses operasional, dan akses administratif |
| **Teknisi** | Mengelola inventaris, booking, validasi peminjaman, audit, jadwal, dan laporan |
| **Kepala Laboratorium** | Menyetujui peminjaman, memantau operasional, membuat/menutup periode audit, dan melihat laporan |
| **Ka. Prodi** | Melihat dashboard prodi, mengelola tahun ajaran dan jadwal kuliah, serta melihat laporan |
| **Peminjam** | Melihat katalog, mengajukan peminjaman alat, booking ruangan, dan memantau riwayat |

Super Admin memiliki akses teknis yang setara dengan menu yang dapat diakses Teknisi dan Kepala Laboratorium, selain menu administrasi pengguna.

## 4. Panduan Super Admin

### 4.1 Dashboard

Dashboard menampilkan ringkasan sistem dan notifikasi pekerjaan yang perlu ditindaklanjuti. Periksa notifikasi sebelum melakukan pekerjaan lain.

### 4.2 Mengelola pengguna

1. Buka **Admin > Pengguna**.
2. Gunakan pencarian untuk menemukan pengguna.
3. Untuk menambah pengguna, isi nama, email, kata sandi, dan peran.
4. Untuk mengubah data, pilih tombol **Edit**.
5. Untuk mengubah hak akses, pilih pengaturan peran pengguna.
6. Untuk menghapus pengguna, pilih **Hapus** dan konfirmasi.

Peran harus diberikan sesuai tanggung jawab. Hindari memberikan peran Super Admin kepada pengguna yang tidak memerlukannya.

### 4.3 Menu operasional

Super Admin dapat mengikuti panduan Teknisi dan Kepala Laboratorium pada bagian berikutnya.

## 5. Panduan Teknisi

### 5.1 Mengelola kategori

1. Buka **Data Kategori**.
2. Pilih **Tambah**.
3. Isi nama kategori dan keterangan jika tersedia.
4. Simpan.
5. Gunakan menu edit atau hapus untuk memperbarui data.

Kategori digunakan saat menambahkan barang, sehingga sebaiknya dibuat sebelum input inventaris.

### 5.2 Mengelola ruangan

1. Buka **Data Ruangan**.
2. Pilih **Tambah Ruangan**.
3. Isi kode ruangan, nama ruangan, lokasi, kapasitas, fasilitas, dan keterangan.
4. Simpan.
5. Pastikan data ruangan benar karena ruangan dipakai untuk inventaris, booking, QR Code, dan laporan.

Kolom fasilitas dapat berisi daftar fasilitas yang tersedia. Gunakan format yang mudah dibaca, misalnya `PC, Proyektor, AC`.

### 5.3 Mengelola barang

1. Buka **Data Barang**.
2. Pilih **Tambah Barang**.
3. Isi nama barang, kategori, ruangan, merk, kondisi, kepemilikan, harga, deskripsi/catatan, dan data lain yang diminta.
4. Jika barang memiliki beberapa unit, tambahkan data setiap unit/sub-barang.
5. Unggah foto jika diperlukan.
6. Simpan.

#### Pencarian dan filter barang

- Gunakan kolom pencarian untuk mencari nama, barcode, atau informasi barang.
- Gunakan filter kategori, ruangan, kondisi, atau status bila tersedia.
- Gunakan tombol reset untuk menghapus filter.

#### Import dan export barang

1. Buka dropdown **Import / Export**.
2. Pilih **Export** untuk mengunduh data barang.
3. Pilih **Import** untuk mengunggah file sesuai format contoh.
4. Pastikan nama kolom file tidak diubah.
5. Periksa hasil import setelah proses selesai.

Simpan salinan export sebelum melakukan import dalam jumlah besar.

#### Barcode barang

- Pilih barcode pada baris barang untuk mencetak satu label.
- Gunakan pilihan barang dan menu barcode batch untuk mencetak beberapa label.
- Pilih ukuran label sebelum mencetak.
- Label berisi identitas TRPL, Polinela, nama barang, dan QR Code.

### 5.4 Mengelola booking ruangan

1. Buka **Booking Ruangan**.
2. Periksa pengajuan baru dan jadwal yang sudah ada.
3. Buka detail pengajuan untuk melihat pemesan, ruangan, tanggal, waktu, keperluan, dan lampiran.
4. Pilih **Setujui** atau **Tolak**.
5. Jika jadwal bertabrakan, sistem menampilkan informasi konflik dan pengajuan tidak dapat disetujui.
6. Setelah kegiatan selesai, gunakan aksi **Selesai** bila tersedia.

Periksa juga jadwal kuliah agar booking tidak bertabrakan dengan kegiatan akademik.

### 5.5 Memproses peminjaman alat

Alur peminjaman alat:

```text
Peminjam mengajukan
        ↓
Teknisi memvalidasi
        ↓
Kepala Laboratorium menyetujui
        ↓
Barang dipinjam
        ↓
Barang dikembalikan
```

Langkah Teknisi:

1. Buka **Peminjaman Alat**.
2. Cari pengajuan berdasarkan nama peminjam, keperluan, email, nama barang, atau barcode.
3. Periksa tanggal, daftar barang, keperluan, dan surat peminjaman jika ada.
4. Pilih **Validasi** untuk meneruskan pengajuan atau **Tolak** bila tidak memenuhi ketentuan.
5. Setelah barang dikembalikan, periksa kondisi barang.
6. Pilih **Kembalikan** untuk menyelesaikan transaksi.

Peminjaman yang melewati tenggat akan ditandai sebagai terlambat. Setelah dikembalikan, riwayat tetap menampilkan jumlah hari keterlambatan.

### 5.6 Melakukan audit inventaris

1. Buka **Audit Inventaris**.
2. Pilih periode audit yang aktif.
3. Buka audit barang atau audit ruangan.
4. Cocokkan data sistem dengan kondisi fisik.
5. Masukkan kondisi dan catatan untuk setiap item.
6. Gunakan input bulk bila ingin mengisi beberapa item sekaligus.
7. Simpan hasil audit.
8. Setelah semua data selesai diperiksa, pilih **Laporkan Hasil Audit**.

Teknisi dapat memperbaiki audit yang dikembalikan untuk revisi. Perhatikan notifikasi audit pada dashboard.

### 5.7 Mengelola jadwal kuliah

1. Buka **Jadwal Kuliah**.
2. Tambahkan tahun ajaran, mata kuliah, kelas, dosen, ruangan, hari, dan waktu.
3. Simpan jadwal.
4. Edit atau hapus data jika ada perubahan.
5. Gunakan import/export jika tersedia dan periksa konflik jadwal setelah import.

### 5.8 Mencetak laporan

1. Buka **Laporan**.
2. Pilih jenis laporan:
   - Data barang.
   - Data ruangan.
   - Rekapitulasi peminjaman.
   - Hasil audit inventaris.
3. Isi filter periode jika diperlukan.
4. Pilih **Cetak** atau unduh PDF dari penampil browser.

Laporan dilengkapi kop identitas TRPL dan Polinela serta tanda tangan Kepala Laboratorium:

**Dani Rofianto, S.Mat., M.Kom.**  
**NIP. 199311262022031005**

## 6. Panduan Kepala Laboratorium

### 6.1 Memeriksa dashboard

Periksa:

- Pengajuan peminjaman yang menunggu persetujuan.
- Booking ruangan yang perlu ditindaklanjuti.
- Notifikasi audit.
- Ringkasan barang dan ruangan.

### 6.2 Menyetujui peminjaman

1. Buka **Peminjaman Alat** atau notifikasi pengajuan.
2. Periksa hasil validasi Teknisi.
3. Periksa identitas peminjam, daftar barang, tanggal, keperluan, dan dokumen.
4. Pilih **ACC** untuk menyetujui.
5. Pilih **Tolak** bila pengajuan tidak disetujui dan isi alasan jika diminta.

Sistem mencegah persetujuan jika barang atau jadwal mengalami konflik.

### 6.3 Mengelola audit

1. Buka **Audit Inventaris**.
2. Pilih **Buat Periode Audit**.
3. Isi nama periode, tipe audit, tanggal mulai, tanggal selesai, dan catatan.
4. Buka periode agar dapat dikerjakan Teknisi.
5. Periksa hasil yang dilaporkan Teknisi.
6. Pilih **Validasi** untuk menyelesaikan audit atau kembalikan untuk revisi.
7. Tutup periode setelah seluruh hasil benar.

### 6.4 Melihat data operasional

Kepala Laboratorium dapat melihat data barang, ruangan, booking, jadwal, audit, dan laporan tanpa mengubah alur kerja Teknisi kecuali pada aksi yang memang diizinkan.

## 7. Panduan Ka. Prodi

### 7.1 Dashboard Prodi

Dashboard digunakan untuk melihat ringkasan informasi yang berkaitan dengan program studi.

### 7.2 Mengelola tahun ajaran

1. Buka **Tahun Ajaran**.
2. Tambahkan tahun ajaran baru dengan format yang konsisten, misalnya `2026/2027`.
3. Aktifkan satu tahun ajaran yang sedang digunakan.
4. Nonaktifkan atau hapus data lama hanya jika tidak lagi dipakai.

### 7.3 Mengelola jadwal kuliah

1. Buka **Jadwal Kuliah**.
2. Pilih **Tambah Jadwal**.
3. Isi tahun ajaran, mata kuliah, kelas, dosen, ruangan, hari, dan jam.
4. Simpan dan periksa jadwal berdasarkan ruangan.

Jadwal kuliah dipakai sebagai acuan saat memeriksa ketersediaan ruangan untuk booking.

### 7.4 Melihat laporan

Ka. Prodi dapat membuka menu laporan untuk melihat atau mencetak data yang dibutuhkan program studi.

## 8. Panduan Peminjam

### 8.1 Melihat katalog barang

1. Buka **Katalog Barang**.
2. Cari barang berdasarkan nama atau informasi yang tersedia.
3. Periksa kondisi, lokasi, dan ketersediaan barang.
4. Pilih barang yang ingin dipinjam.

### 8.2 Mengajukan peminjaman alat

1. Pilih barang yang tersedia.
2. Isi tanggal mulai dan tanggal kembali.
3. Isi keperluan peminjaman.
4. Unggah surat peminjaman bila diwajibkan.
5. Periksa kembali daftar barang.
6. Kirim pengajuan.

Status pengajuan:

- **Pending**: menunggu pemeriksaan.
- **Divalidasi Teknisi**: sudah diperiksa Teknisi dan menunggu persetujuan Kepala Laboratorium.
- **Disetujui**: peminjaman dapat dilaksanakan.
- **Ditolak**: pengajuan tidak disetujui; baca catatan penolakan.
- **Dikembalikan**: barang sudah dikembalikan dan transaksi selesai.

### 8.3 Booking ruangan

1. Buka **Katalog Ruangan**.
2. Pilih ruangan.
3. Lihat jadwal ruangan terlebih dahulu.
4. Isi tanggal, waktu mulai, waktu selesai, keperluan, dan data lain yang diminta.
5. Unggah surat booking bila diwajibkan.
6. Kirim pengajuan.

Pengajuan akan ditolak atau dikembalikan apabila waktunya bertabrakan dengan booking lain atau jadwal kuliah.

### 8.4 Melihat notifikasi dan riwayat

1. Buka dashboard peminjam.
2. Periksa notifikasi pengajuan diproses, ditolak, atau terlambat.
3. Buka **Riwayat** untuk melihat detail peminjaman dan booking.
4. Gunakan tautan pada notifikasi untuk langsung menuju riwayat yang sesuai.

Jika peminjaman melewati tanggal kembali, sistem menampilkan jumlah hari keterlambatan. Informasi tersebut tetap tersimpan setelah barang dikembalikan.

### 8.5 Mengembalikan barang

1. Kembalikan semua barang kepada Teknisi/laboratorium sebelum tanggal tenggat.
2. Pastikan kondisi barang diperiksa bersama petugas.
3. Tunggu status berubah menjadi **Dikembalikan**.
4. Jika terlambat, riwayat akan menampilkan jumlah hari keterlambatan.

## 9. Pemindaian QR Code

QR Code barang dan ruangan dapat dipindai menggunakan kamera ponsel:

1. Buka kamera atau aplikasi pemindai QR.
2. Arahkan ke QR Code.
3. Buka tautan yang muncul.
4. Periksa informasi barang atau ruangan.

Halaman hasil scan bersifat publik, tetapi tindakan pengelolaan tetap memerlukan login dan hak akses.

## 10. Notifikasi dan Indikator Status

Periksa ikon notifikasi pada navigasi. Notifikasi dapat berisi:

- Peminjaman yang menunggu validasi atau persetujuan.
- Booking ruangan yang menunggu tindakan.
- Peminjaman yang terlambat.
- Audit yang perlu dikerjakan atau direvisi.

Warna umum yang digunakan:

- **Kuning**: menunggu tindakan.
- **Merah**: ditolak, bermasalah, atau terlambat.
- **Hijau**: disetujui, tersedia, atau selesai.
- **Biru/Indigo**: sedang divalidasi atau informasi proses.

## 11. Pemecahan Masalah

### Tidak dapat login

- Pastikan email dan kata sandi benar.
- Pastikan akun sudah terdaftar.
- Gunakan fitur lupa kata sandi jika tersedia.
- Hubungi Super Admin jika akun dinonaktifkan atau perannya salah.

### Data tidak tersimpan

- Pastikan semua kolom wajib telah diisi.
- Periksa format tanggal, angka, dan file.
- Pastikan ukuran file tidak melebihi batas.
- Muat ulang halaman dan periksa notifikasi kesalahan.
- Hubungi Teknisi atau Super Admin jika masalah berulang.

### Booking ditolak karena bentrok

- Buka jadwal ruangan.
- Pilih waktu lain yang tidak bertabrakan.
- Periksa juga jadwal kuliah pada ruangan tersebut.

### Barang tidak terlihat tersedia

- Periksa status barang.
- Pastikan barang tidak sedang dipinjam.
- Pastikan filter pencarian telah direset.
- Hubungi Teknisi untuk pemeriksaan data inventaris.

### Import gagal

- Gunakan file hasil export atau template terbaru.
- Jangan mengubah nama kolom.
- Pastikan format tanggal dan angka benar.
- Hapus baris kosong atau data duplikat.
- Import kembali setelah memperbaiki baris yang bermasalah.

### Laporan tidak sesuai

- Periksa filter periode dan data sumber.
- Pastikan transaksi atau audit sudah disimpan.
- Muat ulang halaman laporan.
- Hubungi Teknisi atau Super Admin jika data tetap berbeda.

## 12. Praktik Penggunaan yang Disarankan

- Selalu periksa notifikasi setelah login.
- Gunakan data master yang konsisten untuk kategori, ruangan, tahun ajaran, dan jadwal.
- Jangan menghapus data historis yang masih diperlukan untuk laporan.
- Lakukan export berkala sebagai cadangan.
- Periksa kondisi barang saat serah terima dan pengembalian.
- Tutup periode audit hanya setelah seluruh item selesai diperiksa.
- Logout setelah selesai menggunakan komputer bersama.
- Jangan membagikan kata sandi kepada pengguna lain.

## 13. Ringkasan Alur Kerja

### Peminjaman alat

```text
Peminjam membuat pengajuan
  -> Teknisi memvalidasi
  -> Kepala Laboratorium menyetujui
  -> Peminjam menggunakan barang
  -> Teknisi menerima dan memeriksa pengembalian
  -> Transaksi selesai
```

### Booking ruangan

```text
Peminjam memilih ruangan dan jadwal
  -> Sistem memeriksa konflik
  -> Teknisi/Kepala Laboratorium menindaklanjuti
  -> Booking disetujui atau ditolak
  -> Kegiatan selesai
```

### Audit inventaris

```text
Kepala Laboratorium membuat periode
  -> Teknisi memeriksa barang/ruangan
  -> Teknisi melaporkan hasil
  -> Kepala Laboratorium memvalidasi
  -> Periode audit ditutup
```

