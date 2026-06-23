<x-app-layout>
    <x-slot name="header">Tambah Ruangan</x-slot>

    <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-lg p-8 shadow-sm">
        <form action="{{ route('ruangan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Ruangan</label>
                    <input type="text" name="nama_ruangan" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Contoh: Lab Komputer 1">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Keterangan / Lokasi</label>
                    <input type="text" name="keterangan" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: Gedung B Lt. 3">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kapasitas Orang</label>
                    <input type="number" name="kapasitas" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Contoh: 40">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Fasilitas (Pisahkan dengan koma)</label>
                    <textarea name="fasilitas" rows="3" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="PC, AC, Proyektor, WiFi"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Ruangan</label>
                    <input type="file" name="foto_ruangan" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('ruangan.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900">Batal</a>
                <button type="submit" class="bg-[#1e293b] text-white px-6 py-2 rounded-md text-sm font-bold">Simpan Ruangan</button>
            </div>
        </form>
    </div>
</x-app-layout>
