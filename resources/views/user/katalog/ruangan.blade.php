<x-app-layout>
    <x-slot name="header">Katalog Ruangan</x-slot>

    <div class="max-w-7xl mx-auto mb-8">
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Gagal!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <ul class="list-disc pl-5 text-sm">
                    @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Booking Ruangan</h2>
                <p class="text-sm text-gray-500">Ajukan penggunaan ruangan untuk kelas, praktikum, atau kegiatan UKM.</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($ruangans as $ruangan)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-shadow">

                <div class="relative h-48 w-full overflow-hidden bg-gray-100">
                    @if($ruangan->foto_ruangan)
                        <img src="{{ asset('storage/foto_ruangan/' . $ruangan->foto_ruangan) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="{{ $ruangan->nama_ruangan }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    @endif

                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase shadow-sm {{ $ruangan->status_label == 'Tersedia' ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-white' }}">
                            {{ $ruangan->status_label }}
                        </span>
                    </div>
                </div>

                <div class="p-5 flex-1">
                    <h3 class="text-lg font-bold text-gray-800">{{ $ruangan->nama_ruangan }}</h3>
                    <div class="flex items-center text-gray-500 text-xs mb-3 mt-1">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $ruangan->lokasi ?? 'Lokasi belum diatur' }}
                    </div>

                    <div class="flex items-center text-gray-600 text-xs mb-4">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Kapasitas: <span class="font-bold ml-1">{{ $ruangan->kapasitas ?? '0' }} orang</span>
                    </div>

                    <div class="pt-4 border-t border-gray-100 mt-auto flex gap-2">
                        <button onclick="openJadwalModal({{ $ruangan->id }}, '{{ addslashes($ruangan->nama_ruangan) }}')" class="flex-1 bg-white border border-slate-300 text-slate-700 py-2.5 rounded-md text-xs font-bold hover:bg-slate-50 transition-colors shadow-sm">
                            LIHAT JADWAL
                        </button>
                        <button onclick="openBookingModal({{ $ruangan->id }}, '{{ addslashes($ruangan->nama_ruangan) }}')" class="flex-1 bg-slate-800 text-white py-2.5 rounded-md text-xs font-bold hover:bg-slate-700 transition-colors shadow-sm">
                            BOOKING
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-lg border border-gray-200 border-dashed">
                <p class="text-gray-500 font-medium">Belum ada data ruangan.</p>
            </div>
        @endforelse
    </div>

    <div id="bookingModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeBookingModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Form Pengajuan Booking</h3>

                <form action="{{ route('peminjam.katalog.ruangan.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="ruangan_id" id="modal_ruangan_id">

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Nama Ruangan</label>
                            <input type="text" id="modal_nama_ruangan" readonly class="w-full border-gray-200 bg-gray-50 text-gray-600 rounded-md mt-1 text-sm font-semibold cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Tanggal</label>
                            <input type="date" name="tanggal_booking" value="{{ date('Y-m-d') }}" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Jam Mulai</label>
                                <input type="time" name="waktu_mulai" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase">Jam Selesai</label>
                                <input type="time" name="waktu_selesai" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Keperluan Penggunaan</label>
                            <textarea name="keperluan" rows="2" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Sebutkan kegiatan..." required></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Surat Peminjaman <span class="text-gray-400 font-normal normal-case">(Opsional, PDF/Gambar)</span></label>
                            <input type="file" name="surat_peminjaman" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-sm text-gray-500 mt-1 file:mr-3 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeBookingModal()" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-gray-600">BATAL</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-xs font-bold shadow-md hover:bg-indigo-700 transition">AJUKAN BOOKING</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="jadwalModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeJadwalModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center border-b pb-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Jadwal Penggunaan Ruangan</h3>
                    <button onclick="closeJadwalModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <p id="jadwal_nama_ruangan" class="font-semibold text-indigo-600 mb-4"></p>
                
                <div class="overflow-y-auto max-h-96">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600">Jenis</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600">Keterangan</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600">Hari/Tanggal</th>
                                <th class="px-4 py-2 text-left font-semibold text-gray-600">Waktu</th>
                            </tr>
                        </thead>
                        <tbody id="jadwal_table_body" class="bg-white divide-y divide-gray-200">
                            <tr><td colspan="4" class="text-center py-4 text-gray-500">Memuat data...</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="button" onclick="closeJadwalModal()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md text-xs font-bold hover:bg-gray-300 transition">TUTUP</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBookingModal(id, nama) {
            document.getElementById('modal_ruangan_id').value = id;
            document.getElementById('modal_nama_ruangan').value = nama;
            document.getElementById('bookingModal').classList.remove('hidden');
        }

        function closeBookingModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }

        function openJadwalModal(id, nama) {
            document.getElementById('jadwal_nama_ruangan').textContent = 'Ruangan: ' + nama;
            document.getElementById('jadwalModal').classList.remove('hidden');
            document.getElementById('jadwal_table_body').innerHTML = '<tr><td colspan="4" class="text-center py-4 text-gray-500">Memuat data...</td></tr>';

            fetch(`/peminjam/katalog/ruangan/${id}/jadwal`)
                .then(response => response.json())
                .then(data => {
                    let html = '';
                    if (data.jadwal.length === 0) {
                        html = '<tr><td colspan="4" class="text-center py-6 text-gray-500 italic">Belum ada jadwal kuliah atau booking aktif.</td></tr>';
                    } else {
                        data.jadwal.forEach(item => {
                            let badge = item.jenis === 'Kuliah' 
                                ? '<span class="px-2 py-1 bg-indigo-100 text-indigo-700 text-[10px] font-bold rounded">KULIAH</span>'
                                : '<span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded">BOOKING</span>';
                            
                            html += `
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">${badge}</td>
                                    <td class="px-4 py-3 text-gray-700 font-medium">${item.keterangan}</td>
                                    <td class="px-4 py-3 text-gray-600">${item.hari_tanggal}</td>
                                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">${item.waktu}</td>
                                </tr>
                            `;
                        });
                    }
                    document.getElementById('jadwal_table_body').innerHTML = html;
                })
                .catch(error => {
                    document.getElementById('jadwal_table_body').innerHTML = '<tr><td colspan="4" class="text-center py-4 text-red-500">Gagal memuat data jadwal.</td></tr>';
                });
        }

        function closeJadwalModal() {
            document.getElementById('jadwalModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
