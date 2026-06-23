<x-app-layout>
    <x-slot name="header">
        Dashboard Mahasiswa
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="bg-indigo-600 rounded-lg p-8 shadow-md text-white flex flex-col md:flex-row justify-between items-center relative overflow-hidden">
            <div class="relative z-10">
                <h2 class="text-2xl font-bold mb-2">Halo, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-indigo-100 text-sm max-w-lg">Selamat datang di Portal Peminjaman InvenTrack. Cek status pengajuanmu atau telusuri katalog alat dan ruangan yang tersedia di Laboratorium.</p>
                <div class="mt-6 flex gap-3">
                    <button class="bg-white text-indigo-600 px-4 py-2 rounded-md text-sm font-bold shadow hover:bg-indigo-50 transition">
                        Lihat Katalog Alat
                    </button>
                    <button class="bg-indigo-500 text-white px-4 py-2 rounded-md text-sm font-bold shadow hover:bg-indigo-400 transition border border-indigo-400">
                        Cek Jadwal Ruangan
                    </button>
                </div>
            </div>
            <svg class="absolute right-0 top-0 h-full w-64 text-indigo-500 opacity-50 transform translate-x-16" fill="currentColor" viewBox="0 0 100 100"><circle cx="50" cy="50" r="50"></circle></svg>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center">
                <div class="p-4 bg-amber-50 text-amber-500 rounded-lg mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase">Tanggungan Alat</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $tanggungan_barang }} <span class="text-sm font-medium text-gray-400">Sedang Dipinjam</span></h3>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center">
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-lg mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase">Ruangan Aktif</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $tanggungan_ruang }} <span class="text-sm font-medium text-gray-400">Sedang Digunakan</span></h3>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-100 bg-gray-50">
                <h3 class="font-bold text-gray-700 text-sm">Status Pengajuan Terakhir Anda</h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-gray-100">
                    @forelse($my_peminjamans as $pinjam)
                        <li class="p-4 flex justify-between items-center hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full mr-3
                                    {{ $pinjam->status == 'pending' ? 'bg-amber-400' :
                                       ($pinjam->status == 'divalidasi_teknisi' ? 'bg-indigo-400' :
                                       ($pinjam->status == 'disetujui' ? 'bg-emerald-500' : 'bg-gray-300')) }}"></div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">
                                        Pinjam: {{ $pinjam->barangs->pluck('nama_barang')->unique()->implode(', ') }}
                                        <span class="text-xs text-gray-400">({{ $pinjam->barangs->count() }} item)</span>
                                    </p>
                                    <p class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase
                                {{ $pinjam->status == 'pending' ? 'text-amber-600' :
                                   ($pinjam->status == 'divalidasi_teknisi' ? 'text-indigo-600' :
                                   ($pinjam->status == 'disetujui' ? 'text-emerald-600' : 'text-gray-500')) }}">
                                {{ str_replace('_', ' ', $pinjam->status) }}
                            </span>
                        </li>
                    @empty
                    @endforelse

                    @forelse($my_bookings as $booking)
                        <li class="p-4 flex justify-between items-center hover:bg-gray-50">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full mr-3 {{ $booking->status == 'pending' ? 'bg-amber-400' : ($booking->status == 'disetujui' ? 'bg-emerald-500' : 'bg-gray-300') }}"></div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">Booking: {{ $booking->ruangan->nama_ruangan }}</p>
                                    <p class="text-[10px] text-gray-500">{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y') }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-bold uppercase {{ $booking->status == 'pending' ? 'text-amber-600' : ($booking->status == 'disetujui' ? 'text-emerald-600' : 'text-gray-500') }}">{{ $booking->status }}</span>
                        </li>
                    @empty
                    @endforelse

                    @if($my_peminjamans->isEmpty() && $my_bookings->isEmpty())
                        <li class="p-6 text-center text-sm text-gray-400 italic">Belum ada riwayat pengajuan apapun.</li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
