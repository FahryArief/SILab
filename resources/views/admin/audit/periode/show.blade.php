<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.audit.periode.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $periode->nama_periode }}
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
            <div class="flex items-center gap-2">
                {{-- Teknisi: Laporkan Hasil --}}
                @if(auth()->user()->role === 'teknisi' && in_array($periode->status, ['open', 'revisi']))
                <form action="{{ route('admin.audit.periode.laporkan', $periode->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melaporkan hasil audit ini ke Kepala Lab? Pastikan semua data audit sudah terisi.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-5 rounded-lg shadow-lg flex items-center transition duration-300 text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Laporkan Hasil Audit
                    </button>
                </form>
                @endif

                {{-- Kepala Lab: Validasi Periode --}}
                @if(in_array(auth()->user()->role, ['kepala_lab', 'super_admin']) && $periode->status === 'dilaporkan')
                <div class="flex items-center gap-2">
                    <button onclick="document.getElementById('modal-reject-periode').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center transition duration-300 text-sm">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Tolak / Revisi
                    </button>
                    <form action="{{ route('admin.audit.periode.validate', $periode->id) }}" method="POST" onsubmit="return confirm('Setujui laporan audit ini? Setelah disetujui, data tidak bisa diubah lagi.')">
                        @csrf @method('PATCH')
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-lg flex items-center transition duration-300 text-sm">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Setujui Audit
                        </button>
                    </form>
                </div>
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

            {{-- Periode Info Card --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase">Periode</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">
                            <svg class="w-4 h-4 inline text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            {{ $periode->tanggal_mulai->format('d M Y') }} — {{ $periode->tanggal_selesai->format('d M Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase">Tipe Audit</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">
                            @if($periode->tipe === 'barang') 📦 Barang
                            @elseif($periode->tipe === 'ruangan') 🏠 Ruangan
                            @else 📦🏠 Barang & Ruangan @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase">Diperintahkan Oleh</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $periode->kepalaLab->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 font-medium uppercase">Progress</p>
                        <p class="text-sm font-bold text-gray-800 mt-0.5">
                            <span class="text-indigo-600">{{ $totalAuditBarang + $totalAuditRuangan }}</span> item telah diaudit
                        </p>
                    </div>
                </div>
                @if($periode->catatan)
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-400 font-medium uppercase mb-1">Instruksi Kepala Lab</p>
                    <p class="text-sm text-gray-600 italic bg-gray-50 p-3 rounded-lg">💬 {{ $periode->catatan }}</p>
                </div>
                @endif
                @if($periode->catatan_revisi)
                <div class="mt-4 pt-4 border-t border-red-100">
                    <p class="text-xs text-red-500 font-medium uppercase mb-1">Catatan Revisi dari Kepala Lab</p>
                    <p class="text-sm text-red-700 font-medium italic bg-red-50 p-3 rounded-lg">⚠️ {{ $periode->catatan_revisi }}</p>
                </div>
                @endif
            </div>

            {{-- Cards for Separate Pages --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                {{-- Card Audit Barang --}}
                @if(in_array($periode->tipe, ['barang', 'semua']))
                <a href="{{ route('admin.audit.periode.barang', $periode->id) }}" class="block bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:border-indigo-300 transition-all group overflow-hidden">
                    <div class="p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="bg-indigo-50 p-4 rounded-xl group-hover:bg-indigo-100 transition-colors">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">Audit Data Barang</h3>
                                <p class="text-sm text-gray-500 mt-1">Periksa kondisi seluruh barang inventaris laboratorium.</p>
                            </div>
                        </div>
                        <svg class="w-6 h-6 text-gray-300 group-hover:text-indigo-600 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-500">Progress Audit</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold {{ $totalAuditBarang === $totalBarangTarget ? 'text-green-600' : 'text-indigo-600' }}">{{ $totalAuditBarang }} / {{ $totalBarangTarget }}</span>
                            <div class="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $totalAuditBarang === $totalBarangTarget ? 'bg-green-500' : 'bg-indigo-500' }}" style="width: {{ $totalBarangTarget > 0 ? ($totalAuditBarang / $totalBarangTarget) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </a>
                @endif

                {{-- Card Audit Ruangan --}}
                @if(in_array($periode->tipe, ['ruangan', 'semua']))
                <a href="{{ route('admin.audit.periode.ruangan', $periode->id) }}" class="block bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md hover:teal-300 transition-all group overflow-hidden">
                    <div class="p-6 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="bg-teal-50 p-4 rounded-xl group-hover:bg-teal-100 transition-colors">
                                <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 group-hover:text-teal-600 transition-colors">Audit Data Ruangan</h3>
                                <p class="text-sm text-gray-500 mt-1">Periksa kondisi seluruh ruangan laboratorium.</p>
                            </div>
                        </div>
                        <svg class="w-6 h-6 text-gray-300 group-hover:text-teal-600 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-xs font-medium text-gray-500">Progress Audit</span>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold {{ $totalAuditRuangan === $totalRuanganTarget ? 'text-green-600' : 'text-teal-600' }}">{{ $totalAuditRuangan }} / {{ $totalRuanganTarget }}</span>
                            <div class="w-24 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $totalAuditRuangan === $totalRuanganTarget ? 'bg-green-500' : 'bg-teal-500' }}" style="width: {{ $totalRuanganTarget > 0 ? ($totalAuditRuangan / $totalRuanganTarget) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Reject/Revisi --}}
    @if(in_array(auth()->user()->role, ['kepala_lab', 'super_admin']) && $periode->status === 'dilaporkan')
    <div id="modal-reject-periode" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="document.getElementById('modal-reject-periode').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('admin.audit.periode.validate', $periode->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="action" value="reject">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="bg-red-100 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Tolak & Minta Revisi</h3>
                                <p class="text-xs text-gray-500">Berikan catatan apa yang perlu direvisi oleh teknisi.</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Revisi <span class="text-red-500">*</span></label>
                                <textarea name="catatan_revisi" rows="3" placeholder="Jelaskan alasan penolakan dan instruksi revisi..." class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-red-500 focus:border-red-500 text-sm" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-2">
                        <button type="submit" class="inline-flex justify-center rounded-lg border border-transparent shadow-sm px-5 py-2.5 bg-red-600 text-sm font-bold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                            Tolak Audit
                        </button>
                        <button type="button" onclick="document.getElementById('modal-reject-periode').classList.add('hidden')" class="inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-5 py-2.5 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
