<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SILab') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 flex h-screen overflow-hidden bg-gray-50"
      x-data="{ isCollapsed: localStorage.getItem('sidebar_state') === 'true' }"
      x-init="$watch('isCollapsed', val => localStorage.setItem('sidebar_state', val))">

    <aside :class="isCollapsed ? 'w-20' : 'w-64'" class="bg-[#1e293b] text-slate-300 flex flex-col shadow-xl z-20 transition-all duration-300 ease-in-out shrink-0 relative">

        <div class="h-16 flex items-center justify-center bg-[#0f172a] text-white font-bold text-xl tracking-wider border-b border-slate-800 overflow-hidden shrink-0">
            <svg class="w-6 h-6 shrink-0 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            <span x-show="!isCollapsed" class="ml-3 transition-opacity duration-300 whitespace-nowrap">SILab</span>
        </div>

        <nav class="flex-1 py-6 space-y-1 overflow-y-auto overflow-x-hidden scrollbar-hide">

            @if(auth()->user()->role === 'super_admin')
                <p x-show="!isCollapsed" class="px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-4">Menu Admin</p>
                <div x-show="isCollapsed" class="h-4"></div>

                <a href="{{ route('admin.dashboard') }}" title="Dashboard Admin" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Dashboard Admin</span>
                </a>
                <a href="{{ route('admin.users.index') }}" title="Kelola Pengguna" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Kelola Pengguna</span>
                </a>
                <a href="{{ route('admin.tahun_ajaran.index') }}" title="Tahun Ajaran" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.tahun_ajaran.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Tahun Ajaran</span>
                </a>
            @endif

            @if(auth()->user()->role === 'teknisi' || auth()->user()->role === 'super_admin')
                <p x-show="!isCollapsed" class="px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-4">Menu Teknisi</p>
                <div x-show="isCollapsed" class="h-4"></div>

                <a href="{{ route('operator.dashboard') }}" title="Dashboard" class="flex items-center px-6 py-3 {{ request()->routeIs('operator.dashboard') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Dashboard</span>
                </a>
                <a href="{{ route('kategori.index') }}" title="Data Kategori" class="flex items-center px-6 py-3 {{ request()->routeIs('kategori.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Data Kategori</span>
                </a>
                <a href="{{ route('barang.index') }}" title="Data Barang" class="flex items-center px-6 py-3 {{ request()->routeIs('barang.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Data Barang</span>
                </a>
                <a href="{{ route('ruangan.index') }}" title="Data Ruang" class="flex items-center px-6 py-3 {{ request()->routeIs('ruangan.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Data Ruang</span>
                </a>
                <a href="{{ route('admin.jadwal_kuliah.index') }}" title="Jadwal Kuliah" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.jadwal_kuliah.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Jadwal Kuliah</span>
                </a>
                <a href="{{ route('admin.audit.periode.index') }}" title="Audit Inventaris" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.audit.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Audit Inventaris</span>
                </a>
                <a href="{{ route('peminjaman.index') }}" title="Peminjaman" class="flex items-center px-6 py-3 {{ request()->routeIs('peminjaman.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Peminjaman</span>
                </a>
                <a href="{{ route('booking.index') }}" title="Booking Ruang" class="flex items-center px-6 py-3 {{ request()->routeIs('booking.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Booking Ruang</span>
                </a>
                <a href="{{ route('laporan.index') }}" title="Cetak Laporan" class="flex items-center px-6 py-3 {{ request()->routeIs('laporan.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Laporan</span>
                </a>

            @elseif(auth()->user()->role === 'kepala_lab' || auth()->user()->role === 'ka_prodi')
                <p x-show="!isCollapsed" class="px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-4">Menu Pimpinan</p>
                <div x-show="isCollapsed" class="h-4"></div>
                
                @if(auth()->user()->role === 'ka_prodi')
                <a href="{{ route('prodi.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('prodi.dashboard') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Dashboard</span>
                </a>
                @else
                <a href="{{ url('/kepala-lab/dashboard') }}" class="flex items-center px-6 py-3 {{ request()->is('kepala-lab/dashboard') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Dashboard</span>
                </a>

                <a href="{{ route('kepala_lab.barang.index') }}" title="Data Barang" class="flex items-center px-6 py-3 {{ request()->routeIs('kepala_lab.barang.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Data Barang</span>
                </a>
                <a href="{{ route('kepala_lab.ruangan.index') }}" title="Data Ruang" class="flex items-center px-6 py-3 {{ request()->routeIs('kepala_lab.ruangan.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Data Ruang</span>
                </a>
                <a href="{{ route('peminjaman.index') }}" title="Peminjaman" class="flex items-center px-6 py-3 {{ request()->routeIs('peminjaman.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Peminjaman</span>
                </a>
                <a href="{{ route('kepala_lab.booking.index') }}" title="Booking Ruang" class="flex items-center px-6 py-3 {{ request()->routeIs('kepala_lab.booking.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Booking Ruang</span>
                </a>
                @endif

                <a href="{{ route('admin.tahun_ajaran.index') }}" title="Tahun Ajaran" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.tahun_ajaran.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Tahun Ajaran</span>
                </a>
                <a href="{{ route('admin.jadwal_kuliah.index') }}" title="Jadwal Kuliah" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.jadwal_kuliah.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Jadwal Kuliah</span>
                </a>
                <a href="{{ route('laporan.index') }}" title="Cetak Laporan" class="flex items-center px-6 py-3 {{ request()->routeIs('laporan.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Laporan</span>
                </a>

                @if(auth()->user()->role === 'kepala_lab')
                <a href="{{ route('admin.audit.periode.index') }}" title="Audit Inventaris" class="flex items-center px-6 py-3 {{ request()->routeIs('admin.audit.*') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Audit Inventaris</span>
                </a>
                @endif

            @elseif(auth()->user()->role === 'peminjam')
                <p x-show="!isCollapsed" class="px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2">Menu Mahasiswa</p>
                <div x-show="isCollapsed" class="h-4"></div>

                <a href="{{ route('user.dashboard') }}" title="Dashboard" class="flex items-center px-6 py-3 {{ request()->routeIs('peminjam.dashboard') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Dashboard Saya</span>
                </a>
                <a href="{{ route('peminjam.katalog.barang') }}" title="Katalog Barang" class="flex items-center px-6 py-3 {{ request()->routeIs('peminjam.katalog.barang') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Katalog Barang</span>
                </a>
                <a href="{{ route('peminjam.katalog.ruangan') }}" title="Katalog Ruangan" class="flex items-center px-6 py-3 {{ request()->routeIs('peminjam.katalog.ruangan') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Katalog Ruangan</span>
                </a>
                <a href="{{ route('peminjam.riwayat') }}" title="Riwayat" class="flex items-center px-6 py-3 {{ request()->routeIs('peminjam.riwayat') ? 'bg-slate-800 text-white border-l-4 border-indigo-500' : 'hover:bg-slate-800 hover:text-white' }} transition-colors mt-1 group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 text-sm whitespace-nowrap">Riwayat Pengajuan</span>
                </a>
            @endif

        </nav>

        <div class="mt-auto border-t border-slate-800 flex flex-col">
            <form method="POST" action="{{ route('logout') }}" class="p-4">
                @csrf
                <button title="Keluar" type="submit" class="flex items-center w-full px-2 py-2 text-sm text-slate-400 hover:text-white hover:bg-slate-800 rounded transition-colors group">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    <span x-show="!isCollapsed" class="ml-4 whitespace-nowrap">Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50">

        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10 shadow-sm shrink-0">

            <div class="flex items-center flex-1">
                <button @click="isCollapsed = !isCollapsed" class="mr-4 text-gray-500 hover:text-indigo-600 focus:outline-none transition-colors p-1 rounded-md hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <div class="font-bold text-xl text-gray-800 flex-1">
                    {{ $header ?? 'SILab' }}
                </div>
            </div>

            <div class="flex items-center space-x-6">


                <!-- <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition-all duration-300 focus:outline-none group">
                        <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if($notifCount > 0)
                            <span class="absolute top-1.5 right-1.5 inline-flex items-center justify-center w-4 h-4 text-[10px] font-bold leading-none text-white bg-rose-500 rounded-full ring-2 ring-white animate-pulse">{{ $notifCount }}</span>
                        @endif
                    </button>
                </div> -->

                <div class="flex items-center border-l pl-6 border-gray-200">
                    <div class="bg-indigo-100 text-indigo-600 w-9 h-9 rounded-full flex items-center justify-center font-bold mr-3">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="text-sm">
                        <p class="font-bold text-gray-700 leading-none">{{ auth()->user()->name }}</p>
                        <p class="text-gray-500 text-xs mt-1 capitalize">{{ auth()->user()->role }}</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-x-hidden overflow-y-auto p-8 relative">
            {{ $slot }}
        </main>

    </div>

