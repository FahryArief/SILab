<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Kategori</h2>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <a href="{{ route('kategori.index') }}" class="text-blue-500 mb-4 inline-block">&larr; Kembali</a>

            <form action="{{ route('kategori.update', $kategori->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm mb-1 font-bold">Nama Kategori</label>
                    <input type="text" name="nama_kategori" value="{{ $kategori->nama_kategori }}" class="w-full border rounded p-2" required>
                </div>
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Update Data</button>
            </form>
        </div>
    </div>
</x-app-layout>
