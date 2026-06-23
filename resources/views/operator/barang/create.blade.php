<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Data Barang Fisik Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <div class="mb-4">
                        <a href="{{ route('barang.index') }}" class="text-blue-500 hover:text-blue-700">
                            &larr; Kembali ke Daftar Barang
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                            <strong class="font-bold">Gagal!</strong>
                            <ul class="list-disc pl-5 mt-1 text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('barang.store') }}" method="POST" enctype="multipart/form-data" id="form-barang">
                        @csrf 
                        
                        <div class="bg-gray-50 p-4 rounded-lg border mb-6">
                            <h3 class="font-bold text-lg mb-4 text-gray-700">1. Informasi Umum Barang</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Barang / Alat</label>
                                    <input type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required placeholder="Contoh: Mouse Logitech">
                                </div>
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Singkatan Kode (3-4 Huruf)</label>
                                    <input type="text" id="singkatan" name="singkatan" value="{{ old('singkatan') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline uppercase" required placeholder="Contoh: MS" maxlength="4">
                                    <p class="text-xs text-gray-500 mt-1">Akan digunakan untuk generate kode inventaris (Misal: INV.TRPL.MS...)</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Kategori</label>
                                    <select name="kategori_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($kategoris as $kategori)
                                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Lokasi Ruangan Default</label>
                                    <select name="ruangan_id" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                        <option value="">-- Pilih Ruangan --</option>
                                        @foreach($ruangans as $ruangan)
                                            <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Deskripsi / Spesifikasi Umum</label>
                                <textarea name="deskripsi" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Opsional: Tuliskan spesifikasi umum barang...">{{ old('deskripsi') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2">Foto Barang Default</label>
                                <input type="file" name="foto_barang" class="shadow border rounded w-full py-2 px-3 text-gray-700" accept="image/*">
                            </div>
                        </div>

                        <div class="bg-blue-50 p-4 rounded-lg border mb-6">
                            <h3 class="font-bold text-lg mb-4 text-blue-800">2. Input Data Item Fisik</h3>
                            <div class="flex items-end gap-4 mb-4">
                                <div class="w-1/3">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Barang Fisik</label>
                                    <input type="number" id="jumlah_item" min="1" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>
                                <div class="w-1/3">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Mulai Nomor Urut</label>
                                    <input type="number" id="mulai_nomor" min="1" value="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                </div>
                                <div>
                                    <button type="button" onclick="generateRows()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                                        Generate Baris Input
                                    </button>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white border border-gray-200 shadow-sm rounded-lg">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="py-2 px-3 border-b text-left text-sm font-semibold text-gray-600">No</th>
                                            <th class="py-2 px-3 border-b text-left text-sm font-semibold text-gray-600 w-1/4">Kode Inventaris</th>
                                            <th class="py-2 px-3 border-b text-left text-sm font-semibold text-gray-600">Merk / Tipe</th>
                                            <th class="py-2 px-3 border-b text-left text-sm font-semibold text-gray-600">Ruangan</th>
                                            <th class="py-2 px-3 border-b text-left text-sm font-semibold text-gray-600">Pemilik</th>
                                            <th class="py-2 px-3 border-b text-left text-sm font-semibold text-gray-600">Kondisi</th>
                                            <th class="py-2 px-3 border-b text-left text-sm font-semibold text-gray-600">Harga (Rp)</th>
                                            <th class="py-2 px-3 border-b text-left text-sm font-semibold text-gray-600">Foto</th>
                                            <th class="py-2 px-3 border-b text-center text-sm font-semibold text-gray-600">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="item_rows">
                                        <!-- Baris akan di-generate via JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg focus:outline-none focus:shadow-outline text-lg">
                                Simpan Semua Barang
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const daftarRuangan = @json($ruangans);
        let counter = 0;

        function generateKode(index, kepemilikan) {
            const singkatan = document.getElementById('singkatan').value.toUpperCase() || 'XXX';
            // Format angka urutan, misal 01, 02
            const urut = String(index).padStart(2, '0');
            if (kepemilikan === 'Prodi') {
                return `INV.TRPL.${singkatan}${urut}`;
            } else {
                return `INV.LAB-TRPL.${singkatan}${urut}`;
            }
        }

        function updateKode(btnOrSelect) {
            const row = btnOrSelect.closest('tr');
            const kepemilikan = row.querySelector('.select-kepemilikan').value;
            const urut = row.dataset.urut || row.dataset.index;
            const inputKode = row.querySelector('.input-kode');
            
            inputKode.value = generateKode(urut, kepemilikan);
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            reindexRows();
        }

        function reindexRows() {
            const rows = document.querySelectorAll('#item_rows tr');
            rows.forEach((row, idx) => {
                const newIndex = idx + 1;
                row.dataset.index = newIndex;
                row.querySelector('.row-number').textContent = newIndex;
                // Optional: update kode automatically on reindex
            });
            counter = rows.length;
        }

        function generateRows() {
            const jumlah = parseInt(document.getElementById('jumlah_item').value);
            const mulai = parseInt(document.getElementById('mulai_nomor').value) || 1;
            const tbody = document.getElementById('item_rows');
            
            if (!document.getElementById('singkatan').value) {
                alert('Mohon isi Singkatan Kode terlebih dahulu sebelum men-generate baris.');
                document.getElementById('singkatan').focus();
                return;
            }

            // Hapus yang ada sebelumnya
            tbody.innerHTML = '';
            
            for (let i = 1; i <= jumlah; i++) {
                const tr = document.createElement('tr');
                tr.dataset.index = i;
                const urut = mulai + i - 1;
                tr.dataset.urut = urut;
                
                const kodeDefault = generateKode(urut, 'Prodi');
                
                tr.innerHTML = `
                    <td class="py-2 px-3 border-b text-center row-number">${i}</td>
                    <td class="py-2 px-3 border-b">
                        <input type="text" name="items[${i}][kode_inventaris]" class="w-full border rounded px-2 py-1 input-kode font-mono text-sm" value="${kodeDefault}" required>
                    </td>
                    <td class="py-2 px-3 border-b">
                        <input type="text" name="items[${i}][merk]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Merk (opsional)">
                    </td>
                    <td class="py-2 px-3 border-b">
                        <select name="items[${i}][ruangan_id]" class="w-full border rounded px-2 py-1 text-sm">
                            <option value="">-- Gunakan Default --</option>
                            ${daftarRuangan.map(r => `<option value="${r.id}">${r.nama_ruangan}</option>`).join('')}
                        </select>
                    </td>
                    <td class="py-2 px-3 border-b">
                        <select name="items[${i}][kepemilikan]" class="w-full border rounded px-2 py-1 select-kepemilikan text-sm" onchange="updateKode(this)">
                            <option value="Prodi">Prodi</option>
                            <option value="Lab">Lab</option>
                        </select>
                    </td>
                    <td class="py-2 px-3 border-b">
                        <select name="items[${i}][kondisi]" class="w-full border rounded px-2 py-1 text-sm">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </td>
                    <td class="py-2 px-3 border-b">
                        <input type="number" name="items[${i}][harga]" class="w-full border rounded px-2 py-1 text-sm" placeholder="Harga">
                    </td>
                    <td class="py-2 px-3 border-b">
                        <input type="file" name="items[${i}][foto]" class="w-full text-[10px]" accept="image/*">
                    </td>
                    <td class="py-2 px-3 border-b text-center">
                        <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 font-bold px-2">&times;</button>
                    </td>
                `;
                tbody.appendChild(tr);
            }
            counter = jumlah;
        }

        // Restore old items if validation fails
        const oldItems = @json(old('items', []));
        
        document.addEventListener('DOMContentLoaded', () => {
            const tbody = document.getElementById('item_rows');
            if (Object.keys(oldItems).length > 0) {
                let i = 1;
                for (const key in oldItems) {
                    const item = oldItems[key];
                    const tr = document.createElement('tr');
                    tr.dataset.index = i;
                    
                    tr.innerHTML = `
                        <td class="py-2 px-3 border-b text-center row-number">${i}</td>
                        <td class="py-2 px-3 border-b">
                            <input type="text" name="items[${i}][kode_inventaris]" class="w-full border rounded px-2 py-1 input-kode font-mono text-sm" value="${item.kode_inventaris || ''}" required>
                        </td>
                        <td class="py-2 px-3 border-b">
                            <input type="text" name="items[${i}][merk]" class="w-full border rounded px-2 py-1 text-sm" value="${item.merk || ''}" placeholder="Merk (opsional)">
                        </td>
                        <td class="py-2 px-3 border-b">
                            <select name="items[${i}][ruangan_id]" class="w-full border rounded px-2 py-1 text-sm">
                                <option value="">-- Gunakan Default --</option>
                                ${daftarRuangan.map(r => `<option value="${r.id}" ${item.ruangan_id == r.id ? 'selected' : ''}>${r.nama_ruangan}</option>`).join('')}
                            </select>
                        </td>
                        <td class="py-2 px-3 border-b">
                            <select name="items[${i}][kepemilikan]" class="w-full border rounded px-2 py-1 select-kepemilikan text-sm" onchange="updateKode(this)">
                                <option value="Prodi" ${item.kepemilikan === 'Prodi' ? 'selected' : ''}>Prodi</option>
                                <option value="Lab" ${item.kepemilikan === 'Lab' ? 'selected' : ''}>Lab</option>
                            </select>
                        </td>
                        <td class="py-2 px-3 border-b">
                            <select name="items[${i}][kondisi]" class="w-full border rounded px-2 py-1 text-sm">
                                <option value="Baik" ${item.kondisi === 'Baik' ? 'selected' : ''}>Baik</option>
                                <option value="Rusak Ringan" ${item.kondisi === 'Rusak Ringan' ? 'selected' : ''}>Rusak Ringan</option>
                                <option value="Rusak Berat" ${item.kondisi === 'Rusak Berat' ? 'selected' : ''}>Rusak Berat</option>
                            </select>
                        </td>
                        <td class="py-2 px-3 border-b">
                            <input type="number" name="items[${i}][harga]" class="w-full border rounded px-2 py-1 text-sm" value="${item.harga || ''}" placeholder="Harga">
                        </td>
                        <td class="py-2 px-3 border-b">
                            <input type="file" name="items[${i}][foto]" class="w-full text-[10px]" accept="image/*">
                        </td>
                        <td class="py-2 px-3 border-b text-center">
                            <button type="button" onclick="removeRow(this)" class="text-red-500 hover:text-red-700 font-bold px-2">&times;</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                    i++;
                }
                document.getElementById('jumlah_item').value = i - 1;
                counter = i - 1;
            }
        });
    </script>
</x-app-layout>
