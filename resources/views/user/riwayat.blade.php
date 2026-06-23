<x-app-layout>
    <x-slot name="header">Riwayat Pengajuan Saya</x-slot>

    <div class="max-w-7xl mx-auto space-y-8">

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100 flex items-center">
                <svg class="w-5 h-5 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <h3 class="font-bold text-indigo-900">Riwayat Peminjaman Alat / Barang</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Barang Dipinjam</th>
                            <th class="px-6 py-4 text-center">Jumlah Item</th>
                            <th class="px-6 py-4">Tanggal Pinjam</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($peminjamans as $pinjam)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($pinjam->barangs as $brg)
                                        <span class="text-[10px] font-mono bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded border border-indigo-100">{{ $brg->barcode }}</span>
                                    @endforeach
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $pinjam->barangs->pluck('nama_barang')->unique()->implode(', ') }}</div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-600">{{ $pinjam->barangs->count() }}</td>
                            <td class="px-6 py-4 text-xs text-gray-600">
                                {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($pinjam->status == 'pending')
                                    <span class="px-2 py-1 bg-amber-100 text-amber-600 text-[10px] font-bold rounded uppercase">Menunggu</span>
                                @elseif($pinjam->status == 'divalidasi_teknisi')
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-600 text-[10px] font-bold rounded uppercase">Divalidasi Teknisi</span>
                                @elseif($pinjam->status == 'disetujui')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-600 text-[10px] font-bold rounded uppercase">Sedang Dipinjam</span>
                                @elseif($pinjam->status == 'ditolak')
                                    <span class="px-2 py-1 bg-red-100 text-red-600 text-[10px] font-bold rounded uppercase">Ditolak</span> <span class="text-xs text-red-600 font-mono bg-red-50 text-red-700 px-2 py-0.5 rounded border border-red-100">Catatan : {{ $pinjam->catatan_admin }}</span>
                                @elseif($pinjam->status == 'dikembalikan')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase">Selesai/Dikembalikan</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm italic">Belum ada riwayat peminjaman barang.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden mb-8">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex items-center">
                <svg class="w-5 h-5 text-slate-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <h3 class="font-bold text-slate-800">Riwayat Booking Ruangan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Nama Ruangan</th>
                            <th class="px-6 py-4">Tanggal Booking</th>
                            <th class="px-6 py-4">Jam Penggunaan</th>
                            <th class="px-6 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-bold text-gray-800 text-sm">{{ $booking->ruangan->nama_ruangan }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}</td>
                            <td class="px-6 py-4">
                                @if($booking->status == 'pending')
                                    <span class="px-2 py-1 bg-amber-100 text-amber-600 text-[10px] font-bold rounded uppercase">Menunggu</span>
                                @elseif($booking->status == 'disetujui')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-600 text-[10px] font-bold rounded uppercase">Disetujui</span>
                                @elseif($booking->status == 'ditolak')
                                    <span class="px-2 py-1 bg-red-100 text-red-600 text-[10px] font-bold rounded uppercase">Ditolak</span>
                                @elseif($booking->status == 'selesai')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase">Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400 text-sm italic">Belum ada riwayat booking ruangan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>