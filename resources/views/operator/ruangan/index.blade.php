<x-app-layout>
    <x-slot name="header">
        Data Ruang
    </x-slot>

    <div class="max-w-7xl mx-auto" x-data="{
        search: '',
        filterStatus: '',
        isVisible(el) {
            const nama = (el.dataset.nama || '').toLowerCase();
            const lokasi = (el.dataset.lokasi || '').toLowerCase();
            const status = el.dataset.status || '';
            
            const q = this.search.toLowerCase();
            const matchSearch = !q || nama.includes(q) || lokasi.includes(q);
            const matchStatus = !this.filterStatus || status === this.filterStatus;
            
            return matchSearch && matchStatus;
        }
    }">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="flex flex-1 w-full max-w-lg">
                <input type="text" x-model="search" placeholder="Cari ruang..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <select x-model="filterStatus" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua Status</option>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Digunakan">Digunakan</option>
                </select>
                <button type="button" onclick="document.getElementById('importModalRuangan').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-bold flex items-center transition">
                    Import Excel
                </button>
                <a href="{{ route('ruangan.export') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-bold flex items-center transition">
                    Export / Template
                </a>
                <a href="{{ route('ruangan.create') }}" class="bg-[#1e293b] hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-bold flex items-center whitespace-nowrap transition">
                    <span class="mr-1.5">+</span> Tambah Ruang
                </a>
            </div>
        </div>

       <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($ruangans as $ruangan)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-shadow"
                 data-nama="{{ $ruangan->nama_ruangan }}"
                 data-lokasi="{{ $ruangan->lokasi ?? '' }}"
                 data-status="{{ $ruangan->status_label }}"
                 x-show="isVisible($el)"
                 x-transition.opacity>

                <div class="relative h-48 w-full overflow-hidden bg-gray-100">
                    @if($ruangan->foto_ruangan)
                        <img src="{{ asset('storage/foto_ruangan/' . $ruangan->foto_ruangan) }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             alt="{{ $ruangan->nama_ruangan }}">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-xs font-medium">Tidak ada foto</span>
                        </div>
                    @endif

                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm {{ $ruangan->status_label == 'Tersedia' ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-white' }}">
                            {{ $ruangan->status_label }}
                        </span>
                    </div>
                </div>

                <div class="p-5 flex-1">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="text-lg font-bold text-gray-800">{{ $ruangan->nama_ruangan }}</h3>
                        <span class="text-[10px] font-mono text-gray-400 mt-1">{{ $ruangan->kode_ruangan ?? 'RM-' . str_pad($ruangan->id, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <p class="text-sm text-gray-500 mb-4">{{ $ruangan->keterangan ?? 'Gedung Teknologi RPL' }}</p>

                    <div class="grid grid-cols-2 gap-2 mb-4">
                        <div class="flex items-center text-gray-600 text-xs">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Cap: {{ $ruangan->kapasitas ?? '0' }}
                        </div>
                        <div class="flex items-center text-gray-600 text-xs">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $ruangan->lokasi ?? 'Polinela' }}
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <div class="flex flex-wrap gap-1">
                            @if($ruangan->fasilitas)
                                @foreach(explode(',', $ruangan->fasilitas) as $item)
                                    <span class="text-[9px] text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded font-medium">{{ trim($item) }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-4 border-t border-gray-100 bg-gray-50/50">
                    @if($ruangan->kode_ruangan)
                    <a href="{{ route('ruangan.qrcode', $ruangan->id) }}" target="_blank" class="py-3 text-center text-xs font-bold text-teal-600 hover:bg-white transition-colors border-r border-gray-100">
                        🖨️ QR
                    </a>
                    <a href="{{ route('scan.ruangan', $ruangan->kode_ruangan) }}" target="_blank" class="py-3 text-center text-xs font-bold text-purple-600 hover:bg-white transition-colors border-r border-gray-100">
                        📋 PREVIEW
                    </a>
                    @else
                    <span class="py-3 text-center text-xs font-bold text-gray-400 border-r border-gray-100">-</span>
                    <span class="py-3 text-center text-xs font-bold text-gray-400 border-r border-gray-100">-</span>
                    @endif
                    <a href="{{ route('admin.jadwal_kuliah.ruangan', $ruangan->id) }}" class="py-3 text-center text-xs font-bold text-gray-600 hover:bg-white transition-colors border-r border-gray-100">
                        JADWAL
                    </a>
                    <a href="{{ route('ruangan.edit', $ruangan->id) }}" class="py-3 text-center text-xs font-bold text-indigo-600 hover:bg-white transition-colors">
                        EDIT
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modal Import -->
    <div id="importModalRuangan" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('importModalRuangan').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('ruangan.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Import Data Ruangan
                                </h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-3">
                                        Silakan unggah file Excel/CSV yang telah diisi sesuai format.
                                    </p>
                                    <input type="file" name="file" accept=".xlsx, .xls, .csv" required class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Mulai Import
                        </button>
                        <button type="button" onclick="document.getElementById('importModalRuangan').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
