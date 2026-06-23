<x-app-layout>
    <x-slot name="header">
        Dashboard Operator
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="bg-white rounded-lg p-6 border border-gray-200 shadow-sm flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Selamat datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-sm text-gray-500 mt-1">Berikut adalah ringkasan sistem inventaris TRPL saat ini.</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Waktu Sistem</p>
                <p class="text-lg font-mono text-indigo-600 font-bold">{{ date('d M Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center">
                <div class="p-4 bg-blue-50 text-blue-600 rounded-lg mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase">Total Item Barang</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $total_barang }} <span class="text-sm font-medium text-gray-400">Unit</span></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm flex items-center">
                <div class="p-4 bg-emerald-50 text-emerald-600 rounded-lg mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase">Total Ruangan</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $total_ruangan }} <span class="text-sm font-medium text-gray-400">Ruang</span></h3>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg border {{ $total_pending > 0 ? 'border-amber-400' : 'border-gray-200' }} shadow-sm flex items-center relative overflow-hidden">
                @if($total_pending > 0)
                    <div class="absolute top-0 right-0 w-2 h-full bg-amber-400"></div>
                @endif
                <div class="p-4 bg-amber-50 text-amber-500 rounded-lg mr-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase">Perlu Tindakan</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $total_pending }} <span class="text-sm font-medium text-gray-400">Pengajuan</span></h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-700 text-sm">Peminjaman Barang Terbaru</h3>
                    <a href="{{ route('peminjaman.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-100">
                        @forelse($recent_peminjamans as $pinjam)
                            <li class="p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $pinjam->user ? $pinjam->user->name : $pinjam->nama_peminjam }}</p>
                                        <p class="text-xs text-gray-500">{{ $pinjam->barangs->count() }} item: {{ $pinjam->barangs->pluck('barcode')->implode(', ') }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $pinjam->created_at->diffForHumans() }}</p>
                                    </div>
                                    <div>
                                        @if($pinjam->status == 'pending')
                                            <span class="px-2 py-1 bg-amber-100 text-amber-600 text-[10px] font-bold rounded uppercase">Pending</span>
                                        @elseif($pinjam->status == 'disetujui')
                                            <span class="px-2 py-1 bg-emerald-100 text-emerald-600 text-[10px] font-bold rounded uppercase">Dipinjam</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase">{{ $pinjam->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="p-6 text-center text-sm text-gray-400 italic">Belum ada aktivitas peminjaman barang.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-700 text-sm">Booking Ruangan Terbaru</h3>
                    <a href="{{ route('booking.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Lihat Semua &rarr;</a>
                </div>
                <div class="p-0">
                    <ul class="divide-y divide-gray-100">
                        @forelse($recent_bookings as $booking)
                            <li class="p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-bold text-gray-800">{{ $booking->user ? $booking->user->name : $booking->nama_peminjam }}</p>
                                        <p class="text-xs text-gray-500">{{ $booking->ruangan->nama_ruangan }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y') }} ({{ substr($booking->waktu_mulai, 0, 5) }})</p>
                                    </div>
                                    <div>
                                        @if($booking->status == 'pending')
                                            <span class="px-2 py-1 bg-amber-100 text-amber-600 text-[10px] font-bold rounded uppercase">Pending</span>
                                        @elseif($booking->status == 'disetujui')
                                            <span class="px-2 py-1 bg-emerald-100 text-emerald-600 text-[10px] font-bold rounded uppercase">Disetujui</span>
                                        @else
                                            <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase">{{ $booking->status }}</span>
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="p-6 text-center text-sm text-gray-400 italic">Belum ada aktivitas booking ruangan.</li>
                        @endforelse
                    </ul>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
