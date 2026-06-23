<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Kategori</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h3 class="font-bold mb-4">Tambah Kategori Baru</h3>
            <form action="{{ route('kategori.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm mb-1">Nama Kategori</label>
                    <input type="text" name="nama_kategori" class="w-full border rounded p-2" required>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded w-full">Simpan</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm md:col-span-2">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="border-b py-2">No</th>
                        <th class="border-b py-2">Nama Kategori</th>
                        <th class="border-b py-2 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kategoris as $index => $kategori)
                    <tr>
                        <td class="border-b py-2">{{ $index + 1 }}</td>
                        <td class="border-b py-2">{{ $kategori->nama_kategori }}</td>
                        <td class="border-b py-2 text-right">
                            <a href="{{ route('kategori.edit', $kategori->id) }}" class="text-yellow-500 hover:text-yellow-700 mr-2">Edit</a>
                            <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
