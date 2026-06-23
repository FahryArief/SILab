<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Audit Inventaris') }}
            </h2>
            
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

            {{-- Info Banner untuk Teknisi --}}
            @if(auth()->user()->role === 'teknisi')
                @php $openPeriodes = $periodes->whereIn('status', ['open', 'revisi'])->count(); @endphp
                @if($openPeriodes > 0)
                <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-4 mb-6 flex items-center gap-3">
                    <div class="bg-indigo-100 p-2 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-indigo-800">Ada {{ $openPeriodes }} perintah audit yang perlu dikerjakan</p>
                        <p class="text-xs text-indigo-600">Klik pada periode untuk mulai mengisi audit, lalu laporkan hasilnya.</p>
                    </div>
                </div>
                @endif
            @endif

            {{-- Filter Tabs --}}
            <div x-data="{ filter: 'semua' }" class="mb-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex gap-2 flex-wrap">
                        <button @click="filter = 'semua'" :class="filter === 'semua' ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold transition">Semua</button>
                        <button @click="filter = 'open'" :class="filter === 'open' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold transition">
                            🟡 Open
                            @if($periodes->where('status', 'open')->count() > 0)
                                <span class="ml-1 bg-white/20 px-1.5 py-0.5 rounded-full text-[10px]">{{ $periodes->where('status', 'open')->count() }}</span>
                            @endif
                        </button>
                        <button @click="filter = 'revisi'" :class="filter === 'revisi' ? 'bg-red-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold transition">
                            🔴 Revisi
                            @if($periodes->where('status', 'revisi')->count() > 0)
                                <span class="ml-1 bg-white/20 px-1.5 py-0.5 rounded-full text-[10px]">{{ $periodes->where('status', 'revisi')->count() }}</span>
                            @endif
                        </button>
                        <button @click="filter = 'dilaporkan'" :class="filter === 'dilaporkan' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold transition">📋 Dilaporkan</button>
                        <button @click="filter = 'disetujui'" :class="filter === 'disetujui' ? 'bg-green-600 text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50'" class="px-4 py-2 rounded-lg text-xs font-bold transition">✅ Disetujui</button>
                    </div>
                    @if(in_array(auth()->user()->role, ['kepala_lab', 'super_admin']))
                    <button onclick="document.getElementById('modal-buat-periode').classList.remove('hidden')" class="bg-[#1e293b] text-white px-4 py-2 rounded-lg text-xs font-bold shadow hover:bg-slate-700 transition flex items-center whitespace-nowrap">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Buat Perintah Audit
                    </button>
                    @endif
                </div>

                {{-- Periode Cards Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($periodes as $periode)
                    <div x-show="filter === 'semua' || filter === '{{ $periode->status }}'" x-transition
                         class="bg-white border rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow group
                            {{ $periode->status === 'open' ? 'border-indigo-200' : ($periode->status === 'revisi' ? 'border-red-200' : ($periode->status === 'dilaporkan' ? 'border-blue-200' : 'border-green-200')) }}">
                        
                        {{-- Card Header --}}
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $periode->nama_periode }}</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">oleh {{ $periode->kepalaLab->name ?? 'Kepala Lab' }}</p>
                                </div>
                                @if($periode->status === 'open')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-yellow-100 text-yellow-700 border border-yellow-200">Open</span>
                                @elseif($periode->status === 'revisi')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-red-100 text-red-700 border border-red-200">Revisi</span>
                                @elseif($periode->status === 'dilaporkan')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-blue-100 text-blue-700 border border-blue-200">Dilaporkan</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-green-100 text-green-700 border border-green-200">Disetujui</span>
                                @endif
                            </div>

                            {{-- Date Range --}}
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-3">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $periode->tanggal_mulai->format('d M Y') }} — {{ $periode->tanggal_selesai->format('d M Y') }}</span>
                            </div>

                            {{-- Type Badge --}}
                            <div class="flex items-center gap-2 mb-3">
                                @if($periode->tipe === 'barang')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-600 border border-purple-100">📦 Barang</span>
                                @elseif($periode->tipe === 'ruangan')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-600 border border-teal-100">🏠 Ruangan</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-600 border border-purple-100">📦 Barang</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-50 text-teal-600 border border-teal-100">🏠 Ruangan</span>
                                @endif
                            </div>

                            {{-- Catatan --}}
                            @if($periode->catatan)
                            <div class="bg-gray-50 rounded-lg p-2.5 mb-3">
                                <p class="text-xs text-gray-500 italic">💬 "{{ Str::limit($periode->catatan, 100) }}"</p>
                            </div>
                            @endif

                            {{-- Progress --}}
                            @php
                                $totalAuditBarang = $periode->auditBarangs->count();
                                $totalAuditRuangan = $periode->auditRuangans->count();
                                $totalAudited = $totalAuditBarang + $totalAuditRuangan;
                            @endphp
                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <span class="font-medium">{{ $totalAudited }} item telah diaudit</span>
                            </div>
                        </div>

                        {{-- Card Footer Actions --}}
                        <div class="border-t border-gray-100 bg-gray-50/50 grid grid-cols-1">
                            <a href="{{ route('admin.audit.periode.show', $periode->id) }}" class="py-3 text-center text-xs font-bold text-indigo-600 hover:bg-indigo-50 transition-colors flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                {{ in_array($periode->status, ['open', 'revisi']) && auth()->user()->role === 'teknisi' ? 'KERJAKAN AUDIT' : 'LIHAT DETAIL' }}
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-3 text-center py-16">
                        <div class="flex flex-col items-center justify-center">
                            <div class="bg-gray-50 p-6 rounded-full mb-4">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-600">Belum Ada Periode Audit</h3>
                            @if(in_array(auth()->user()->role, ['kepala_lab', 'super_admin']))
                                <p class="text-sm text-gray-400 mt-1 max-w-md">Buat perintah audit baru untuk memulai proses audit inventaris laboratorium.</p>
                            @else
                                <p class="text-sm text-gray-400 mt-1 max-w-md">Menunggu perintah audit dari Kepala Lab.</p>
                            @endif
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Buat Perintah Audit (Kepala Lab / Super Admin) --}}
    @if(in_array(auth()->user()->role, ['kepala_lab', 'super_admin']))
    <div id="modal-buat-periode" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modal-buat-periode').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.audit.periode.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="bg-indigo-100 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Buat Perintah Audit Baru</h3>
                                <p class="text-xs text-gray-500">Teknisi akan menerima perintah ini untuk melakukan audit.</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Periode <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_periode" placeholder="cth: Audit Semester Genap 2025/2026" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_mulai" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                                    <input type="date" name="tanggal_selesai" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Audit <span class="text-red-500">*</span></label>
                                <select name="tipe" class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" required>
                                    <option value="semua">📦🏠 Semua (Barang & Ruangan)</option>
                                    <option value="barang">📦 Barang Saja</option>
                                    <option value="ruangan">🏠 Ruangan Saja</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan / Instruksi (Opsional)</label>
                                <textarea name="catatan" rows="3" placeholder="Instruksi tambahan untuk teknisi..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2">
                        <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-indigo-600 text-sm font-bold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            Kirim Perintah Audit
                        </button>
                        <button type="button" onclick="document.getElementById('modal-buat-periode').classList.add('hidden')" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
