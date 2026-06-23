<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $barang->nama_barang }} - Detail Barang</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen font-[Inter]">

    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white">
        <div class="max-w-2xl mx-auto px-4 py-8">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-white/20 backdrop-blur-sm p-2 rounded-xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <p class="text-xs text-emerald-200 font-semibold uppercase tracking-wider">Sistem Inventaris TRPL</p>
                    <h1 class="text-2xl font-extrabold">{{ $barang->nama_barang }}</h1>
                </div>
            </div>

            <div class="flex flex-wrap gap-3 text-sm">
                <span class="bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-mono">
                    🏷️ {{ $barang->barcode }}
                </span>
                @if($barang->kategori)
                    <span class="bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium">
                        📦 {{ $barang->kategori->nama_kategori }}
                    </span>
                @endif
                @if($barang->ruangan)
                    <span class="bg-white/15 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-medium">
                        📍 {{ $barang->ruangan->nama_ruangan }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Detail Cards --}}
    <div class="max-w-2xl mx-auto px-4 -mt-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                @if($barang->kondisi == 'Baik')
                    <div class="text-2xl font-extrabold text-green-600">✓</div>
                    <div class="text-[10px] uppercase font-bold text-green-500 tracking-wider mt-1">Baik</div>
                @elseif($barang->kondisi == 'Rusak Ringan')
                    <div class="text-2xl font-extrabold text-yellow-600">⚠</div>
                    <div class="text-[10px] uppercase font-bold text-yellow-500 tracking-wider mt-1">Rusak Ringan</div>
                @else
                    <div class="text-2xl font-extrabold text-red-600">✕</div>
                    <div class="text-[10px] uppercase font-bold text-red-500 tracking-wider mt-1">Rusak Berat</div>
                @endif
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                @if($barang->status_peminjaman == 'Tersedia')
                    <div class="text-2xl font-extrabold text-emerald-600">●</div>
                    <div class="text-[10px] uppercase font-bold text-emerald-500 tracking-wider mt-1">Tersedia</div>
                @elseif($barang->status_peminjaman == 'Dipinjam')
                    <div class="text-2xl font-extrabold text-blue-600">●</div>
                    <div class="text-[10px] uppercase font-bold text-blue-500 tracking-wider mt-1">Dipinjam</div>
                @else
                    <div class="text-2xl font-extrabold text-orange-600">●</div>
                    <div class="text-[10px] uppercase font-bold text-orange-500 tracking-wider mt-1">Pemeliharaan</div>
                @endif
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <div class="text-sm font-extrabold text-gray-800">{{ $barang->kepemilikan ?? '-' }}</div>
                <div class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mt-1">Pemilik</div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <div class="text-sm font-extrabold text-gray-800">{{ $barang->merk ?? '-' }}</div>
                <div class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mt-1">Merk</div>
            </div>
        </div>
    </div>

    {{-- Detail Info --}}
    <div class="max-w-2xl mx-auto px-4 pb-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">Informasi Lengkap</h2>
            </div>
            <div class="divide-y divide-gray-50">
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Kode Inventaris</span>
                    <span class="text-sm font-mono font-bold text-indigo-600">{{ $barang->barcode }}</span>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Nama Barang</span>
                    <span class="text-sm font-bold text-gray-800">{{ $barang->nama_barang }}</span>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Kategori</span>
                    <span class="text-sm text-gray-800">{{ $barang->kategori->nama_kategori ?? '-' }}</span>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Lokasi Ruangan</span>
                    <span class="text-sm text-gray-800">{{ $barang->ruangan->nama_ruangan ?? '-' }}</span>
                </div>
                @if($barang->ruangan && $barang->ruangan->lokasi)
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Gedung / Lantai</span>
                    <span class="text-sm text-gray-800">{{ $barang->ruangan->lokasi }}</span>
                </div>
                @endif
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Merk</span>
                    <span class="text-sm text-gray-800">{{ $barang->merk ?? '-' }}</span>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Kepemilikan</span>
                    <span class="text-sm text-gray-800">{{ $barang->kepemilikan ?? '-' }}</span>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Kondisi</span>
                    <span class="text-sm font-bold {{ $barang->kondisi == 'Baik' ? 'text-green-600' : ($barang->kondisi == 'Rusak Ringan' ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ $barang->kondisi }}
                    </span>
                </div>
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Status</span>
                    <span class="text-sm font-bold {{ $barang->status_peminjaman == 'Tersedia' ? 'text-emerald-600' : ($barang->status_peminjaman == 'Dipinjam' ? 'text-blue-600' : 'text-orange-600') }}">
                        {{ $barang->status_peminjaman }}
                    </span>
                </div>
                @if($barang->harga)
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Harga</span>
                    <span class="text-sm text-gray-800">Rp {{ number_format($barang->harga, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($barang->deskripsi)
                <div class="px-6 py-3">
                    <span class="text-sm text-gray-500 block mb-1">Deskripsi</span>
                    <p class="text-sm text-gray-700">{{ $barang->deskripsi }}</p>
                </div>
                @endif
                @if($barang->terakhir_diperiksa_at)
                <div class="flex justify-between px-6 py-3">
                    <span class="text-sm text-gray-500">Terakhir Diperiksa</span>
                    <span class="text-sm text-gray-800">{{ \Carbon\Carbon::parse($barang->terakhir_diperiksa_at)->translatedFormat('d F Y, H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="mt-8 text-center text-xs text-gray-400">
            <p>Sistem Informasi Inventaris &mdash; Teknologi Rekayasa Perangkat Lunak</p>
            <p class="mt-1">&copy; {{ date('Y') }} InvenTrack</p>
        </div>
    </div>

</body>
</html>
