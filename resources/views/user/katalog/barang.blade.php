<x-app-layout>
    <x-slot name="header">Katalog Alat Laboratorium</x-slot>

    <div class="max-w-7xl mx-auto mb-8">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Cari Alat Praktikum</h2>
                <p class="text-sm text-gray-500">Pilih alat yang tersedia dan ajukan peminjaman ke Operator Lab.</p>
            </div>
            <div class="w-full md:w-72">
                <input type="text" placeholder="Cari nama barang..." class="w-full border-gray-300 rounded-md text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        </div>
    </div>

    <form action="{{ route('peminjam.katalog.barang.store') }}" method="POST" enctype="multipart/form-data" id="formPeminjaman">
        @csrf
        <div class="max-w-7xl mx-auto flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-700">Daftar Item Spesifik</h3>
            <button type="button" onclick="openPinjamModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow-md transition">
                Pinjam Item Terpilih (<span id="countSelected">0</span>)
            </button>
        </div>

        <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($barangs as $barang)
                <label class="cursor-pointer">
                    <div class="relative bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col group hover:shadow-md hover:border-indigo-400 transition-all">
                        <div class="absolute top-3 right-3 z-10">
                            <input type="checkbox" name="barang_ids[]" value="{{ $barang->id }}" 
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 item-checkbox disabled:opacity-50 disabled:bg-gray-200 disabled:cursor-not-allowed" 
                                onchange="updateCount()"
                                {{ $barang->kondisi !== 'Baik' ? 'disabled' : '' }}>
                        </div>
                        
                        <div class="h-48 bg-gray-100 relative overflow-hidden isolate rounded-t-xl">
                            @if($barang->foto_barang)
                                <img src="{{ asset('storage/foto_barang/' . $barang->foto_barang) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 z-0" alt="{{ $barang->nama_barang }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-3">
                                <span class="text-white text-xs font-mono bg-black/40 px-2 py-1 rounded backdrop-blur-sm">{{ $barang->barcode }}</span>
                            </div>
                        </div>

                        <div class="p-4 flex-1 flex flex-col">
                            <div class="flex justify-between items-start mb-1">
                                <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">{{ $barang->kategori->nama_kategori ?? 'Umum' }}</p>
                                <span class="text-[10px] font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $barang->kepemilikan }}</span>
                            </div>
                            <h3 class="text-md font-bold text-gray-800 mb-1 line-clamp-2">{{ $barang->nama_barang }}</h3>
                            <p class="text-xs text-gray-500 mb-2">{{ $barang->merk ?? 'Tanpa Merk' }}</p>
                            
                            <div class="mt-auto border-t border-gray-100 pt-3 flex justify-between items-center">
                                <span class="text-xs text-gray-500">Kondisi:</span>
                                @if ($barang->kondisi == 'Baik')
                                    <span class="text-xs font-bold text-green-600">{{ $barang->kondisi }}</span>
                                @else
                                    <span class="text-xs font-bold text-red-600">Rusak</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </label>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-lg border border-gray-200 border-dashed">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                <p class="text-gray-500 font-medium">Belum ada barang yang tersedia di katalog.</p>
            </div>
        @endforelse
    </div>

    <div id="pinjamModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closePinjamModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Lengkapi Pengajuan Peminjaman</h3>

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Tgl Mulai</label>
                            <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Tgl Selesai</label>
                            <input type="date" name="tanggal_kembali" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Keperluan Penggunaan</label>
                        <textarea name="keperluan" rows="2" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Sebutkan mata kuliah / kegiatan terkait..." required></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Surat Peminjaman (Opsional)</label>
                        <input type="file" name="surat_peminjaman" class="w-full border-gray-300 rounded-md mt-1 text-sm bg-gray-50" accept=".pdf,image/*">
                        <p class="text-[10px] text-gray-400 mt-1">Jika peminjam bukan mahasiswa TRPL, wajib melampirkan surat.</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3 border-t pt-4">
                    <button type="button" onclick="closePinjamModal()" class="px-4 py-2 text-xs font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 rounded-md">BATAL</button>
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-xs font-bold shadow-md hover:bg-indigo-700 transition">KIRIM PENGAJUAN</button>
                </div>
            </div>
        </div>
    </div>
    </form>

    <script>
        function updateCount() {
            const count = document.querySelectorAll('.item-checkbox:checked').length;
            document.getElementById('countSelected').textContent = count;
        }

        function openPinjamModal() {
            const count = document.querySelectorAll('.item-checkbox:checked').length;
            if(count === 0) {
                alert('Pilih minimal 1 barang fisik untuk dipinjam!');
                return;
            }
            document.getElementById('pinjamModal').classList.remove('hidden');
        }

        function closePinjamModal() {
            document.getElementById('pinjamModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
