<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Operator Lab',
            'email' => 'operator@test.com',
            'password' => Hash::make('password123'),
            'role' => 'operator',
        ]);

        // 3. Akun Koordinator Prodi (Hanya Lihat Laporan)
        User::create([
            'name' => 'Koordinator Prodi',
            'email' => 'prodi@test.com',
            'password' => Hash::make('password123'),
            'role' => 'prodi',
        ]);

        // 4. Akun Mahasiswa / Peminjam
        User::create([
            'name' => 'Mahasiswa Peminjam',
            'email' => 'peminjam@test.com',
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
        ]);
    }
}
