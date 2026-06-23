<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Jadwal Kuliah: {{ $ruangan->nama_ruangan }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">{{ $ruangan->kode_ruangan ?? '' }} &bull; Kapasitas: {{ $ruangan->kapasitas ?? '-' }} orang</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.jadwal_kuliah.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg text-sm transition">
                    &larr; Semua Jadwal
                </a>
                <a href="{{ route('ruangan.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg text-sm transition">
                    Daftar Ruangan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm">
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if($tahunAjaranAktif)
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded shadow-sm">
                    <p>Tahun Ajaran Aktif: <strong>{{ $tahunAjaranAktif->nama_tahun }} ({{ $tahunAjaranAktif->semester }})</strong></p>
                </div>
            @else
                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded shadow-sm">
                    <p>Tidak ada Tahun Ajaran aktif. Data jadwal mungkin kosong.</p>
                </div>
            @endif

            {{-- Tabel Jadwal --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl mb-6">
                <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Jadwal Penggunaan Ruangan</h3>
                    <span class="text-xs text-gray-400">{{ $jadwals->count() }} jadwal terdaftar</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Kuliah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosen</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $currentHari = ''; @endphp
                            @forelse ($jadwals as $jadwal)
                            <tr class="{{ $jadwal->hari !== $currentHari ? 'border-t-2 border-indigo-100' : '' }} hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($jadwal->hari !== $currentHari)
                                        <span class="text-sm font-bold text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">{{ $jadwal->hari }}</span>
                                        @php $currentHari = $jadwal->hari; @endphp
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">
                                    {{ \Carbon\Carbon::parse($jadwal->waktu_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->waktu_selesai)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $jadwal->mata_kuliah }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $jadwal->dosen ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <form action="{{ route('admin.jadwal_kuliah.destroy', $jadwal->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus jadwal ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-bold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm italic">
                                    Belum ada jadwal kuliah untuk ruangan ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quick Add Form --}}
            @if($tahunAjaranAktif)
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-bold text-gray-800">Tambah Jadwal untuk {{ $ruangan->nama_ruangan }}</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.jadwal_kuliah.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ruangan_id" value="{{ $ruangan->id }}">
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Hari</label>
                                <select name="hari" class="w-full border-gray-300 rounded-md text-sm" required>
                                    <option value="Senin">Senin</option>
                                    <option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option>
                                    <option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option>
                                    <option value="Sabtu">Sabtu</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mulai</label>
                                <input type="time" name="waktu_mulai" class="w-full border-gray-300 rounded-md text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Selesai</label>
                                <input type="time" name="waktu_selesai" class="w-full border-gray-300 rounded-md text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mata Kuliah</label>
                                <input type="text" name="mata_kuliah" class="w-full border-gray-300 rounded-md text-sm" placeholder="Contoh: Basis Data" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dosen</label>
                                <input type="text" name="dosen" class="w-full border-gray-300 rounded-md text-sm" placeholder="Opsional">
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-lg shadow transition text-sm">
                                Tambah Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
