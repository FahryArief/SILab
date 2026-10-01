<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Ruangan</h2>
    </x-slot>

    <div class="py-12 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <a href="{{ route('ruangan.index') }}" class="text-blue-500 mb-4 inline-block">&larr; Kembali</a>

            <form action="{{ route('ruangan.update', $ruangan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-bold mb-1">Nama Ruangan</label>
                    <input type="text" name="nama_ruangan" value="{{ $ruangan->nama_ruangan }}" class="w-full border rounded p-2 mb-3" required>

                    <label class="block text-sm font-bold mb-1">Keterangan Singkat</label>
                    <input type="text" name="keterangan" value="{{ $ruangan->keterangan }}" class="w-full border rounded p-2 mb-3">
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Lokasi (Gedung/Lantai)</label>
        <input type="text" name="lokasi" value="{{ $ruangan->lokasi ?? '' }}" class="w-full border-gray-300 rounded-md shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-2">Kapasitas (Orang)</label>
        <input type="number" name="kapasitas" value="{{ $ruangan->kapasitas ?? '' }}" class="w-full border-gray-300 rounded-md shadow-sm">
    </div>
</div>
                    <label class="block text-sm font-bold mb-1">Fasilitas Ruangan</label>
                    <textarea name="fasilitas" rows="3" class="w-full border rounded p-2 mb-3">{{ $ruangan->fasilitas }}</textarea>

                    <div class="grid grid-cols-2 gap-4 mb-3 p-3 bg-gray-50 rounded border border-gray-200">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Ruangan Saat Ini</label>
                            <select name="jenis_ruangan" class="w-full border-gray-300 rounded-md shadow-sm">
                                <option value="Lab" {{ $ruangan->jenis_ruangan == 'Lab' ? 'selected' : '' }}>Lab</option>
                                <option value="Kelas" {{ $ruangan->jenis_ruangan == 'Kelas' ? 'selected' : '' }}>Kelas Biasa (bukan lab)</option>
                                <option value="Lainnya" {{ $ruangan->jenis_ruangan == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Berhenti Jadi Lab Sejak</label>
                            <input type="date" name="lab_nonaktif_sejak" value="{{ $ruangan->lab_nonaktif_sejak?->format('Y-m-d') }}" class="w-full border-gray-300 rounded-md shadow-sm">
                            <p class="text-[11px] text-gray-400 mt-1">Isi kalau ruangan ini dulunya lab lalu dialihfungsikan (mis. jadi kelas biasa). Laporan Data Ruangan Laboratorium tetap akan menampilkan ruangan ini untuk tahun-tahun sebelum tanggal ini.</p>
                        </div>
                    </div>

                    <label class="block text-sm font-bold mb-1">Foto Ruangan (Biarkan kosong jika tidak ingin ganti)</label>
                    @if($ruangan->foto_ruangan)
                        <img src="{{ asset('storage/foto_ruangan/' . $ruangan->foto_ruangan) }}" class="w-32 h-32 object-cover mb-2 rounded shadow">
                    @endif
                    <input type="file" name="foto_ruangan" class="w-full border rounded p-2" accept="image/*">
                </div>

                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Update Data Ruangan</button>
            </form>
        </div>
    </div>
</x-app-layout>
