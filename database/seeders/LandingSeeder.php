<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Settings (Statistik & Footer)
        $settings = [
            // Statistik
            ['key' => 'stats_ruangan', 'value' => '12', 'type' => 'number', 'group' => 'statistik'],
            ['key' => 'stats_inventaris', 'value' => '500+', 'type' => 'text', 'group' => 'statistik'],
            ['key' => 'stats_penghargaan', 'value' => '25', 'type' => 'number', 'group' => 'statistik'],
            ['key' => 'stats_mahasiswa', 'value' => '1200+', 'type' => 'text', 'group' => 'statistik'],
            
            // Footer Info
            ['key' => 'footer_desc', 'value' => 'Sistem informasi manajemen inventaris laboratorium komputer terpadu. Memudahkan pengelolaan aset, peminjaman peralatan, dan booking ruangan secara digital.', 'type' => 'textarea', 'group' => 'footer'],
            ['key' => 'footer_address', 'value' => 'Kampus Politeknik Negeri Lampung, Jl. Soekarno-Hatta No.10, Bandar Lampung, Lampung, Indonesia. 35141', 'type' => 'textarea', 'group' => 'footer'],
            ['key' => 'footer_phone', 'value' => '(021) 123-4567', 'type' => 'text', 'group' => 'footer'],
            ['key' => 'footer_email', 'value' => 'trpl@polinela.ac.id', 'type' => 'text', 'group' => 'footer'],
            ['key' => 'footer_hours', 'value' => 'Sen-Jum, 08:00-17:00', 'type' => 'text', 'group' => 'footer'],
            ['key' => 'social_instagram', 'value' => 'https://www.instagram.com/trpl.polinela', 'type' => 'text', 'group' => 'footer'],
            ['key' => 'social_youtube', 'value' => 'https://www.youtube.com/@trplpolinela', 'type' => 'text', 'group' => 'footer'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // 2. Dokumentasi
        if (\App\Models\LandingDokumentasi::count() == 0) {
            \App\Models\LandingDokumentasi::insert([
                [
                    'judul' => 'Workshop Pemrograman Web Modern',
                    'deskripsi' => 'Pelatihan intensif pengembangan web menggunakan teknologi terkini seperti Laravel, React, dan microservices. Diikuti oleh 80+ mahasiswa dari berbagai program studi.',
                    'tanggal' => '2026-05-15',
                    'tag' => 'Workshop',
                    'gambar' => 'lab_activity_1.png', // assumed these exist in public/images/landing
                    'created_at' => now(), 'updated_at' => now()
                ],
                [
                    'judul' => 'Praktikum Internet of Things (IoT)',
                    'deskripsi' => 'Sesi praktikum hands-on menggunakan sensor, mikrokontroler, dan platform IoT. Mahasiswa belajar membuat smart device dan sistem monitoring otomatis.',
                    'tanggal' => '2026-04-28',
                    'tag' => 'Praktikum',
                    'gambar' => 'lab_activity_2.png',
                    'created_at' => now(), 'updated_at' => now()
                ],
                [
                    'judul' => 'Seminar Kecerdasan Buatan & Machine Learning',
                    'deskripsi' => 'Seminar bersama pakar industri tentang implementasi AI dalam dunia kerja, dilengkapi demo proyek mahasiswa dan sesi tanya jawab interaktif.',
                    'tanggal' => '2026-03-10',
                    'tag' => 'Seminar',
                    'gambar' => 'lab_activity_3.png',
                    'created_at' => now(), 'updated_at' => now()
                ]
            ]);
        }

        // 3. Prestasi
        if (\App\Models\LandingPrestasi::count() == 0) {
            \App\Models\LandingPrestasi::insert([
                [
                    'judul' => 'Juara 1 Gemastik XVI',
                    'deskripsi' => 'Tim lab berhasil meraih juara pertama dalam Pagelaran Mahasiswa Nasional bidang TIK kategori Keamanan Siber.',
                    'tahun' => '2026',
                    'ikon' => '🏆',
                    'medali' => 'gold',
                    'created_at' => now(), 'updated_at' => now()
                ],
                [
                    'judul' => 'Best Paper Award - ICSEC 2025',
                    'deskripsi' => 'Paper penelitian tentang optimisasi jaringan kampus menggunakan SDN mendapat penghargaan Best Paper.',
                    'tahun' => '2025',
                    'ikon' => '🎖️',
                    'medali' => 'champion',
                    'created_at' => now(), 'updated_at' => now()
                ],
                [
                    'judul' => 'Runner Up Hackathon Nasional',
                    'deskripsi' => 'Tim mahasiswa lab komputer berhasil menjadi runner up dalam Hackathon Nasional dengan solusi Smart Campus berbasis IoT.',
                    'tahun' => '2025',
                    'ikon' => '🥈',
                    'medali' => 'silver',
                    'created_at' => now(), 'updated_at' => now()
                ],
                [
                    'judul' => 'Juara 3 Kompetisi Web Design',
                    'deskripsi' => 'Mahasiswa dari Laboratorium Web Development berhasil meraih juara 3 pada ajang kompetisi desain web tingkat nasional dengan UI/UX inovatif.',
                    'tahun' => '2024',
                    'ikon' => '🏅',
                    'medali' => 'bronze',
                    'created_at' => now(), 'updated_at' => now()
                ]
            ]);
        }

        // 4. Fasilitas
        if (\App\Models\LandingFasilitas::count() == 0) {
            \App\Models\LandingFasilitas::insert([
                [
                    'judul' => 'Lab Komputer Utama',
                    'deskripsi' => '40 unit PC spesifikasi tinggi dengan monitor dual',
                    'gambar' => 'lab_facility_1.png',
                    'created_at' => now(), 'updated_at' => now()
                ],
                [
                    'judul' => 'Lab Elektronika',
                    'deskripsi' => 'Peralatan praktikum lengkap',
                    'gambar' => 'lab_facility_2.png',
                    'created_at' => now(), 'updated_at' => now()
                ],
                [
                    'judul' => 'Lab Jaringan',
                    'deskripsi' => 'Server rack & networking profesional',
                    'gambar' => 'lab_facility_3.png',
                    'created_at' => now(), 'updated_at' => now()
                ]
            ]);
        }
    }
}
