<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.audit.periode.show', $periode->id) }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Audit Ruangan - {{ $periode->nama_periode }}
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
                    <span class="text-indigo-600 font-bold">{{ count($auditedRuanganIds) }}</span> / {{ $ruangans->count() }} diaudit
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
                    <input type="text" placeholder="Cari ruangan..." class="border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm w-full max-w-sm" id="searchRuanganInput">
                </div>

                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                <form action="{{ route('admin.audit.ruangan.bulkStore') }}" method="POST" id="bulk-audit-form">
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
                                <th class="px-3 py-3 font-semibold text-gray-500 bg-gray-50 text-left w-12">No</th>
                                <th class="px-3 py-3 font-semibold text-gray-500 bg-gray-50 text-left">Ruangan</th>
                                <th class="px-3 py-3 font-semibold text-gray-500 bg-gray-50 text-left w-32">Lokasi</th>
                                <th class="px-3 py-3 font-semibold text-gray-500 bg-gray-50 text-center w-24">Kapasitas</th>
                                <th class="px-3 py-3 font-semibold text-gray-500 bg-gray-50 text-left">Fasilitas</th>
                                <th class="px-3 py-3 font-semibold text-indigo-600 bg-indigo-50 text-left">Hasil Audit</th>
                                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                    <th class="px-3 py-3 font-semibold text-indigo-600 bg-indigo-50 text-center">Aksi</th>
                                @endif
                                <th class="px-3 py-3 font-semibold text-gray-500 bg-gray-50 text-center w-28">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($ruangans as $index => $ruangan)
                            @php
                                $isAudited = in_array($ruangan->id, $auditedRuanganIds);
                                $auditData = $auditRuanganMap[$ruangan->id] ?? null;
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors ruangan-row {{ $isAudited ? 'bg-green-50/30' : '' }}"
                                data-nama="{{ strtolower($ruangan->nama_ruangan) }}"
                                data-kode="{{ strtolower($ruangan->kode_ruangan ?? '') }}">
                                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                <td class="px-3 py-2.5 text-center">
                                    <input type="checkbox" name="ruangan_ids[]" value="{{ $ruangan->id }}" form="bulk-audit-form" class="row-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </td>
                                @endif
                                <td class="px-3 py-2.5 text-xs text-gray-400 font-mono">{{ $index + 1 }}</td>
                                <td class="px-3 py-2.5">
                                    <div class="flex items-center">
                                        @if($ruangan->foto_ruangan)
                                            <img src="{{ asset('storage/foto_ruangan/' . $ruangan->foto_ruangan) }}" class="w-7 h-7 rounded object-cover mr-2 border">
                                        @endif
                                        <div class="flex flex-col">
                                            <span class="text-xs font-semibold text-gray-800">{{ $ruangan->nama_ruangan }}</span>
                                            <span class="text-[10px] text-gray-400">{{ $ruangan->kode_ruangan ?? '-' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-2.5 text-xs text-gray-500">{{ $ruangan->lokasi ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-xs text-gray-500 text-center">{{ $ruangan->kapasitas ?? '0' }}</td>
                                <td class="px-3 py-2.5">
                                    <div class="flex flex-wrap gap-0.5 max-w-[150px]">
                                        @if($ruangan->fasilitas)
                                            @foreach(array_slice(explode(',', $ruangan->fasilitas), 0, 2) as $item)
                                                <span class="text-[8px] text-indigo-600 bg-indigo-50 px-1 py-0.5 rounded font-medium">{{ trim($item) }}</span>
                                            @endforeach
                                            @if(count(explode(',', $ruangan->fasilitas)) > 2)
                                                <span class="text-[8px] text-gray-400">+{{ count(explode(',', $ruangan->fasilitas)) - 2 }}</span>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-300">-</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-3 py-2.5 bg-indigo-50/20">
                                    @if($auditData)
                                        @php
                                            $fasilitasAudit = $auditData->fasilitas_audit ?? [];
                                            $baik = count(array_filter($fasilitasAudit, fn($v) => $v === 'Baik'));
                                            $rusak = count(array_filter($fasilitasAudit, fn($v) => $v === 'Rusak'));
                                        @endphp
                                        <div class="flex flex-col gap-1">
                                            <div class="flex gap-2 text-[10px]">
                                                <span class="text-green-600 font-bold">{{ $baik }} Baik</span>
                                                <span class="text-red-600 font-bold">{{ $rusak }} Rusak</span>
                                            </div>
                                            @if($auditData->catatan)
                                                <span class="text-[10px] text-gray-500 italic border-l-2 border-gray-300 pl-1 block truncate max-w-[150px]" title="{{ $auditData->catatan }}">{{ $auditData->catatan }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                                @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
                                <td class="px-3 py-2.5 bg-indigo-50/20 text-center">
                                    <button type="button" onclick="document.getElementById('modal-audit-{{ $ruangan->id }}').classList.remove('hidden')" class="inline-flex items-center gap-1 px-2.5 py-1.5 {{ $isAudited ? 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' : 'bg-indigo-600 text-white hover:bg-indigo-700' }} text-[10px] font-bold rounded-md shadow-sm transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                        {{ $isAudited ? 'Edit' : 'Audit' }}
                                    </button>
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
                                <td colspan="11" class="px-6 py-12 text-center text-sm text-gray-400">Tidak ada data ruangan.</td>
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
            const searchRuangan = document.getElementById('searchRuanganInput');
            if (searchRuangan) {
                searchRuangan.addEventListener('input', function() {
                    const q = this.value.toLowerCase();
                    document.querySelectorAll('.ruangan-row').forEach(r => {
                        const match = (r.dataset.nama || '').includes(q) || (r.dataset.kode || '').includes(q);
                        r.style.display = match ? '' : 'none';
                    });
                });
            }

            const selectAll = document.getElementById('select-all');
            const bulkSubmitBtn = document.getElementById('bulk-submit-btn');
            const selectedCount = document.getElementById('selected-count');

            function updateBulkAction() {
                const checked = document.querySelectorAll('.row-checkbox:checked').length;
                if (selectedCount) selectedCount.innerText = checked;
                if (bulkSubmitBtn) bulkSubmitBtn.disabled = checked === 0;
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => {
                        if (cb.closest('.ruangan-row').style.display !== 'none') {
                            cb.checked = selectAll.checked;
                        }
                    });
                    updateBulkAction();
                });
            }

            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.addEventListener('change', updateBulkAction);
            });
        });

        function toggleCheckboxes(source) {
            checkboxes = document.getElementsByName('ruangan_ids[]');
            for(var i=0, n=checkboxes.length;i<n;i++) {
                checkboxes[i].checked = source.checked;
            }
        }
    </script>

    @if(in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi')
        @foreach($ruangans as $ruangan)
            @php
                $auditData = $auditRuanganMap[$ruangan->id] ?? null;
                $fasilitasArray = array_filter(array_map('trim', explode(',', $ruangan->fasilitas ?? '')));
                $fasilitasAudit = $auditData->fasilitas_audit ?? [];
            @endphp
            <div id="modal-audit-{{ $ruangan->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modal-audit-{{ $ruangan->id }}').classList.add('hidden')"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                        <form action="{{ route('admin.audit.ruangan.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="audit_periode_id" value="{{ $periode->id }}">
                            <input type="hidden" name="ruangan_id" value="{{ $ruangan->id }}">
                            <div class="bg-white px-6 pt-6 pb-4">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">Audit Fasilitas: {{ $ruangan->nama_ruangan }}</h3>
                                        <p class="text-xs text-gray-500 mt-1">Tandai kondisi masing-masing fasilitas di ruangan ini.</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('modal-audit-{{ $ruangan->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-500">
                                        <span class="sr-only">Close</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                                
                                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                                    @if(count($fasilitasArray) > 0)
                                        <div class="grid grid-cols-1 gap-3">
                                            @foreach($fasilitasArray as $item)
                                                @php 
                                                    $status = $fasilitasAudit[$item] ?? null; 
                                                @endphp
                                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg bg-gray-50">
                                                    <span class="text-sm font-medium text-gray-800">{{ $item }}</span>
                                                    <div class="flex gap-4">
                                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                                            <input type="radio" name="fasilitas_audit[{{ $item }}]" value="Baik" class="text-green-600 focus:ring-green-500 w-4 h-4" {{ $status === 'Baik' ? 'checked' : '' }} required>
                                                            <span class="text-xs font-medium text-gray-700">Baik</span>
                                                        </label>
                                                        <label class="flex items-center gap-1.5 cursor-pointer">
                                                            <input type="radio" name="fasilitas_audit[{{ $item }}]" value="Rusak" class="text-red-600 focus:ring-red-500 w-4 h-4" {{ $status === 'Rusak' ? 'checked' : '' }} required>
                                                            <span class="text-xs font-medium text-gray-700">Rusak</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="p-4 bg-yellow-50 text-yellow-700 rounded-lg text-sm">
                                            Ruangan ini tidak memiliki data fasilitas.
                                        </div>
                                    @endif

                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Keseluruhan (Opsional)</label>
                                        <textarea name="catatan" rows="2" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" placeholder="Tulis catatan jika ada...">{{ $auditData->catatan ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2 rounded-b-2xl">
                                <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                    Simpan Audit
                                </button>
                                <button type="button" onclick="document.getElementById('modal-audit-{{ $ruangan->id }}').classList.add('hidden')" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</x-app-layout>
