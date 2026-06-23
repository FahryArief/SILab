<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.audit.periode.show', $periode->id) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Audit Barang - {{ $periode->nama_periode }}
                </h2>
                @if($periode->status === 'open')
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-yellow-100 text-yellow-700">Open</span>
                @elseif($periode->status === 'dilaporkan')
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-100 text-blue-700">Dilaporkan</span>
                @elseif($periode->status === 'revisi')
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-700">Revisi</span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700">Disetujui</span>
                @endif
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-500 font-medium">
                <div>
                    <span class="text-indigo-600 font-bold">{{ count($auditedBarangIds) }}</span> / {{ $barangs->count() }} diaudit
                </div>

                @if(auth()->user()->role === 'teknisi' && in_array($periode->status, ['open', 'revisi']))
                <form action="{{ route('admin.audit.periode.laporkan', $periode->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melaporkan hasil audit ini ke Kepala Lab? Pastikan semua data audit sudah terisi.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-1.5 px-4 rounded-lg shadow-sm flex items-center transition duration-300 text-xs">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Laporkan Hasil
                    </button>
                </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
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

            <div class="flex justify-between items-center mb-4">
                <div class="flex items-center gap-2">
                    <input type="text" placeholder="Cari barang..." class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm w-full max-w-sm" id="searchBarangInput">
                </div>

                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                <form action="{{ route('admin.audit.barang.bulkStore') }}" method="POST" id="bulk-audit-form">
                    @csrf
                    <input type="hidden" name="audit_periode_id" value="{{ $periode->id }}">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center transition duration-300 text-sm disabled:opacity-50" id="bulk-submit-btn" disabled>
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Set Terpilih Menjadi Baik (<span id="selected-count">0</span>)
                    </button>
                </form>
                @endif
            </div>

            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                            <tr>
                                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                <th class="px-3 py-3 font-semibold w-10 text-center">
                                    <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </th>
                                @endif
                                <th class="px-3 py-3 font-semibold w-10">No</th>
                                <th class="px-3 py-3 font-semibold">Barcode</th>
                                <th class="px-3 py-3 font-semibold">Nama Barang</th>
                                <th class="px-3 py-3 font-semibold">Ruangan</th>
                                <th class="px-3 py-3 font-semibold">Kondisi Sekarang</th>
                                <th class="px-3 py-3 font-semibold text-indigo-600 bg-indigo-50">Kondisi Audit</th>
                                <th class="px-3 py-3 font-semibold text-indigo-600 bg-indigo-50">Catatan</th>
                                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                <th class="px-3 py-3 font-semibold text-indigo-600 bg-indigo-50 text-center">Aksi</th>
                                @endif
                                <th class="px-3 py-3 font-semibold text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($barangs as $index => $barang)
                            @php
                                $isAudited = in_array($barang->id, $auditedBarangIds);
                                $auditData = $auditBarangMap[$barang->id] ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors barang-row {{ $isAudited ? 'bg-green-50/30' : '' }}"
                                data-nama="{{ strtolower($barang->nama_barang) }}"
                                data-barcode="{{ strtolower($barang->barcode) }}"
                                data-merk="{{ strtolower($barang->merk ?? '') }}">
                                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                <td class="px-3 py-2.5 text-center">
                                    <input type="checkbox" name="barang_ids[]" value="{{ $barang->id }}" form="bulk-audit-form" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                @endif
                                <td class="px-3 py-2.5 text-xs text-gray-400 font-mono">{{ $index + 1 }}</td>
                                <td class="px-3 py-2.5">
                                    <span class="text-[10px] font-mono font-bold text-gray-700 bg-gray-100 px-1.5 py-0.5 rounded">{{ $barang->barcode }}</span>
                                </td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center">
                                        @if($barang->foto_barang)
                                            <img src="{{ asset('storage/foto_barang/' . $barang->foto_barang) }}" class="w-7 h-7 rounded object-cover mr-2 border">
                                        @endif
                                        <span class="text-xs font-semibold text-gray-800">{{ $barang->nama_barang }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-xs text-gray-500">{{ $barang->ruangan->nama_ruangan ?? '-' }}</td>
                                <td class="px-3 py-2.5">
                                    @if($barang->kondisi == 'Baik')
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-600 uppercase">Baik</span>
                                    @elseif($barang->kondisi == 'Rusak Ringan')
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-yellow-100 text-yellow-600 uppercase">Rusak Ringan</span>
                                    @else
                                        <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-red-100 text-red-600 uppercase">Rusak Berat</span>
                                    @endif
                                </td>

                                {{-- Editable Audit Columns --}}
                                <td class="px-3 py-2.5 bg-indigo-50/20 w-32">
                                    @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                        <select form="audit-form-b-{{ $barang->id }}" name="kondisi" class="block w-full text-[11px] border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1">
                                            <option value="Baik" {{ ($auditData->kondisi ?? $barang->kondisi) === 'Baik' ? 'selected' : '' }}>Baik</option>
                                            <option value="Rusak Ringan" {{ ($auditData->kondisi ?? '') === 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                            <option value="Rusak Berat" {{ ($auditData->kondisi ?? '') === 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                        </select>
                                    @else
                                        @if($auditData)
                                            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold uppercase
                                                {{ $auditData->kondisi === 'Baik' ? 'bg-green-100 text-green-600' : ($auditData->kondisi === 'Rusak Ringan' ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') }}">
                                                {{ $auditData->kondisi }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-300">—</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 bg-indigo-50/20">
                                    @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                        <input form="audit-form-b-{{ $barang->id }}" type="text" name="catatan" value="{{ $auditData->catatan ?? '' }}" placeholder="Catatan..." class="block w-full text-[11px] border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1">
                                    @else
                                        <span class="text-xs text-gray-500">{{ $auditData->catatan ?? '—' }}</span>
                                    @endif
                                </td>

                                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                <td class="px-3 py-2.5 bg-indigo-50/20 text-center w-20">
                                    <form id="audit-form-b-{{ $barang->id }}" action="{{ route('admin.audit.barang.store') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="audit_periode_id" value="{{ $periode->id }}">
                                        <input type="hidden" name="barang_id" value="{{ $barang->id }}">
                                        <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 {{ $isAudited ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700' }} text-white text-[10px] font-bold rounded-md shadow-sm transition" title="{{ $isAudited ? 'Update' : 'Simpan' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            {{ $isAudited ? 'Update' : 'Simpan' }}
                                        </button>
                                    </form>
                                </td>
                                @endif

                                <td class="px-3 py-2.5 text-center">
                                    @if($isAudited)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-600">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            Done
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-gray-300">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" class="px-6 py-12 text-center text-sm text-gray-400">Tidak ada data barang.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchBarang = document.getElementById('searchBarangInput');
            if (searchBarang) {
                searchBarang.addEventListener('input', function() {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('.barang-row').forEach(r => {
                        const match = (r.dataset.nama || '').includes(q) || (r.dataset.barcode || '').includes(q) || (r.dataset.merk || '').includes(q);
                        r.style.display = match ? '' : 'none';
                    });
                });
            }

            // Bulk actions logic
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const bulkSubmitBtn = document.getElementById('bulk-submit-btn');
            const selectedCount = document.getElementById('selected-count');

            function updateBulkAction() {
                const checked = document.querySelectorAll('.row-checkbox:checked').length;
                if (selectedCount) selectedCount.innerText = checked;
                if (bulkSubmitBtn) bulkSubmitBtn.disabled = checked === 0;
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    // Only check visible rows
                    document.querySelectorAll('.barang-row').forEach(row => {
                        if (row.style.display !== 'none') {
                            const cb = row.querySelector('.row-checkbox');
                            if (cb) cb.checked = selectAll.checked;
                        }
                    });
                    updateBulkAction();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkAction);
            });
        });
    </script>
</x-app-layout>
