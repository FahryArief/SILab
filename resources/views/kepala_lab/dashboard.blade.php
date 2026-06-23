<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-xl text-gray-800">Dashboard Kepala Laboratorium</h2>
                <p class="text-sm text-gray-500 mt-0.5 font-normal">Pantau aset, validasi peminjaman, dan kelola laboratorium TRPL</p>
            </div>
            <div class="text-right mr-5">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Tahun Ajaran</p>
                <p class="text-sm font-mono font-bold text-indigo-600">
                    {{ $tahunAjaranAktif ? $tahunAjaranAktif->nama_tahun . ' (' . $tahunAjaranAktif->semester . ')' : 'Belum di-set' }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- BARIS 1: ALERT ACC + WELCOME --}}
            @if($menungguAcc > 0)
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-xl p-5 shadow-lg text-white flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-white/20 p-3 rounded-xl mr-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">{{ $menungguAcc }} Pengajuan Menunggu ACC Anda</h3>
                        <p class="text-amber-100 text-sm">Peminjaman yang sudah divalidasi teknisi dan perlu persetujuan Anda.</p>
                    </div>
                </div>
                <a href="{{ route('peminjaman.index') }}" class="bg-white text-amber-600 px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-amber-50 transition shadow">
                    Lihat & ACC →
                </a>
            </div>
            @endif

            {{-- BARIS 2: STATISTIK WIDGETS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                {{-- Total Aset --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-emerald-500 bg-emerald-50 px-2 py-0.5 rounded-full">{{ $barangTersedia }} tersedia</span>
                    </div>
                    <p class="text-2xl font-black text-gray-800">{{ $totalBarang }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Total Aset Barang</p>
                </div>

                {{-- Ruangan --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-blue-500 bg-blue-50 px-2 py-0.5 rounded-full">{{ $jadwalAktifCount }} jadwal</span>
                    </div>
                    <p class="text-2xl font-black text-gray-800">{{ $totalRuangan }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Total Ruangan</p>
                </div>

                {{-- Menunggu ACC --}}
                <div class="bg-white rounded-xl border {{ $menungguAcc > 0 ? 'border-amber-300 ring-1 ring-amber-100' : 'border-gray-100' }} shadow-sm p-5 hover:shadow-md transition relative overflow-hidden">
                    @if($menungguAcc > 0)
                        <div class="absolute top-0 right-0 w-1.5 h-full bg-amber-400"></div>
                    @endif
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-amber-50 text-amber-500 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-black text-gray-800">{{ $menungguAcc }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Menunggu ACC Anda</p>
                </div>

                {{-- Sedang Dipinjam --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-3">
                        <div class="p-2.5 bg-purple-50 text-purple-600 rounded-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                    </div>
                    <p class="text-2xl font-black text-gray-800">{{ $totalAktif }}</p>
                    <p class="text-xs text-gray-500 font-medium mt-0.5">Peminjaman Aktif</p>
                </div>
            </div>

            {{-- BARIS 3: KONDISI ASET RINGKAS --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-700 text-sm mb-4">Kondisi Aset Laboratorium</h3>
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-emerald-50 rounded-lg border border-emerald-100">
                        <p class="text-2xl font-black text-emerald-600">{{ $barangBaik }}</p>
                        <p class="text-xs text-emerald-700 font-bold mt-1">✓ BAIK</p>
                        <div class="mt-2 bg-emerald-200 rounded-full h-1.5"><div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $totalBarang > 0 ? ($barangBaik / $totalBarang) * 100 : 0 }}%"></div></div>
                    </div>
                    <div class="text-center p-4 bg-amber-50 rounded-lg border border-amber-100">
                        <p class="text-2xl font-black text-amber-600">{{ $barangRusakRingan }}</p>
                        <p class="text-xs text-amber-700 font-bold mt-1">⚠ RUSAK RINGAN</p>
                        <div class="mt-2 bg-amber-200 rounded-full h-1.5"><div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $totalBarang > 0 ? ($barangRusakRingan / $totalBarang) * 100 : 0 }}%"></div></div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg border border-red-100">
                        <p class="text-2xl font-black text-red-600">{{ $barangRusakBerat }}</p>
                        <p class="text-xs text-red-700 font-bold mt-1">✗ RUSAK BERAT</p>
                        <div class="mt-2 bg-red-200 rounded-full h-1.5"><div class="bg-red-500 h-1.5 rounded-full" style="width: {{ $totalBarang > 0 ? ($barangRusakBerat / $totalBarang) * 100 : 0 }}%"></div></div>
                    </div>
                </div>
            </div>

            {{-- BARIS 4: GRAFIK --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Tren Peminjaman --}}
                <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-700 text-sm">Tren Peminjaman Alat ({{ $tahunIni }})</h3>
                        <a href="{{ route('laporan.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Lihat Laporan →</a>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="trenChart"></canvas>
                    </div>
                </div>

                {{-- Kondisi Barang Donut --}}
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-700 text-sm mb-4">Distribusi Kondisi Barang</h3>
                    <div class="relative h-64 w-full flex justify-center">
                        <canvas id="kondisiChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- BARIS 5: TABEL PENGAJUAN + AKTIF --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Menunggu ACC Anda --}}
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-amber-50/50">
                        <h3 class="font-bold text-gray-700 text-sm flex items-center">
                            <span class="w-2 h-2 bg-amber-400 rounded-full mr-2 animate-pulse"></span>
                            Menunggu ACC Anda
                        </h3>
                        <a href="{{ route('peminjaman.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Kelola →</a>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-gray-100">
                            @forelse($recentMenungguAcc as $pinjam)
                                <li class="p-4 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $pinjam->user ? $pinjam->user->name : $pinjam->nama_peminjam }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $pinjam->barangs->count() }} item: {{ $pinjam->barangs->pluck('nama_barang')->unique()->implode(', ') }}</p>
                                            <div class="flex gap-1 mt-1.5 flex-wrap">
                                                @foreach($pinjam->barangs->take(3) as $brg)
                                                    <span class="text-[9px] font-mono bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded border border-indigo-100">{{ $brg->barcode }}</span>
                                                @endforeach
                                                @if($pinjam->barangs->count() > 3)
                                                    <span class="text-[9px] text-gray-400">+{{ $pinjam->barangs->count() - 3 }} lainnya</span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded uppercase whitespace-nowrap">Perlu ACC</span>
                                    </div>
                                </li>
                            @empty
                                <li class="p-8 text-center text-sm text-gray-400 italic">
                                    Tidak ada pengajuan yang menunggu ACC saat ini. 👍
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- Peminjaman Aktif --}}
                <div class="bg-white border border-gray-100 rounded-xl shadow-sm">
                    <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-emerald-50/50">
                        <h3 class="font-bold text-gray-700 text-sm flex items-center">
                            <span class="w-2 h-2 bg-emerald-500 rounded-full mr-2"></span>
                            Peminjaman Aktif (Sedang Dipinjam)
                        </h3>
                        <span class="text-xs text-gray-400">{{ $totalAktif }} total</span>
                    </div>
                    <div class="p-0">
                        <ul class="divide-y divide-gray-100">
                            @forelse($recentAktif as $pinjam)
                                <li class="p-4 hover:bg-gray-50 transition">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $pinjam->user ? $pinjam->user->name : $pinjam->nama_peminjam }}</p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $pinjam->barangs->count() }} item &bull;
                                                Kembali: {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d M Y') }}
                                            </p>
                                        </div>
                                        @php
                                            $isOverdue = \Carbon\Carbon::parse($pinjam->tanggal_kembali)->isPast();
                                        @endphp
                                        <span class="px-2 py-1 {{ $isOverdue ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }} text-[10px] font-bold rounded uppercase whitespace-nowrap">
                                            {{ $isOverdue ? 'TERLAMBAT' : 'AKTIF' }}
                                        </span>
                                    </div>
                                </li>
                            @empty
                                <li class="p-8 text-center text-sm text-gray-400 italic">
                                    Tidak ada peminjaman aktif saat ini.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            {{-- BARIS 6: QUICK ACCESS --}}
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-700 text-sm mb-4">Akses Cepat</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <a href="{{ route('peminjaman.index') }}" class="flex items-center p-3 bg-amber-50 rounded-lg hover:bg-amber-100 transition border border-amber-100">
                        <div class="p-2 bg-amber-100 text-amber-600 rounded-lg mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Peminjaman</p>
                            <p class="text-[10px] text-gray-500">Validasi & ACC</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.jadwal_kuliah.index') }}" class="flex items-center p-3 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition border border-indigo-100">
                        <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Jadwal Kuliah</p>
                            <p class="text-[10px] text-gray-500">Kelola jadwal ruangan</p>
                        </div>
                    </a>
                    <a href="{{ route('laporan.index') }}" class="flex items-center p-3 bg-emerald-50 rounded-lg hover:bg-emerald-100 transition border border-emerald-100">
                        <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Laporan</p>
                            <p class="text-[10px] text-gray-500">Cetak & statistik</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.tahun_ajaran.index') }}" class="flex items-center p-3 bg-purple-50 rounded-lg hover:bg-purple-100 transition border border-purple-100">
                        <div class="p-2 bg-purple-100 text-purple-600 rounded-lg mr-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-800">Tahun Ajaran</p>
                            <p class="text-[10px] text-gray-500">Atur periode aktif</p>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tren Peminjaman
            new Chart(document.getElementById('trenChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Total Peminjaman',
                        data: {!! json_encode($dataTren) !!},
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointBackgroundColor: 'rgb(79, 70, 229)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: 'rgba(0,0,0,0.04)' } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // Kondisi Barang
            new Chart(document.getElementById('kondisiChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($kondisiLabel) !!},
                    datasets: [{
                        data: {!! json_encode($kondisiData) !!},
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.85)',
                            'rgba(245, 158, 11, 0.85)',
                            'rgba(239, 68, 68, 0.85)',
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { padding: 15, usePointStyle: true, pointStyle: 'circle' } }
                    }
                }
            });
        });
    </script>
</x-app-layout>
