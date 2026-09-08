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
        // 1. Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
        ]);

        // 2. Teknisi (Operator Lab)
        User::create([
            'name' => 'Teknisi Lab',
            'email' => 'teknisi@test.com',
            'password' => Hash::make('password123'),
            'role' => 'teknisi',
        ]);

        // 3. Kepala Laboratorium
        User::create([
            'name' => 'Kepala Lab',
            'email' => 'kepalalab@test.com',
            'password' => Hash::make('password123'),
            'role' => 'kepala_lab',
        ]);

        // 4. Koordinator Prodi
        User::create([
            'name' => 'Ka Prodi',
            'email' => 'prodi@test.com',
            'password' => Hash::make('password123'),
            'role' => 'ka_prodi',
        ]);

        // 5. Mahasiswa / Peminjam
        User::create([
            'name' => 'Mahasiswa Peminjam',
            'email' => 'peminjam@test.com',
            'password' => Hash::make('password123'),
            'role' => 'peminjam',
        ]);
    }
}