</body>

{{-- Modal Global: Preview Surat/Dokumen --}}
<div id="suratModal" class="fixed inset-0 z-[999] hidden" onclick="closeSuratModal()">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="absolute inset-0 bg-black opacity-70"></div>
        <div class="relative bg-white rounded-lg shadow-2xl w-full max-w-4xl flex flex-col" style="height: 90vh;" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center p-4 border-b shrink-0">
                <h3 class="font-bold text-gray-800">Preview Surat Peminjaman</h3>
                <button onclick="closeSuratModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-hidden p-2">
                <iframe id="suratIframe" src="" class="w-full h-full rounded border border-gray-200" style="display:block;"></iframe>
                <img id="suratImage" src="" class="w-full h-full object-contain hidden" alt="Surat Peminjaman">
            </div>
        </div>
    </div>
</div>
<script>
    function openSuratModal(url) {
        const ext = url.split('.').pop().toLowerCase();
        const iframe = document.getElementById('suratIframe');
        const img = document.getElementById('suratImage');
        if (['jpg','jpeg','png','gif','webp'].includes(ext)) {
            iframe.style.display = 'none';
            iframe.src = '';
            img.src = url;
            img.classList.remove('hidden');
        } else {
            img.classList.add('hidden');
            img.src = '';
            iframe.style.display = 'block';
            iframe.src = url;
        }
        document.getElementById('suratModal').classList.remove('hidden');
    }
    function closeSuratModal() {
        document.getElementById('suratModal').classList.add('hidden');
        document.getElementById('suratIframe').src = '';
        document.getElementById('suratImage').src = '';
    }
</script>

</html>
