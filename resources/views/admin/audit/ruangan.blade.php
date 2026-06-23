<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Audit Ruangan') }}
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Data Ruangan & Audit
                    </button>
                    <button @click="activeTab = 'riwayat'" :class="activeTab === 'riwayat' ? 'border-indigo-500 text-indigo-600 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'" class="px-6 py-3 text-sm border-b-2 transition-colors flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Riwayat Audit
                        @if($audits->where('status', 'pending')->count() > 0)
                            <span class="bg-yellow-100 text-yellow-700 text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $audits->where('status', 'pending')->count() }}</span>
                        @endif
                    </button>
                </div>

                {{-- ====== TAB 1: Data Ruangan (Editable Table) ====== --}}
                <div x-show="activeTab === 'data'" x-transition>
                    {{-- Search & Info --}}
                    <div class="flex flex-col md:flex-row justify-between items-center my-4 gap-4">
                        <div class="flex flex-1 w-full max-w-lg">
                            <input type="text" placeholder="Cari nama ruangan, kode..." class="w-full border-gray-300 rounded-l-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" id="searchRuanganInput">
                            <button class="bg-gray-100 border border-l-0 border-gray-300 px-4 rounded-r-md hover:bg-gray-200">
                                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-500">Total: <strong class="text-gray-700">{{ $ruangans->count() }}</strong> ruangan</span>
                        </div>
                    </div>

                    {{-- Editable Table --}}
                    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse" id="tableAuditRuangan">
                                <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold w-12">No</th>
                                        <th class="px-4 py-3 font-semibold">Foto</th>
                                        <th class="px-4 py-3 font-semibold">Kode</th>
                                        <th class="px-4 py-3 font-semibold">Nama Ruangan</th>
                                        <th class="px-4 py-3 font-semibold">Lokasi</th>
                                        <th class="px-4 py-3 font-semibold">Kapasitas</th>
                                        <th class="px-4 py-3 font-semibold">Fasilitas</th>
                                        <th class="px-4 py-3 font-semibold">Keterangan</th>
                                        <th class="px-4 py-3 font-semibold">Terakhir Diperiksa</th>
                                        @if(auth()->user()->role === 'teknisi')
                                        <th class="px-4 py-3 font-semibold text-indigo-600 bg-indigo-50">Kondisi Audit</th>
                                        <th class="px-4 py-3 font-semibold text-indigo-600 bg-indigo-50">Catatan</th>
                                        <th class="px-4 py-3 font-semibold text-indigo-600 bg-indigo-50 text-center">Aksi</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($ruangans as $index => $ruangan)
                                    <tr class="hover:bg-gray-50 transition-colors ruangan-row" data-nama="{{ strtolower($ruangan->nama_ruangan) }}" data-kode="{{ strtolower($ruangan->kode_ruangan ?? '') }}">
                                        <td class="px-4 py-3 text-sm text-gray-400 font-mono">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            @if($ruangan->foto_ruangan)
                                                <img src="{{ asset('storage/foto_ruangan/' . $ruangan->foto_ruangan) }}" class="w-12 h-8 rounded object-cover border border-gray-100">
                                            @else
                                                <div class="w-12 h-8 bg-gray-100 rounded flex items-center justify-center text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-xs font-mono font-bold text-gray-700 bg-gray-100 px-2 py-1 rounded">{{ $ruangan->kode_ruangan ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm font-semibold text-gray-800">{{ $ruangan->nama_ruangan }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $ruangan->lokasi ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600 text-center">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                                {{ $ruangan->kapasitas ?? '0' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1 max-w-[200px]">
                                                @if($ruangan->fasilitas)
                                                    @foreach(array_slice(explode(',', $ruangan->fasilitas), 0, 3) as $item)
                                                        <span class="text-[9px] text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded font-medium">{{ trim($item) }}</span>
                                                    @endforeach
                                                    @if(count(explode(',', $ruangan->fasilitas)) > 3)
                                                        <span class="text-[9px] text-gray-400 font-medium">+{{ count(explode(',', $ruangan->fasilitas)) - 3 }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-xs text-gray-400">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500 max-w-[150px] truncate">{{ $ruangan->keterangan ?? '-' }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-500">
                                            {{ $ruangan->terakhir_diperiksa_at ? \Carbon\Carbon::parse($ruangan->terakhir_diperiksa_at)->format('d M Y') : 'Belum pernah' }}
                                        </td>
                                        @if(auth()->user()->role === 'teknisi')
                                        <td class="px-4 py-3 bg-indigo-50/30">
                                            <select form="audit-ruangan-form-{{ $ruangan->id }}" name="kondisi" class="block w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5" id="kondisi-ruangan-{{ $ruangan->id }}">
                                                <option value="Baik">Baik</option>
                                                <option value="Rusak Ringan">Rusak Ringan</option>
                                                <option value="Rusak Berat">Rusak Berat</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 bg-indigo-50/30">
                                            <input form="audit-ruangan-form-{{ $ruangan->id }}" type="text" name="catatan" placeholder="Catatan audit..." class="block w-full text-xs border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1.5" id="catatan-ruangan-{{ $ruangan->id }}">
                                        </td>
                                        <td class="px-4 py-3 bg-indigo-50/30 text-center">
                                            <form id="audit-ruangan-form-{{ $ruangan->id }}" action="{{ route('admin.audit.ruangan.store') }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="ruangan_id" value="{{ $ruangan->id }}">
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
                                        <td colspan="12" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="bg-gray-50 p-4 rounded-full mb-4">
                                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                    </svg>
                                                </div>
                                                <h3 class="text-md font-bold text-gray-600">Belum ada data ruangan</h3>
                                                <p class="text-sm text-gray-400 mt-1">Silakan tambahkan ruangan terlebih dahulu.</p>
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
                                        <th class="px-4 py-3 font-semibold">Kode</th>
                                        <th class="px-4 py-3 font-semibold">Nama Ruangan</th>
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
                                            <span class="text-xs font-mono font-bold text-gray-600 bg-gray-100 px-2 py-0.5 rounded">{{ $audit->ruangan->kode_ruangan ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-800">{{ $audit->ruangan->nama_ruangan }}</td>
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
                                                    <form action="{{ route('admin.audit.ruangan.validate', $audit->id) }}" method="POST" class="inline-block">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="action" value="approve">
                                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 hover:bg-green-100 text-green-700 text-xs font-bold rounded-md transition">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                            ACC
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.audit.ruangan.validate', $audit->id) }}" method="POST" class="inline-block">
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
                                                <h3 class="text-md font-bold text-gray-600">Belum ada riwayat audit ruangan</h3>
                                                <p class="text-sm text-gray-400 mt-1">Audit ruangan melalui tab "Data Ruangan & Audit".</p>
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
            const searchInput = document.getElementById('searchRuanganInput');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase();
                    document.querySelectorAll('.ruangan-row').forEach(function(row) {
                        const nama = row.dataset.nama || '';
                        const kode = row.dataset.kode || '';
                        const match = nama.includes(query) || kode.includes(query);
                        row.style.display = match ? '' : 'none';
                    });
                });
            }
        });
    </script>
</x-app-layout>
