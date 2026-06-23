<x-app-layout>
    <x-slot name="header">Booking Ruang</x-slot>

    <div class="max-w-7xl mx-auto mb-4">
        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Berhasil!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    
    @php
        $currentRoute = request()->route()->getName();
    @endphp

    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-6">

    <div class="w-full lg:w-1/3 bg-white border border-gray-200 rounded-lg p-6 shadow-sm self-start">

            <div class="flex justify-between items-center mb-6">
                <a href="{{ route($currentRoute, ['date' => $prevMonth]) }}" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 rounded transition">&lt;</a>
                <h3 class="font-bold text-gray-700">{{ $monthName }}</h3>
                <a href="{{ route($currentRoute, ['date' => $nextMonth]) }}" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-1 rounded transition">&gt;</a>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-xs mb-2">
                <div class="font-bold text-gray-400">Su</div><div class="font-bold text-gray-400">Mo</div>
                <div class="font-bold text-gray-400">Tu</div><div class="font-bold text-gray-400">We</div>
                <div class="font-bold text-gray-400">Th</div><div class="font-bold text-gray-400">Fr</div>
                <div class="font-bold text-gray-400">Sa</div>
            </div>

            <div class="grid grid-cols-7 gap-1 text-center text-xs">
                @for($i = 0; $i < $startDayOfWeek; $i++)
                    <div></div>
                @endfor

                @for($i = 1; $i <= $daysInMonth; $i++)
                    @php
                        // Format YYYY-MM-DD untuk dicocokkan dengan URL
                        $loopDate = $currentYear . '-' . $currentMonth . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $isSelected = ($loopDate == $selectedDate);
                    @endphp

                    <a href="{{ route($currentRoute, ['date' => $loopDate]) }}"
                       class="block py-2 rounded-full cursor-pointer transition-colors {{ $isSelected ? 'bg-indigo-600 text-white font-bold shadow-md hover:bg-indigo-700' : 'text-gray-600 hover:bg-indigo-50' }}">
                        {{ $i }}
                    </a>
                @endfor
            </div>

        </div>

        <div class="flex-1 bg-white border border-gray-200 rounded-lg shadow-sm flex flex-col overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-700 uppercase text-xs tracking-widest">Daftar Booking Tanggal {{ date('d M Y', strtotime($selectedDate)) }}</h3>
                <button onclick="openModal()" class="bg-[#1e293b] text-white px-4 py-2 rounded-md text-xs font-bold hover:bg-slate-700 transition">
                    + Booking Ruang Baru
                </button>
            </div>

            <div class="p-6 space-y-4">
                @forelse($bookings as $booking)
                <div class="group border border-gray-100 rounded-lg p-4 hover:border-indigo-200 transition-colors flex justify-between items-center">
                    <div class="flex items-start">
                        <div class="w-1 bg-indigo-500 self-stretch rounded-full mr-4"></div>
                        <div>
                            <h4 class="font-bold text-gray-800 text-sm">{{ $booking->keperluan }}</h4>
                            <p class="text-xs text-gray-500 mt-1">{{ $booking->ruangan->nama_ruangan }}</p>
                            <div class="flex items-center text-[10px] text-gray-400 mt-2">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}
                                <span class="mx-2">•</span>
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                {{ $booking->user ? $booking->user->name : $booking->nama_peminjam }}
                                <span class="mx-2">•</span>
                                {{ date('d M Y', strtotime($booking->tanggal_booking)) }}
                                <span class="mx-2">•</span>
                                {{ optional($booking->created_at)->locale('id')->diffForHumans() }}

                            </div>
                            @if($booking->surat_peminjaman)
                                <button type="button" onclick="openSuratModal('{{ asset('storage/surat_peminjaman/' . $booking->surat_peminjaman) }}')" class="mt-2 text-xs text-indigo-600 font-bold hover:underline">
                                    📄 Lihat Surat Peminjaman
                                </button>
                            @endif
                        </div>
                    </div>
                <div class="flex flex-col items-end gap-2">
                        @if($booking->status == 'pending')
                            <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-amber-100 text-amber-600">
                                Menunggu Acc
                            </span>
                            <div class="flex gap-2 mt-1">
                                <form action="{{ route('booking.approve', $booking->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-[10px] bg-emerald-500 text-white px-3 py-1 rounded shadow-sm hover:bg-emerald-600 transition">
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('booking.reject', $booking->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" onclick="return confirm('Yakin ingin menolak pengajuan ini?')" class="text-[10px] bg-red-500 text-white px-3 py-1 rounded shadow-sm hover:bg-red-600 transition">
                                        Tolak
                                    </button>
                                </form>
                            </div>

                        @elseif($booking->status == 'disetujui')
                            <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-emerald-100 text-emerald-600">
                                Disetujui
                            </span>
                            <form action="{{ route('booking.selesai', $booking->id) }}" method="POST" class="mt-1">
                                @csrf @method('PATCH')
                                <button type="submit" onclick="return confirm('Apakah ruangan ini sudah selesai digunakan?')" class="text-[10px] bg-slate-800 text-white px-3 py-1 rounded shadow-sm hover:bg-slate-700 transition">
                                    Selesaikan
                                </button>
                            </form>

                        @elseif($booking->status == 'ditolak')
                            <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-red-100 text-red-600 border border-red-200">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Ditolak
                            </span>

                        @elseif($booking->status == 'selesai')
                            <span class="px-2 py-1 rounded text-[9px] font-bold uppercase bg-gray-100 text-gray-500 border border-gray-200">
                                <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Selesai
                            </span>
                        @endif
                        </div>
                </div>
                @empty
                <div class="text-center py-10">
                    <p class="text-gray-400 text-sm italic">Belum ada jadwal untuk tanggal ini.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===== TABEL SEMUA BOOKING ===== --}}
    <div class="max-w-7xl mx-auto mt-8">
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">

            {{-- Header & Filter --}}
            <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-3">
                <h3 class="font-bold text-gray-700 uppercase text-xs tracking-widest">Semua Riwayat Booking Ruangan</h3>
                <form method="GET" action="{{ route($currentRoute) }}" class="flex gap-2 flex-wrap items-center">
                    @if(request('date'))
                        <input type="hidden" name="date" value="{{ request('date') }}">
                    @endif
                    <input type="text" name="search" value="{{ $searchBooking }}" placeholder="Cari peminjam / ruangan / keperluan..." class="border border-gray-300 rounded-md px-3 py-1.5 text-xs w-60 focus:ring-indigo-500 focus:border-indigo-500">
                    <select name="filter_status" class="border border-gray-300 rounded-md px-3 py-1.5 text-xs focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ $filterStatus == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ $filterStatus == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ $filterStatus == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="selesai" {{ $filterStatus == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <button type="submit" class="bg-slate-800 text-white px-4 py-1.5 rounded-md text-xs font-bold hover:bg-slate-700 transition">Filter</button>
                    @if($searchBooking || $filterStatus)
                        <a href="{{ route($currentRoute) }}" class="text-xs text-red-400 hover:text-red-600 font-bold">× Reset</a>
                    @endif
                </form>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold">Peminjam</th>
                            <th class="px-5 py-3 text-left font-semibold">Ruangan</th>
                            <th class="px-5 py-3 text-left font-semibold">Tanggal & Waktu</th>
                            <th class="px-5 py-3 text-left font-semibold">Keperluan</th>
                            <th class="px-5 py-3 text-left font-semibold">Surat</th>
                            <th class="px-5 py-3 text-left font-semibold">Status</th>
                            <th class="px-5 py-3 text-left font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($allBookings as $b)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="font-semibold text-gray-800 text-xs">{{ $b->user ? $b->user->name : ($b->nama_peminjam ?? '-') }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">{{ $b->user ? ucfirst($b->user->role) : 'Tamu' }}</div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-gray-700 text-xs">{{ $b->ruangan->nama_ruangan ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $b->ruangan->lokasi ?? '' }}</div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-700 text-xs">{{ \Carbon\Carbon::parse($b->tanggal_booking)->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-500 font-mono mt-0.5">{{ substr($b->waktu_mulai,0,5) }} – {{ substr($b->waktu_selesai,0,5) }}</div>
                            </td>
                            <td class="px-5 py-4 max-w-[180px]">
                                <span class="text-xs text-gray-600" title="{{ $b->keperluan }}">{{ \Illuminate\Support\Str::limit($b->keperluan, 50) }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($b->surat_peminjaman)
                                    <button type="button" onclick="openSuratModal('{{ asset('storage/surat_peminjaman/' . $b->surat_peminjaman) }}')" class="text-indigo-600 hover:text-indigo-800 text-xs font-bold underline">Lihat</button>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if($b->status == 'pending')
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-[10px] font-bold rounded uppercase">Menunggu</span>
                                @elseif($b->status == 'disetujui')
                                    <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded uppercase">Disetujui</span>
                                @elseif($b->status == 'ditolak')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-[10px] font-bold rounded uppercase">Ditolak</span>
                                @elseif($b->status == 'selesai')
                                    <span class="px-2 py-1 bg-gray-100 text-gray-500 text-[10px] font-bold rounded uppercase">Selesai</span>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex gap-1.5 flex-wrap">
                                    @if($b->status == 'pending')
                                        <form action="{{ route('booking.approve', $b->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-[10px] bg-emerald-500 text-white px-2.5 py-1 rounded shadow-sm hover:bg-emerald-600 transition font-bold">Setujui</button>
                                        </form>
                                        <form action="{{ route('booking.reject', $b->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" onclick="return confirm('Tolak pengajuan ini?')" class="text-[10px] bg-red-500 text-white px-2.5 py-1 rounded shadow-sm hover:bg-red-600 transition font-bold">Tolak</button>
                                        </form>
                                    @elseif($b->status == 'disetujui')
                                        <form action="{{ route('booking.selesai', $b->id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <button type="submit" onclick="return confirm('Tandai ruangan sudah selesai digunakan?')" class="text-[10px] bg-slate-700 text-white px-2.5 py-1 rounded shadow-sm hover:bg-slate-800 transition font-bold">Selesaikan</button>
                                        </form>
                                    @else
                                        <span class="text-[10px] text-gray-400 italic">-</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 text-sm italic">Belum ada data booking yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($allBookings->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $allBookings->links() }}
            </div>
            @endif

        </div>
    </div>

    <div id="bookingModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeModal()"></div>

            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Form Reservasi Ruangan</h3>

                <form action="{{ route('booking.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Akun Peminjam (Opsional)</label>
                            <select name="user_id" class="w-full border-gray-300 rounded-md mt-1 text-sm">
                                <option value="">-- Tidak Punya Akun (Isi nama di bawah) --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Nama Peminjam Manual</label>
                            <input type="text" name="nama_peminjam" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Isi jika tidak punya akun (Misal: Pak Budi - Dosen)">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Pilih Ruangan</label>
                            <select name="ruangan_id" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                                @foreach(\App\Models\Ruangan::all() as $r)
                                    <option value="{{ $r->id }}">{{ $r->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Tanggal</label>
                            <input type="date" name="tanggal_booking" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Mulai</label>
                                <input type="time" name="waktu_mulai" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Selesai</label>
                                <input type="time" name="waktu_selesai" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Keperluan</label>
                            <textarea name="keperluan" rows="3" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Misal: Rapat Koordinasi UKM SMART" required></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Surat Peminjaman <span class="text-gray-400 font-normal normal-case">(Opsional)</span></label>
                            <input type="file" name="surat_peminjaman" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 mt-1 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-gray-600">BATAL</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-xs font-bold shadow-md hover:bg-indigo-700 transition">SIMPAN JADWAL</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('bookingModal').classList.remove('hidden'); }
        function closeModal() { document.getElementById('bookingModal').classList.add('hidden'); }
    </script>
</x-app-layout>
