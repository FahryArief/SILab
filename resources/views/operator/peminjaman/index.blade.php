<x-app-layout>
    <x-slot name="header">Peminjaman Barang</x-slot>

    <div class="max-w-7xl mx-auto mb-4">
        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Kelola Peminjaman Alat</h2>
                <p class="text-sm text-gray-500">Validasi, ACC, dan kelola pengembalian barang laboratorium.</p>
            </div>
            <button onclick="openModal()" class="bg-[#1e293b] text-white px-4 py-2 rounded-md text-sm font-bold shadow hover:bg-slate-700 transition flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Peminjaman Baru (Manual)
            </button>
        </div>

        {{-- Alur Persetujuan Info --}}
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-xs text-blue-700 font-semibold mb-2">Alur Persetujuan Peminjaman:</p>
            <div class="flex items-center gap-2 text-xs text-blue-600">
                <span class="bg-amber-100 text-amber-700 px-2 py-1 rounded font-bold">1. Pending</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded font-bold">2. Validasi Teknisi</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded font-bold">3. ACC Kepala Lab</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded font-bold">4. Dikembalikan</span>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 font-semibold">ID</th>
                            <th class="px-6 py-4 font-semibold">Peminjam</th>
                            <th class="px-6 py-4 font-semibold">Barang Dipinjam</th>
                            <th class="px-6 py-4 font-semibold">Durasi</th>
                            <th class="px-6 py-4 font-semibold">Keperluan</th>
                            <th class="px-6 py-4 font-semibold">Surat</th>
                            <th class="px-6 py-4 font-semibold">Status</th>
                            <th class="px-6 py-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($peminjamans as $pinjam)
                        <tr class="hover:bg-gray-50 transition-colors hover-lift">
                            <td class="px-6 py-4">
                                <div class="text-[10px] text-gray-400 font-mono">PMJ-{{ str_pad($pinjam->id, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 text-sm">
                                    {{ $pinjam->user ? $pinjam->user->name : $pinjam->nama_peminjam }}
                                </div>
                                @if($pinjam->user)
                                    <div class="text-[10px] text-gray-400">{{ $pinjam->user->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($pinjam->barangs as $brg)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono bg-indigo-50 text-indigo-700 border border-indigo-200">
                                            {{ $brg->barcode }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $pinjam->barangs->count() }} item</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs text-gray-600"><span class="font-bold text-gray-700">Mulai:</span> {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d M Y') }}</div>
                                <div class="text-xs text-gray-600 mt-1"><span class="font-bold text-gray-700">Tenggat:</span> {{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-600 max-w-[150px] truncate" title="{{ $pinjam->keperluan }}">
                                {{ $pinjam->keperluan }}
                            </td>
                            <td class="px-6 py-4">
                                @if($pinjam->surat_peminjaman)
                                    <button type="button" onclick="openSuratModal('{{ asset('storage/surat_peminjaman/' . $pinjam->surat_peminjaman) }}')" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold underline">
                                        Lihat Surat
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($pinjam->status == 'pending')
                                    <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-amber-100 text-amber-600">
                                        Menunggu Validasi
                                    </span>
                                @elseif($pinjam->status == 'divalidasi_teknisi')
                                    <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-indigo-100 text-indigo-600">
                                        Divalidasi Teknisi
                                    </span>
                                @elseif($pinjam->status == 'disetujui')
                                    <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-600">
                                        ACC Kepala Lab
                                    </span>
                                @elseif($pinjam->status == 'ditolak')
                                    <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-red-100 text-red-600 border border-red-200">
                                        Ditolak
                                    </span>
                                @elseif($pinjam->status == 'dikembalikan')
                                    <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-gray-100 text-gray-500 border border-gray-200">
                                        Dikembalikan
                                    </span>
                                @endif

                                @if($pinjam->catatan_admin)
                                    <div class="mt-2 text-[10px] text-gray-500 italic border-l-2 border-gray-300 pl-2">
                                        <span class="font-bold">Catatan:</span> {{ $pinjam->catatan_admin }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2 flex-wrap">
                                    {{-- Teknisi: Validasi pengajuan pending --}}
                                    @if($pinjam->status == 'pending' && in_array(auth()->user()->role, ['teknisi', 'super_admin']))
                                        <button onclick="openActionModal('{{ route('peminjaman.approve', $pinjam->id) }}', 'Validasi Peminjaman', 'bg-indigo-600', 'Validasi')" class="text-[10px] bg-indigo-500 text-white px-3 py-1.5 rounded shadow-sm hover:bg-indigo-600 transition">Validasi</button>
                                        <button onclick="openActionModal('{{ route('peminjaman.reject', $pinjam->id) }}', 'Tolak Peminjaman', 'bg-red-600', 'Tolak')" class="text-[10px] bg-red-500 text-white px-3 py-1.5 rounded shadow-sm hover:bg-red-600 transition">Tolak</button>
                                    @endif

                                    {{-- Kepala Lab: ACC pengajuan yang sudah divalidasi --}}
                                    @if($pinjam->status == 'divalidasi_teknisi' && in_array(auth()->user()->role, ['kepala_lab', 'super_admin']))
                                        <button onclick="openActionModal('{{ route('kepala_lab.peminjaman.acc', $pinjam->id) }}', 'Setujui Peminjaman (ACC)', 'bg-emerald-600', 'ACC')" class="text-[10px] bg-emerald-500 text-white px-3 py-1.5 rounded shadow-sm hover:bg-emerald-600 transition">ACC / Setujui</button>
                                        <button onclick="openActionModal('{{ route('peminjaman.reject', $pinjam->id) }}', 'Tolak Peminjaman', 'bg-red-600', 'Tolak')" class="text-[10px] bg-red-500 text-white px-3 py-1.5 rounded shadow-sm hover:bg-red-600 transition">Tolak</button>
                                    @endif

                                    {{-- Teknisi / Admin: Tandai barang sudah dikembalikan --}}
                                    @if($pinjam->status == 'disetujui')
                                        <form action="{{ route('peminjaman.kembalikan', $pinjam->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" onclick="return confirm('Apakah barang sudah dikembalikan ke Lab?')" class="text-[10px] bg-slate-800 text-white px-3 py-1.5 rounded shadow-sm hover:bg-slate-700 transition">Tandai Kembali</button>
                                        </form>
                                    @endif

                                    @if(in_array($pinjam->status, ['ditolak', 'dikembalikan']))
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center animate-in">
                                    <div class="bg-gray-50 p-4 rounded-full mb-4">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-700">Belum Ada Peminjaman</h3>
                                    <p class="text-sm text-gray-500 max-w-xs mx-auto mt-1">Data peminjaman barang laboratorium akan muncul di sini setelah diajukan atau diinput.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $peminjamans->links() }}
        </div>
    </div>

    {{-- Modal Peminjaman Manual --}}
    <div id="peminjamanModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Form Peminjaman Manual</h3>

                <form action="{{ route('peminjaman.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Akun Mahasiswa</label>
                                <select name="user_id" class="w-full border-gray-300 rounded-md mt-1 text-sm">
                                    <option value="">-- Manual (Tanpa Akun) --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Nama (Jika Manual)</label>
                                <input type="text" name="nama_peminjam" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Misal: Dosen / Tamu">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Pilih Barang Fisik (Multi-Select)</label>
                            <div class="mt-1 max-h-40 overflow-y-auto border border-gray-200 rounded-md p-2">
                                @foreach($barangs as $b)
                                    <label class="flex items-center gap-2 py-1 {{ $b->kondisi == 'Baik' ? 'hover:bg-gray-50 cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50' }} px-2 rounded">
                                        <input type="checkbox" name="barang_ids[]" value="{{ $b->id }}" 
                                            class="text-indigo-600 border-gray-300 rounded disabled:bg-gray-200"
                                            {{ $b->kondisi !== 'Baik' ? 'disabled' : '' }}>
                                        <span class="text-sm text-gray-700">{{ $b->nama_barang }}</span>
                                        @if($b->kondisi !== 'Baik')
                                            <span class="text-[10px] text-red-500 font-bold ml-1">(Rusak)</span>
                                        @endif
                                        <span class="text-[10px] font-mono text-gray-400 ml-auto">{{ $b->barcode }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Tgl Pinjam</label>
                                <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Tgl Kembali</label>
                                <input type="date" name="tanggal_kembali" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Keperluan</label>
                            <textarea name="keperluan" rows="2" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Misal: Praktikum Jaringan" required></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Surat Peminjaman (Opsional)</label>
                            <input type="file" name="surat_peminjaman" class="w-full border-gray-300 rounded-md mt-1 text-sm bg-gray-50" accept=".pdf,image/*">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-gray-600">BATAL</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-xs font-bold shadow-md hover:bg-indigo-700 transition">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('peminjamanModal').classList.remove('hidden'); }
        function closeModal() { document.getElementById('peminjamanModal').classList.add('hidden'); }

        function openActionModal(actionUrl, title, buttonColorClass, buttonText) {
            const modal = document.getElementById('actionModal');
            document.getElementById('actionForm').action = actionUrl;
            document.getElementById('actionTitle').textContent = title;
            
            const btn = document.getElementById('actionBtn');
            btn.textContent = buttonText;
            btn.className = `px-4 py-2 rounded-md text-xs font-bold shadow-md text-white transition ${buttonColorClass}`;
            
            modal.classList.remove('hidden');
        }

        function closeActionModal() {
            document.getElementById('actionModal').classList.add('hidden');
            document.getElementById('actionForm').reset();
        }
    </script>

    {{-- Modal Konfirmasi Aksi (Setujui / Tolak) --}}
    <div id="actionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeActionModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-sm w-full p-6">
                <h3 id="actionTitle" class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Konfirmasi</h3>

                <form id="actionForm" action="" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Catatan Admin (Opsional)</label>
                        <textarea name="catatan" rows="3" class="w-full border-gray-300 rounded-md mt-1 text-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="Tambahkan pesan, alasan, atau instruksi..."></textarea>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeActionModal()" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-gray-600">BATAL</button>
                        <button type="submit" id="actionBtn" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-xs font-bold shadow-md hover:bg-indigo-700 transition">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
