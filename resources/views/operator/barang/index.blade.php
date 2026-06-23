<x-app-layout>
    <x-slot name="header">Data Barang</x-slot>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="max-w-7xl mx-auto" x-data="{
        search: '',
        filterKategori: '',
        filterRuangan: '',
        filterStatus: '',
        selectedIds: [],
        toggleSelect(id) {
            const i = this.selectedIds.indexOf(id);
            if (i === -1) this.selectedIds.push(id);
            else this.selectedIds.splice(i, 1);
        },
        isVisible(el) {
            const nama = el.dataset.nama || '';
            const kategori = el.dataset.kategori || '';
            const ruangan = el.dataset.ruangan || '';
            const status = el.dataset.status || '';
            const matchSearch = !this.search || 
                                nama.toLowerCase().includes(this.search.toLowerCase()) || 
                                (el.dataset.search || '').toLowerCase().includes(this.search.toLowerCase());
            const matchKategori = !this.filterKategori || kategori === this.filterKategori;
            const matchRuangan = !this.filterRuangan || ruangan.includes(this.filterRuangan);
            const matchStatus = !this.filterStatus || status.includes(this.filterStatus);
            return matchSearch && matchKategori && matchRuangan && matchStatus;
        },
        batchPrintSelected() {
            if (this.selectedIds.length === 0) { alert('Pilih minimal 1 item terlebih dahulu.'); return; }
            window.open('/operator/barang-batch/barcode?ids=' + this.selectedIds.join(','), '_blank');
        }
    }">
        {{-- Flash --}}
        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg mb-4 text-sm font-semibold" x-data x-init="setTimeout(() => $el.remove(), 4000)">
            ✓ {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm font-semibold">{{ session('error') }}</div>
        @endif

        {{-- Toolbar --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-3">
            <div class="flex flex-1 w-full max-w-md">
                <input type="text" x-model="search" placeholder="Cari barang..." class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div class="flex gap-2 w-full md:w-auto flex-wrap items-center">
                <select x-model="filterRuangan" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua Ruangan</option>
                    @foreach($ruangans as $ruang)
                        <option value="{{ $ruang->nama_ruangan }}">{{ $ruang->nama_ruangan }}</option>
                    @endforeach
                </select>
                <select x-model="filterKategori" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->nama_kategori }}">{{ $kat->nama_kategori }}</option>
                    @endforeach
                </select>
                <select x-model="filterStatus" class="border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">Semua Status</option>
                    <option value="Tersedia">Tersedia</option>
                    <option value="Dipinjam">Dipinjam</option>
                </select>
                <button type="button" onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-sm font-bold flex items-center transition">
                    Import Excel
                </button>
                <a href="{{ route('barang.export') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-bold flex items-center transition">
                    Export / Template
                </a>
                <a href="{{ route('barang.create') }}" class="bg-[#1e293b] hover:bg-gray-800 text-white px-4 py-2 rounded-md text-sm font-bold flex items-center whitespace-nowrap transition">
                    <span class="mr-1.5">+</span> Tambah Barang
                </a>
            </div>
        </div>

        {{-- Selected Actions Bar --}}
        <div x-show="selectedIds.length > 0" x-transition class="bg-indigo-50 border border-indigo-200 rounded-lg px-4 py-3 mb-4 flex items-center justify-between">
            <span class="text-sm font-bold text-indigo-700">
                <span x-text="selectedIds.length"></span> item dipilih
            </span>
            <div class="flex gap-2">
                <button @click="batchPrintSelected()" class="bg-indigo-600 text-white px-4 py-1.5 rounded-md text-xs font-bold hover:bg-indigo-700 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak QR Label
                </button>
                <button @click="selectedIds = []" class="text-xs text-gray-500 hover:text-gray-800 font-semibold px-3 py-1.5">Batal Pilih</button>
            </div>
        </div>

        {{-- Inline Toast --}}
        <div id="saveToast" class="fixed top-4 right-4 z-[100] hidden">
            <div class="bg-emerald-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span id="toastMsg">Tersimpan!</span>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-3 py-3 font-semibold w-8"></th>
                        <th class="px-3 py-3 font-semibold w-8"></th>
                        <th class="px-3 py-3 font-semibold">Nama Barang</th>
                        <th class="px-3 py-3 font-semibold">Kategori</th>
                        <th class="px-3 py-3 font-semibold">Total</th>
                        <th class="px-3 py-3 font-semibold">Kondisi</th>
                        <th class="px-3 py-3 font-semibold">Status</th>
                        <th class="px-3 py-3 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                @forelse ($barangs as $nama_barang => $items)
                @php
                    $firstItem = $items->first();
                    $totalBaik = $items->where('kondisi', 'Baik')->count();
                    $totalRusak = $items->whereIn('kondisi', ['Rusak Ringan', 'Rusak Berat'])->count();
                    $totalTersedia = $items->where('status_peminjaman', 'Tersedia')->count();
                    $totalDipinjam = $items->where('status_peminjaman', 'Dipinjam')->count();
                    $statusList = $items->pluck('status_peminjaman')->unique()->implode(',');
                    $ruanganList = $items->pluck('ruangan.nama_ruangan')->filter()->unique()->implode(',');
                    $searchList = $items->map(function($i) { return $i->barcode . ' ' . $i->merk; })->implode(' ');
                @endphp
                <tbody class="divide-y divide-gray-100" x-data="{ open: false }"
                       data-nama="{{ $nama_barang }}"
                       data-kategori="{{ $firstItem->kategori->nama_kategori ?? '' }}"
                       data-ruangan="{{ $ruanganList }}"
                       data-search="{{ $searchList }}"
                       data-status="{{ $statusList }}"
                       x-show="isVisible($el)" x-transition.opacity>
                    {{-- Master Row --}}
                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer border-t border-gray-200" @click="open = !open">
                        <td class="px-3 py-3"></td>
                        <td class="px-3 py-3 text-center">
                            <svg class="w-4 h-4 transform transition-transform duration-200 mx-auto text-gray-400" :class="{ 'rotate-90': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </td>
                        <td class="px-3 py-3">
                            <div class="flex items-center">
                                @if($firstItem->foto_barang)
                                    <img src="{{ asset('storage/foto_barang/' . $firstItem->foto_barang) }}" class="w-9 h-9 rounded object-cover mr-3 border">
                                @else
                                    <div class="w-9 h-9 bg-gray-100 rounded flex items-center justify-center mr-3 text-gray-400 text-[9px]">N/A</div>
                                @endif
                                <div>
                                    <div class="text-sm font-bold text-gray-800">{{ $nama_barang }}</div>
                                    <div class="text-xs text-gray-400"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 py-3">
                            <div class="text-sm text-gray-600">{{ $firstItem->kategori->nama_kategori ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $firstItem->ruangan->nama_ruangan ?? '-' }}</div>
                        </td>
                        <td class="px-3 py-3 text-sm text-gray-600 font-bold">{{ $items->count() }} Item</td>
                        <td class="px-3 py-3">
                            <span class="text-xs">Baik: <strong class="text-green-600">{{ $totalBaik }}</strong></span>
                            <span class="text-xs ml-1">Rusak: <strong class="text-red-600">{{ $totalRusak }}</strong></span>
                        </td>
                        <td class="px-3 py-3">
                            <span class="text-xs">✓ <strong class="text-emerald-600">{{ $totalTersedia }}</strong></span>
                            <span class="text-xs ml-1">⇄ <strong class="text-blue-600">{{ $totalDipinjam }}</strong></span>
                        </td>
                        <td class="px-3 py-3 text-right">
                            <a href="{{ route('barang.batch-barcode', ['nama_barang' => $nama_barang]) }}" target="_blank" @click.stop class="text-indigo-600 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded text-xs font-bold transition inline-flex items-center">
                                🖨️ Batch QR
                            </a>
                        </td>
                    </tr>

                    {{-- Editable Sub Items --}}
                    @foreach($items as $barang)
                    <tr x-show="open && 
                                (!filterRuangan || '{{ $barang->ruangan->nama_ruangan ?? '' }}' === filterRuangan) && 
                                (!search || '{{ strtolower($nama_barang . ' ' . $barang->barcode . ' ' . $barang->merk) }}'.includes(search.toLowerCase()))" 
                        x-transition.opacity class="bg-slate-50/50 hover:bg-slate-100/80 transition-colors" id="row-{{ $barang->id }}">
                            {{-- Checkbox --}}
                        <td class="px-3 py-2 text-center" @click.stop>
                            <input type="checkbox" :checked="selectedIds.includes({{ $barang->id }})" @change="toggleSelect({{ $barang->id }})"
                                   class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                        </td>
                        {{-- Arrow --}}
                        <td class="px-3 py-2 text-center">
                            <svg class="w-3 h-3 text-indigo-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </td>
                        {{-- Barcode + Foto --}}
                        <td class="px-3 py-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded border border-gray-200 overflow-hidden flex-shrink-0 bg-gray-50">
                                    @if($barang->foto_barang)
                                        <img src="{{ asset('storage/foto_barang/' . $barang->foto_barang) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300 text-[6px]">📷</div>
                                    @endif
                                </div>
                                <span class="text-xs font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ $barang->barcode }}</span>
                            </div>
                        </td>
                        {{-- Compact Info --}}
                        <td class="px-3 py-2" colspan="4">
                            <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-gray-600">
                                <span>Merk: <strong>{{ $barang->merk ?? '-' }}</strong></span>
                                <span>Pemilik: <strong>{{ $barang->kepemilikan ?? '-' }}</strong></span>
                                <span>Lokasi: <strong>{{ $barang->ruangan->nama_ruangan ?? '-' }}</strong></span>
                                <span>Kondisi:
                                    <strong class="{{ $barang->kondisi == 'Baik' ? 'text-green-600' : ($barang->kondisi == 'Rusak Ringan' ? 'text-yellow-600' : 'text-red-600') }}">{{ $barang->kondisi }}</strong>
                                </span>
                                <span>Status:
                                    <strong class="{{ $barang->status_peminjaman == 'Tersedia' ? 'text-emerald-600' : ($barang->status_peminjaman == 'Dipinjam' ? 'text-blue-600' : 'text-orange-600') }}">{{ $barang->status_peminjaman }}</strong>
                                </span>
                                @if($barang->harga)<span>Harga: <strong>Rp {{ number_format($barang->harga, 0, ',', '.') }}</strong></span>@endif
                            </div>
                        </td>
                        {{-- Actions --}}
                        <td class="px-3 py-2 text-right" @click.stop>
                            <div class="flex justify-end gap-1.5 items-center">
                                <a href="{{ route('barang.barcode', $barang->id) }}" target="_blank" class="text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded text-[10px] font-bold transition">QR</a>
                                <button onclick="openEditPanel({{ $barang->id }})" class="text-indigo-500 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded text-[10px] font-bold transition">Edit</button>
                                <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus barang ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-gray-400 hover:text-red-600 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- Dropdown Edit Panel (Hidden, opened by JS) --}}
                    <tr x-show="open" class="hidden edit-panel" id="edit-panel-{{ $barang->id }}">
                        <td colspan="8" class="px-3 py-0">
                            <div class="bg-white border border-indigo-200 rounded-lg p-5 my-2 mx-6 shadow-sm">
                                <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                                    <h4 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                                        ✏️ Edit — <span class="font-mono text-indigo-600">{{ $barang->barcode }}</span>
                                    </h4>
                                    <button onclick="closeEditPanel({{ $barang->id }})" class="text-gray-400 hover:text-red-500 text-xs font-bold">✕ Tutup</button>
                                </div>

                                {{-- Row 1: Nama & Merk --}}
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Nama Barang</label>
                                        <input type="text" value="{{ $barang->nama_barang }}" disabled
                                               class="w-full border-gray-200 rounded-md text-sm bg-gray-50 text-gray-500 px-3 py-1.5">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Merk / Tipe</label>
                                        <input type="text" value="{{ $barang->merk ?? '' }}" id="edit-merk-{{ $barang->id }}"
                                               class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-indigo-300 focus:border-indigo-400" placeholder="Contoh: Logitech B100">
                                    </div>
                                </div>

                                {{-- Row 2: Kategori & Ruangan --}}
                                <div class="grid grid-cols-2 gap-4 mb-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Kategori</label>
                                        <select id="edit-kategori-{{ $barang->id }}" class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-indigo-300 focus:border-indigo-400">
                                            @foreach($kategoris as $kat)
                                                <option value="{{ $kat->id }}" {{ $barang->kategori_id == $kat->id ? 'selected' : '' }}>{{ $kat->nama_kategori }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Lokasi Ruangan</label>
                                        <select id="edit-ruangan-{{ $barang->id }}" class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-indigo-300 focus:border-indigo-400">
                                            @foreach($ruangans as $r)
                                                <option value="{{ $r->id }}" {{ $barang->ruangan_id == $r->id ? 'selected' : '' }}>{{ $r->nama_ruangan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                {{-- Row 3: Kepemilikan, Kondisi, Status, Harga --}}
                                <div class="grid grid-cols-4 gap-4 mb-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Kepemilikan</label>
                                        <select id="edit-kepemilikan-{{ $barang->id }}" class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-indigo-300 focus:border-indigo-400">
                                            <option value="Prodi" {{ $barang->kepemilikan == 'Prodi' ? 'selected' : '' }}>Prodi</option>
                                            <option value="Lab" {{ $barang->kepemilikan == 'Lab' ? 'selected' : '' }}>Lab</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Kondisi</label>
                                        <select id="edit-kondisi-{{ $barang->id }}" class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-indigo-300 focus:border-indigo-400">
                                            <option value="Baik" {{ $barang->kondisi == 'Baik' ? 'selected' : '' }}>✓ Baik</option>
                                            <option value="Rusak Ringan" {{ $barang->kondisi == 'Rusak Ringan' ? 'selected' : '' }}>⚠ Rusak Ringan</option>
                                            <option value="Rusak Berat" {{ $barang->kondisi == 'Rusak Berat' ? 'selected' : '' }}>✕ Rusak Berat</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Status</label>
                                        <select id="edit-status-{{ $barang->id }}" class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-indigo-300 focus:border-indigo-400">
                                            <option value="Tersedia" {{ $barang->status_peminjaman == 'Tersedia' ? 'selected' : '' }}>Tersedia</option>
                                            <option value="Dipinjam" {{ $barang->status_peminjaman == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                            <option value="Pemeliharaan" {{ $barang->status_peminjaman == 'Pemeliharaan' ? 'selected' : '' }}>Pemeliharaan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Harga (Rp)</label>
                                        <input type="number" value="{{ $barang->harga }}" id="edit-harga-{{ $barang->id }}"
                                               class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-indigo-300 focus:border-indigo-400" placeholder="Opsional">
                                    </div>
                                </div>

                                {{-- Row 4: Deskripsi --}}
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div class="col-span-2">
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Deskripsi / Catatan</label>
                                        <textarea id="edit-deskripsi-{{ $barang->id }}" rows="2"
                                                  class="w-full border-gray-300 rounded-md text-sm px-3 py-1.5 focus:ring-indigo-300 focus:border-indigo-400" placeholder="Catatan tambahan...">{{ $barang->deskripsi }}</textarea>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Foto Barang</label>
                                        <div class="flex items-center gap-3">
                                            <div class="w-14 h-14 rounded-lg border border-gray-200 overflow-hidden flex-shrink-0 bg-gray-50" id="foto-preview-{{ $barang->id }}">
                                                @if($barang->foto_barang)
                                                    <img src="{{ asset('storage/foto_barang/' . $barang->foto_barang) }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-gray-300 text-[8px]">No Foto</div>
                                                @endif
                                            </div>
                                            <div>
                                                <label for="edit-foto-{{ $barang->id }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-1.5 rounded text-[10px] font-bold cursor-pointer transition inline-block">
                                                    📷 Ganti Foto
                                                </label>
                                                <input type="file" id="edit-foto-{{ $barang->id }}" accept="image/*" class="hidden"
                                                       onchange="previewFoto(this, {{ $barang->id }})">
                                                <p class="text-[9px] text-gray-400 mt-1">JPG/PNG, maks 2MB</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Save --}}
                                <div class="flex justify-end gap-2">
                                    <button onclick="closeEditPanel({{ $barang->id }})" class="px-4 py-1.5 text-xs font-bold text-gray-400 hover:text-gray-600">Batal</button>
                                    <button onclick="saveEditPanel({{ $barang->id }})" class="bg-[#1e293b] text-white px-5 py-1.5 rounded-md text-xs font-bold hover:bg-[#0f172a] transition flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                @empty
                <tbody>
                    <tr>
                        <td colspan="8" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="bg-gray-50 p-4 rounded-full mb-4">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-700">Barang Belum Tersedia</h3>
                                <p class="text-sm text-gray-500 max-w-xs mx-auto mt-1">Silakan tambahkan data barang fisik baru.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
                @endforelse
            </table>
        </div>
    </div>

    <script>
        function openEditPanel(id) {
            const panel = document.getElementById('edit-panel-' + id);
            panel.classList.remove('hidden');
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function closeEditPanel(id) {
            document.getElementById('edit-panel-' + id).classList.add('hidden');
        }

        function previewFoto(input, id) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('foto-preview-' + id);
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function saveEditPanel(id) {
            const formData = new FormData();
            formData.append('_method', 'PATCH');
            formData.append('merk', document.getElementById('edit-merk-' + id).value);
            formData.append('kategori_id', document.getElementById('edit-kategori-' + id).value);
            formData.append('ruangan_id', document.getElementById('edit-ruangan-' + id).value);
            formData.append('kepemilikan', document.getElementById('edit-kepemilikan-' + id).value);
            formData.append('kondisi', document.getElementById('edit-kondisi-' + id).value);
            formData.append('status_peminjaman', document.getElementById('edit-status-' + id).value);
            formData.append('harga', document.getElementById('edit-harga-' + id).value || '');
            formData.append('deskripsi', document.getElementById('edit-deskripsi-' + id).value);

            // Append foto if selected
            const fotoInput = document.getElementById('edit-foto-' + id);
            if (fotoInput.files.length > 0) {
                formData.append('foto_barang', fotoInput.files[0]);
            }

            fetch(`/operator/barang/${id}/inline`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    showToast('Perubahan berhasil disimpan!');
                    setTimeout(() => location.reload(), 800);
                }
            })
            .catch(() => showToast('Gagal menyimpan', true));
        }

        function showToast(msg, isError = false) {
            const toast = document.getElementById('saveToast');
            const msgEl = document.getElementById('toastMsg');
            msgEl.textContent = msg;
            toast.querySelector('div').className = `${isError ? 'bg-red-600' : 'bg-emerald-600'} text-white px-5 py-3 rounded-lg shadow-lg text-sm font-bold flex items-center gap-2`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000);
        }
    </script>
</x-app-layout>
