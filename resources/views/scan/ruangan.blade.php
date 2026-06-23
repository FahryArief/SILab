<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $ruangan->nama_ruangan }} - Inventaris Ruangan</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen font-[Inter]">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-700 text-white">
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-indigo-200 font-semibold uppercase tracking-wider">Sistem Inventaris TRPL</p>
                    <h1 class="text-2xl font-extrabold">{{ $ruangan->nama_ruangan }}</h1>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 text-sm">
                @if($ruangan->lokasi)
                    <span class="bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium">
                        📍 {{ $ruangan->lokasi }}
                    </span>
                @endif
                @if($ruangan->kapasitas)
                    <span class="bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium">
                        👥 Kapasitas: {{ $ruangan->kapasitas }} orang
                    </span>
                @endif
                <span class="bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-mono">
                    🏷️ {{ $ruangan->kode_ruangan }}
                </span>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="max-w-4xl mx-auto px-4 -mt-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <div class="text-2xl font-extrabold text-gray-800">{{ $totalBarang }}</div>
                <div class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mt-1">Total Item</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-green-100 p-4 text-center">
                <div class="text-2xl font-extrabold text-green-600">{{ $barangBaik }}</div>
                <div class="text-[10px] uppercase font-bold text-green-500 tracking-wider mt-1">Baik</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-yellow-100 p-4 text-center">
                <div class="text-2xl font-extrabold text-yellow-600">{{ $barangRusakRingan }}</div>
                <div class="text-[10px] uppercase font-bold text-yellow-500 tracking-wider mt-1">Rusak Ringan</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-red-100 p-4 text-center">
                <div class="text-2xl font-extrabold text-red-600">{{ $barangRusakBerat }}</div>
                <div class="text-[10px] uppercase font-bold text-red-500 tracking-wider mt-1">Rusak Berat</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-blue-100 p-4 text-center">
                <div class="text-2xl font-extrabold text-blue-600">{{ $barangDipinjam }}</div>
                <div class="text-[10px] uppercase font-bold text-blue-500 tracking-wider mt-1">Dipinjam</div>
            </div>
        </div>
    </div>

    {{-- Daftar Barang --}}
    <div class="max-w-4xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800">Daftar Barang di Ruangan Ini</h2>
                <span class="text-xs text-gray-400">Terakhir diakses: {{ now()->format('d M Y, H:i') }}</span>
            </div>

            @if($barangs->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3 font-semibold">Kode</th>
                                <th class="px-6 py-3 font-semibold">Barang</th>
                                <th class="px-6 py-3 font-semibold">Merk</th>
                                <th class="px-6 py-3 font-semibold">Pemilik</th>
                                <th class="px-6 py-3 font-semibold">Kondisi</th>
                                <th class="px-6 py-3 font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($barangs as $barang)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3">
                                    <span class="font-mono text-xs text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ $barang->barcode }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        @if($barang->foto_barang)
                                            <img src="{{ asset('storage/foto_barang/' . $barang->foto_barang) }}" class="w-9 h-9 rounded object-cover border border-gray-100">
                                        @else
                                            <div class="w-9 h-9 bg-gray-100 rounded flex items-center justify-center text-gray-400 text-[8px]">N/A</div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-bold text-gray-800">{{ $barang->nama_barang }}</div>
                                            @if($barang->deskripsi)
                                                <div class="text-[10px] text-gray-400 max-w-[200px] truncate">{{ $barang->deskripsi }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-600">{{ $barang->merk ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded {{ $barang->kepemilikan == 'Prodi' ? 'bg-purple-50 text-purple-600' : 'bg-teal-50 text-teal-600' }}">
                                        {{ $barang->kepemilikan }}
                                    </span>
                                </td>
                                <td class="px-6 py-3">
                                    @if($barang->kondisi == 'Baik')
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-green-100 text-green-600">✓ Baik</span>
                                    @elseif($barang->kondisi == 'Rusak Ringan')
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-yellow-100 text-yellow-600">⚠ Rusak Ringan</span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-red-100 text-red-600">✕ Rusak Berat</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3">
                                    @if($barang->status_peminjaman == 'Tersedia')
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-emerald-100 text-emerald-600">Tersedia</span>
                                    @elseif($barang->status_peminjaman == 'Dipinjam')
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-blue-100 text-blue-600">Dipinjam</span>
                                    @else
                                        <span class="text-[10px] font-bold px-2 py-1 rounded bg-orange-100 text-orange-600">Pemeliharaan</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <p class="text-gray-400 text-sm">Belum ada barang tercatat di ruangan ini.</p>
                </div>
            @endif
        </div>

        {{-- Keterangan Ruangan --}}
        @if($ruangan->keterangan)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mt-4 p-6">
            <h3 class="font-bold text-gray-800 text-sm mb-2">Keterangan Ruangan</h3>
            <p class="text-sm text-gray-600">{{ $ruangan->keterangan }}</p>
        </div>
        @endif

        <div class="mt-8 text-center text-xs text-gray-400">
            <p>Sistem Informasi Inventaris &mdash; Teknologi Rekayasa Perangkat Lunak</p>
            <p class="mt-1">&copy; {{ date('Y') }} InvenTrack</p>
        </div>
    </div>

</body>
</html>
