<x-app-layout>
    <x-slot name="header">Laporan & Statistik</x-slot>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header: Year Filter + Export --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <form method="GET" action="{{ route('laporan.index') }}" class="flex items-center gap-2">
                    <label class="text-sm font-semibold text-gray-600">Tahun:</label>
                    <select name="tahun" onchange="this.form.submit()" class="appearance-none bg-white border border-gray-200 text-gray-700 py-2 pl-4 pr-10 rounded-lg text-sm focus:outline-none focus:ring-1 focus:ring-slate-500 shadow-sm cursor-pointer">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ $tahunIni == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
            </div>

            <div x-data="{ exportOpen: false }" class="relative">
                <button @click="exportOpen = !exportOpen" class="bg-[#1e293b] hover:bg-[#0f172a] text-white px-5 py-2 rounded-lg text-sm font-bold flex items-center shadow-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export PDF
                    <svg class="w-4 h-4 ml-2 transition-transform" :class="{ 'rotate-180': exportOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="exportOpen" @click.outside="exportOpen = false" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 z-50 overflow-hidden">
                    <a href="{{ route('laporan.barang') }}" target="_blank" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 transition-colors border-b border-gray-100">
                        <span class="text-lg mr-3">📦</span>
                        <div>
                            <div class="font-bold text-xs">Laporan Data Barang</div>
                            <div class="text-[10px] text-gray-400">Seluruh inventaris barang</div>
                        </div>
                    </a>
                    <a href="{{ route('laporan.ruangan') }}" target="_blank" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-emerald-50 transition-colors border-b border-gray-100">
                        <span class="text-lg mr-3">🏠</span>
                        <div>
                            <div class="font-bold text-xs">Laporan Data Ruangan</div>
                            <div class="text-[10px] text-gray-400">Seluruh data ruangan lab</div>
                        </div>
                    </a>
                    <button @click="exportOpen = false; document.getElementById('exportPeminjamanModal').classList.remove('hidden')" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 transition-colors w-full text-left border-b border-gray-100">
                        <span class="text-lg mr-3">📋</span>
                        <div>
                            <div class="font-bold text-xs">Laporan Peminjaman</div>
                            <div class="text-[10px] text-gray-400">Per rentang tanggal</div>
                        </div>
                    </button>
                    <button @click="exportOpen = false; document.getElementById('exportAuditModal').classList.remove('hidden')" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-purple-50 transition-colors w-full text-left">
                        <span class="text-lg mr-3">🔍</span>
                        <div>
                            <div class="font-bold text-xs">Laporan Audit</div>
                            <div class="text-[10px] text-gray-400">Per periode audit</div>
                        </div>
                    </button>
                </div>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Barang</p>
                        <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ number_format($totalBarang) }}</p>
                    </div>
                    <div class="bg-indigo-50 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Total Ruangan</p>
                        <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ number_format($totalRuangan) }}</p>
                    </div>
                    <div class="bg-emerald-50 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Peminjaman ({{ $tahunIni }})</p>
                        <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ number_format($totalPeminjaman) }}</p>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Booking ({{ $tahunIni }})</p>
                        <p class="text-2xl font-extrabold text-gray-800 mt-1">{{ number_format($totalBooking) }}</p>
                    </div>
                    <div class="bg-purple-50 p-3 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status Peminjaman Mini Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-amber-50 border border-amber-100 rounded-lg p-3 text-center">
                <p class="text-lg font-extrabold text-amber-700">{{ $statusStats->pending ?? 0 }}</p>
                <p class="text-[10px] uppercase font-bold text-amber-500 tracking-wider">Pending</p>
            </div>
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-center">
                <p class="text-lg font-extrabold text-blue-700">{{ $statusStats->aktif ?? 0 }}</p>
                <p class="text-[10px] uppercase font-bold text-blue-500 tracking-wider">Sedang Dipinjam</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-lg p-3 text-center">
                <p class="text-lg font-extrabold text-green-700">{{ $statusStats->selesai ?? 0 }}</p>
                <p class="text-[10px] uppercase font-bold text-green-500 tracking-wider">Dikembalikan</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-lg p-3 text-center">
                <p class="text-lg font-extrabold text-red-700">{{ $statusStats->ditolak ?? 0 }}</p>
                <p class="text-[10px] uppercase font-bold text-red-500 tracking-wider">Ditolak</p>
            </div>
        </div>

        {{-- Charts Row 1: Tren Peminjaman + Kondisi Barang --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-1 text-sm">Tren Peminjaman Alat ({{ $tahunIni }})</h3>
                <p class="text-xs text-gray-400 mb-4">Jumlah peminjaman per bulan</p>
                <div class="relative h-64 w-full">
                    <canvas id="trenChart"></canvas>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-1 text-sm">Kondisi Barang</h3>
                <p class="text-xs text-gray-400 mb-4">Distribusi kondisi seluruh barang</p>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="kondisiChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Charts Row 2: Kategori + Ruangan --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-1 text-sm">Distribusi Kategori Barang</h3>
                <p class="text-xs text-gray-400 mb-4">Jumlah item per kategori</p>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="kategoriChart"></canvas>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-1 text-sm">Top 5 Ruangan Paling Sering Digunakan</h3>
                <p class="text-xs text-gray-400 mb-4">Berdasarkan jumlah booking</p>
                <div class="relative h-64 w-full">
                    <canvas id="ruanganChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Charts Row 3: Tren Booking --}}
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
            <h3 class="font-bold text-gray-800 mb-1 text-sm">Tren Booking Ruangan ({{ $tahunIni }})</h3>
            <p class="text-xs text-gray-400 mb-4">Jumlah booking per bulan</p>
            <div class="relative h-56 w-full">
                <canvas id="bookingTrenChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Export PDF Modal: Peminjaman --}}
    <div id="exportPeminjamanModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 opacity-50 transition-opacity" onclick="document.getElementById('exportPeminjamanModal').classList.add('hidden')"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
                <div class="flex justify-between items-center mb-5 border-b pb-3">
                    <h3 class="text-lg font-bold text-gray-800">📋 Laporan Peminjaman</h3>
                    <button onclick="document.getElementById('exportPeminjamanModal').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('laporan.peminjaman') }}" method="POST" target="_blank">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                            <input type="date" name="tgl_mulai" class="w-full border-gray-300 rounded-lg text-sm focus:border-slate-500 focus:ring-slate-500" required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                            <input type="date" name="tgl_sampai" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-lg text-sm focus:border-slate-500 focus:ring-slate-500" required>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-[#1e293b] text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow hover:bg-[#0f172a] transition w-full flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Unduh PDF Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Export PDF Modal: Audit --}}
    <div id="exportAuditModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 opacity-50 transition-opacity" onclick="document.getElementById('exportAuditModal').classList.add('hidden')"></div>

            <div class="relative bg-white rounded-xl shadow-xl max-w-sm w-full p-6">
                <div class="flex justify-between items-center mb-5 border-b pb-3">
                    <h3 class="text-lg font-bold text-gray-800">🔍 Laporan Audit</h3>
                    <button onclick="document.getElementById('exportAuditModal').classList.add('hidden')" class="text-gray-400 hover:text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <form action="{{ route('laporan.audit') }}" method="POST" target="_blank">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Pilih Periode Audit</label>
                        <select name="periode_id" class="w-full border-gray-300 rounded-lg text-sm focus:border-slate-500 focus:ring-slate-500" required>
                            <option value="">-- Pilih Periode --</option>
                            @foreach($auditPeriodes as $ap)
                                <option value="{{ $ap->id }}">
                                    {{ $ap->nama_periode }} ({{ ucfirst($ap->status) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="bg-[#1e293b] text-white px-5 py-2.5 rounded-lg text-sm font-bold shadow hover:bg-[#0f172a] transition w-full flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Unduh PDF Audit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const colors = {
            slate: '#334155',
            indigo: '#4f46e5',
            emerald: '#059669',
            amber: '#d97706',
            red: '#dc2626',
            blue: '#2563eb',
            purple: '#7c3aed',
        };
        const palette = ['#334155', '#4f46e5', '#059669', '#d97706', '#dc2626', '#7c3aed', '#0891b2', '#be185d'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        const defaultOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
        };

        // 1. Tren Peminjaman
        new Chart(document.getElementById('trenChart'), {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Peminjaman',
                    data: @json($dataTren),
                    backgroundColor: colors.indigo + '20',
                    borderColor: colors.indigo,
                    borderWidth: 2,
                    borderRadius: 6,
                    barPercentage: 0.7,
                }]
            },
            options: {
                ...defaultOptions,
                scales: {
                    y: { beginAtZero: true, border: { display: false }, grid: { borderDash: [4, 4], color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } }
                }
            }
        });

        // 2. Kondisi Barang (Doughnut)
        new Chart(document.getElementById('kondisiChart'), {
            type: 'doughnut',
            data: {
                labels: @json($kondisiLabel),
                datasets: [{
                    data: @json($kondisiData),
                    backgroundColor: [colors.emerald, colors.amber, colors.red],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                ...defaultOptions,
                cutout: '68%',
                plugins: {
                    legend: { display: true, position: 'bottom', labels: { boxWidth: 12, usePointStyle: true, color: '#475569', font: { size: 11 }, padding: 16 } }
                }
            }
        });

        // 3. Kategori Barang (Doughnut)
        new Chart(document.getElementById('kategoriChart'), {
            type: 'doughnut',
            data: {
                labels: @json($labelKategori),
                datasets: [{
                    data: @json($dataKategori),
                    backgroundColor: palette,
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                ...defaultOptions,
                cutout: '65%',
                plugins: {
                    legend: { display: true, position: 'right', labels: { boxWidth: 12, usePointStyle: true, color: '#475569', font: { size: 11 } } }
                }
            }
        });

        // 4. Top Ruangan (Horizontal Bar)
        new Chart(document.getElementById('ruanganChart'), {
            type: 'bar',
            data: {
                labels: @json($labelRuang),
                datasets: [{
                    label: 'Booking',
                    data: @json($dataRuang),
                    backgroundColor: colors.purple + '20',
                    borderColor: colors.purple,
                    borderWidth: 2,
                    borderRadius: 6,
                    barThickness: 24,
                }]
            },
            options: {
                ...defaultOptions,
                indexAxis: 'y',
                scales: {
                    x: { beginAtZero: true, border: { display: false }, grid: { borderDash: [4, 4], color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                    y: { grid: { display: false }, ticks: { color: '#475569', font: { size: 11, weight: 'bold' } } }
                }
            }
        });

        // 5. Tren Booking Ruangan (Line)
        new Chart(document.getElementById('bookingTrenChart'), {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Booking Ruangan',
                    data: @json($dataBookingTren),
                    borderColor: colors.emerald,
                    backgroundColor: colors.emerald + '15',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: colors.emerald,
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                ...defaultOptions,
                scales: {
                    y: { beginAtZero: true, border: { display: false }, grid: { borderDash: [4, 4], color: '#f1f5f9' }, ticks: { color: '#94a3b8', font: { size: 11 } } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { size: 11 } } }
                }
            }
        });
    </script>
</x-app-layout>
