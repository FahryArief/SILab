<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Data Barang
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('barang.index') }}" class="text-blue-500 hover:text-blue-700 text-sm font-medium">
                            &larr; Kembali ke Daftar Barang
                        </a>
                    </div>

                    {{-- Info Barang --}}
                    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 mb-6">
                        <div class="flex items-center gap-3">
                            @if($barang->foto_barang)
                                <img src="{{ asset('storage/foto_barang/' . $barang->foto_barang) }}" class="w-14 h-14 object-cover rounded-lg shadow border border-indigo-200">
                            @else
                                <div class="w-14 h-14 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-300 text-xs font-bold">N/A</div>
                            @endif
                            <div>
                                <h3 class="font-bold text-indigo-800">{{ $barang->nama_barang }}</h3>
                                <p class="text-xs text-indigo-500 font-mono">{{ $barang->barcode }}</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('barang.update', $barang->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Baris 1: Nama & Merk --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Nama Barang / Alat</label>
                                <input type="text" name="nama_barang" value="{{ old('nama_barang', $barang->nama_barang) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Merk / Tipe</label>
                                <input type="text" name="merk" value="{{ old('merk', $barang->merk) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="Contoh: Logitech B100">
                            </div>
                        </div>

                        {{-- Baris 2: Kategori & Ruangan --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                <select name="kategori_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                                    @foreach($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}" {{ $barang->kategori_id == $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Ruangan</label>
                                <select name="ruangan_id" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                                    @foreach($ruangans as $ruangan)
                                        <option value="{{ $ruangan->id }}" {{ $barang->ruangan_id == $ruangan->id ? 'selected' : '' }}>
                                            {{ $ruangan->nama_ruangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Baris 3: Kode Inventaris (Editable) & Pemilik --}}
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">
                                    Kode Inventaris (Barcode)
                                    <span class="text-xs text-gray-400 font-normal ml-1">— bisa diedit jika perlu</span>
                                </label>
                                <input type="text" name="barcode" value="{{ old('barcode', $barang->barcode) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700 font-mono" required>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kepemilikan</label>
                                <select name="kepemilikan" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                                    <option value="Prodi" {{ old('kepemilikan', $barang->kepemilikan) == 'Prodi' ? 'selected' : '' }}>Prodi</option>
                                    <option value="Lab" {{ old('kepemilikan', $barang->kepemilikan) == 'Lab' ? 'selected' : '' }}>Lab</option>
                                </select>
                            </div>
                        </div>

                        {{-- Baris 4: Kondisi, Status, Harga --}}
                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Kondisi</label>
                                <select name="kondisi" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                                    <option value="Baik" {{ old('kondisi', $barang->kondisi) == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ old('kondisi', $barang->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ old('kondisi', $barang->kondisi) == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Status Peminjaman</label>
                                <select name="status_peminjaman" class="shadow border rounded w-full py-2 px-3 text-gray-700" required>
                                    <option value="Tersedia" {{ old('status_peminjaman', $barang->status_peminjaman) == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="Dipinjam" {{ old('status_peminjaman', $barang->status_peminjaman) == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                    <option value="Pemeliharaan" {{ old('status_peminjaman', $barang->status_peminjaman) == 'Pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-gray-700 text-sm font-bold mb-2">Harga (Rp)</label>
                                <input type="number" name="harga" value="{{ old('harga', $barang->harga) }}" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="Opsional">
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                            <textarea name="deskripsi" rows="3" class="shadow border rounded w-full py-2 px-3 text-gray-700" placeholder="Catatan atau deskripsi tambahan...">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                        </div>

                        {{-- Foto --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Foto Barang <span class="text-xs text-gray-400 font-normal">(biarkan kosong jika tidak diubah)</span></label>
                            @if($barang->foto_barang)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/foto_barang/' . $barang->foto_barang) }}" class="w-32 h-32 object-cover rounded shadow border">
                                </div>
                            @endif
                            <input type="file" name="foto_barang" class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-gray-50" accept="image/*">
                        </div>

                        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-100">
                            <a href="{{ route('barang.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
                            <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 px-6 rounded shadow transition">
                                Update Data Barang
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
