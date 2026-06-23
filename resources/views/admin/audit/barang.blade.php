<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Audit Inventaris Barang') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Message -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-bold">Sukses</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-bold">Gagal</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            {{-- Tab Navigation --}}
            <div class="mb-6" x-data="{ activeTab: 'data' }">
                <div class="flex border-b border-gray-200">
                    <button @click="activeTab = 'data'" :class="activeTab === 'data' ? 'border-indigo-500 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-3 text-sm border-b-2 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        Data Barang & Audit
                    </button>
                    <button @click="activeTab = 'riwayat'" :class="activeTab === 'riwayat' ? 'border-indigo-500 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-3 text-sm border-b-2 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Audit
                        @if($audits->where('status', 'pending')->count() > 0)
                            <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $audits->where('status', 'pending')->count() }}</span>
                        @endif
                    </button>
                </div>

                {{-- ====== TAB 1: Data Barang (Editable Table) ====== --}}
                <div x-show="activeTab === 'data'" x-transition>
                    {{-- Search & Filter --}}
                    <div class="flex flex-col md:flex-row justify-between items-center my-4 gap-4" x-data="{ search: '' }">
                        <div class="flex flex-1 w-full max-w-lg">
                            <input type="text" x-model="search" placeholder="Cari nama barang, barcode, merk..." class="w-full border-gray-300 rounded-l-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" id="searchBarangInput">
                            <button class="bg-gray-100 border border-l-0 border-gray-300 px-4 rounded-r-md hover:bg-gray-200">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500">Total: <strong class="text-gray-700">{{ $barangs->count() }}</strong> barang</span>
                        </div>
                    </div>

                    {{-- Editable Table --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="tableAuditBarang">
                                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold w-12">No</th>
                                        <th class="px-4 py-3 font-semibold">Barcode</th>
                                        <th class="px-4 py-3 font-semibold">Nama Barang</th>
                                        <th class="px-4 py-3 font-semibold">Merk</th>
                                        <th class="px-4 py-3 font-semibold">Kategori</th>
                                        <th class="px-4 py-3 font-semibold">Ruangan</th>
                                        <th class="px-4 py-3 font-semibold">Kepemilikan</th>
                                        <th class="px-4 py-3 font-semibold">Kondisi Saat Ini</th>
                                        <th class="px-4 py-3 font-semibold">Status</th>
                                        <th class="px-4 py-3 font-semibold">Terakhir Diperiksa</th>
                                        @if(auth()->user()->role === 'teknisi')
                                        <th class="px-4 py-3 font-semibold text-indigo-600 bg-indigo-50">Kondisi Audit</th>
                                        <th class="px-4 py-3 font-semibold text-indigo-600 bg-indigo-50">Catatan</th>
                                        <th class="px-4 py-3 font-semibold text-indigo-600 bg-indigo-50 text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($barangs as $index => $barang)
                                    <tr class="hover:bg-gray-50 transition-colors barang-row" data-nama="{{ strtolower($barang->nama_barang) }}" data-barcode="{{ strtolower($barang->barcode) }}" data-merk="{{ strtolower($barang->merk ?? '') }}">
                                        <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <span class="text-xs font-mono font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $barang->barcode }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                @if($barang->foto_barang)
                                                    <img src="{{ asset('storage/foto_barang/' . $barang->foto_barang) }}" class="w-8 h-8 rounded object-cover mr-2 border border-gray-100">
                                                @else
                                                    <div class="w-8 h-8 bg-gray-100 rounded flex items-center justify-center mr-2 text-gray-400">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                                    </div>
                                                @endif
                                                <span class="text-sm font-semibold text-gray-800">{{ $barang->nama_barang }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $barang->merk ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $barang->kategori->nama_kategori ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $barang->ruangan->nama_ruangan ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $barang->kepemilikan === 'Prodi' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700' }}">{{ $barang->kepemilikan ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($barang->kondisi == 'Baik')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-600 uppercase">Baik</span>
                                            @elseif($barang->kondisi == 'Rusak Ringan')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-600 uppercase">Rusak Ringan</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-600 uppercase">Rusak Berat</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($barang->status_peminjaman == 'Tersedia')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-600 uppercase">Tersedia</span>
                                            @elseif($barang->status_peminjaman == 'Dipinjam')
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-600 uppercase">Dipinjam</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-600 uppercase">Pemeliharaan</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ $barang->terakhir_diperiksa_at ? \Carbon\Carbon::parse($barang->terakhir_diperiksa_at)->format('d M Y') : 'Belum pernah' }}
                                        </td>
                                        @if(auth()->user()->role === 'teknisi')
                                        <td class="px-4 py-3 bg-indigo-50/30">
                                            <select form="audit-form-{{ $barang->id }}" name="kondisi" class="block w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5" id="kondisi-{{ $barang->id }}">
                                                <option value="Baik" {{ $barang->kondisi === 'Baik' ? 'selected' : '' }}>Baik</option>
                                                <option value="Rusak Ringan" {{ $barang->kondisi === 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                                <option value="Rusak Berat" {{ $barang->kondisi === 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 bg-indigo-50/30">
                                            <input form="audit-form-{{ $barang->id }}" type="text" name="catatan" placeholder="Catatan audit..." class="block w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5" id="catatan-{{ $barang->id }}">
                                        </td>
                                        <td class="px-4 py-3 bg-indigo-50/30 text-center">
                                            <form id="audit-form-{{ $barang->id }}" action="{{ route('admin.audit.barang.store') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                                                <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all hover:shadow-md" title="Kirim Audit">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    Audit
                                                </button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="13" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="bg-gray-50 p-4 rounded-full mb-4">
                                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-md font-bold text-gray-600">Belum ada data barang</h3>
                                                <p class="text-sm text-gray-400 mt-1">Silakan tambahkan barang terlebih dahulu.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- ====== TAB 2: Riwayat Audit ====== --}}
                <div x-show="activeTab === 'riwayat'" x-transition>
                    <div class="mt-4 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">No</th>
                                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                                        <th class="px-4 py-3 font-semibold">Barcode</th>
                                        <th class="px-4 py-3 font-semibold">Nama Barang</th>
                                        <th class="px-4 py-3 font-semibold">Kondisi</th>
                                        <th class="px-4 py-3 font-semibold">Catatan</th>
                                        <th class="px-4 py-3 font-semibold">Status</th>
                                        @if(auth()->user()->role === 'kepala_lab')
                                        <th class="px-4 py-3 font-semibold text-right">Validasi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($audits as $index => $audit)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-400">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($audit->tanggal_audit)->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="text-xs font-mono font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $audit->barang->barcode }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $audit->barang->nama_barang }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-0.5 inline-flex text-[10px] leading-5 font-bold rounded-full uppercase
                                                @if($audit->kondisi === 'Baik') bg-green-100 text-green-700 
                                                @elseif($audit->kondisi === 'Rusak Ringan') bg-yellow-100 text-yellow-700 
                                                @else bg-red-100 text-red-700 @endif
                                            ">
                                                {{ $audit->kondisi }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 max-w-[200px] truncate">{{ $audit->catatan ?? '-' }}</td>
                                        <td class="px-4 py-3">
                                            @if($audit->status === 'pending')
                                                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded-full bg-yellow-100 text-yellow-700 uppercase">Menunggu ACC</span>
                                            @elseif($audit->status === 'divalidasi')
                                                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded-full bg-green-100 text-green-700 uppercase">Divalidasi</span>
                                            @else
                                                <span class="px-2 py-0.5 inline-flex text-[10px] font-bold rounded-full bg-red-100 text-red-700 uppercase">Ditolak</span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->role === 'kepala_lab')
                                        <td class="px-4 py-3 text-right">
                                            @if($audit->status === 'pending')
                                                <div class="flex items-center justify-end gap-2">
                                                    <form action="{{ route('admin.audit.barang.validate', $audit->id) }}" method="POST" class="inline-block">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-bold rounded-md transition">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            ACC
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.audit.barang.validate', $audit->id) }}" method="POST" class="inline-block">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="action" value="reject">
                                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 hover:bg-red-100 text-red-700 text-xs font-bold rounded-md transition">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            Tolak
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-xs">Tervalidasi</span>
                                            @endif
                                        </td>
                                        @endif
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="bg-gray-50 p-4 rounded-full mb-4">
                                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <h3 class="text-md font-bold text-gray-600">Belum ada riwayat audit barang</h3>
                                                <p class="text-sm text-gray-400 mt-1">Audit barang melalui tab "Data Barang & Audit".</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search/Filter Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchBarangInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    document.querySelectorAll('.barang-row').forEach(function(row) {
                        const nama = row.dataset.nama || '';
                        const barcode = row.dataset.barcode || '';
                        const merk = row.dataset.merk || '';
                        const match = nama.includes(query) || barcode.includes(query) || merk.includes(query);
                        row.style.display = match ? '' : 'none';
                    });
                });
            }
        });
    </script>
</x-app-layout>
