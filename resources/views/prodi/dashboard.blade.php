<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Koordinator Prodi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Ringkasan Sistem</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Widget 1: Tahun Ajaran / Jadwal -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-indigo-100 text-indigo-500 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Jadwal Kuliah Aktif</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $jadwalAktifCount }}</p>
                            </div>
                        </div>
                        <div class="mt-4 text-xs text-gray-500">
                            TA: {{ $tahunAjaranAktif ? $tahunAjaranAktif->nama_tahun . ' (' . $tahunAjaranAktif->semester . ')' : 'Belum di-set' }}
                        </div>
                    </div>

                    <!-- Widget 2: Total Ruangan -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Total Ruangan</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $totalRuangan }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Widget 3: Total Aset Barang -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Total Stok Barang</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $totalBarang }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Widget 4: Peminjaman/Booking Pending -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Pengajuan Pending</p>
                                <p class="text-2xl font-bold text-gray-800">{{ $peminjamanPending + $bookingPending }}</p>
                            </div>
                        </div>
                        <div class="mt-4 text-xs text-gray-500">
                            {{ $peminjamanPending }} Barang | {{ $bookingPending }} Ruangan
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRAFIK STATISTIK (Diambil dari Laporan) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart 1: Tren Peminjaman -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Tren Peminjaman Alat ({{ $tahunIni }})</h3>
                    <div class="relative h-72 w-full">
                        <canvas id="trenChart"></canvas>
                    </div>
                </div>

                <!-- Chart 2: Top 5 Ruangan -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Top 5 Ruangan Sering Digunakan</h3>
                    <div class="relative h-72 w-full flex justify-center">
                        <canvas id="ruangChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Data Tren Peminjaman
            const ctxTren = document.getElementById('trenChart').getContext('2d');
            new Chart(ctxTren, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
                    datasets: [{
                        label: 'Total Peminjaman',
                        data: {!! json_encode($dataTren) !!},
                        borderColor: 'rgb(79, 70, 229)', // indigo-600
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { stepSize: 1 } }
                    }
                }
            });

            // Data Top Ruangan
            const ctxRuang = document.getElementById('ruangChart').getContext('2d');
            new Chart(ctxRuang, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($labelRuang) !!},
                    datasets: [{
                        data: {!! json_encode($dataRuang) !!},
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.8)', // blue
                            'rgba(16, 185, 129, 0.8)', // emerald
                            'rgba(245, 158, 11, 0.8)', // amber
                            'rgba(239, 68, 68, 0.8)',  // red
                            'rgba(139, 92, 246, 0.8)'  // violet
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right' }
                    }
                }
            });
        });
    </script>
</x-app-layout>
