<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Pengguna</h2>
            <!-- <button onclick="openModal('modal-tambah')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow flex items-center transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Pengguna
            </button> -->
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex justify-end mb-4">
                <button onclick="document.getElementById('modal-tambah').classList.remove('hidden')" class="bg-[#1e293b] text-white px-4 py-2 rounded-lg text-sm font-bold shadow hover:bg-slate-700 transition flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Pengguna
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terdaftar</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $user->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($user->role === 'super_admin') bg-red-100 text-red-800 
                                            @elseif($user->role === 'teknisi') bg-yellow-100 text-yellow-800 
                                            @elseif($user->role === 'kepala_lab') bg-blue-100 text-blue-800 
                                            @elseif($user->role === 'ka_prodi') bg-purple-100 text-purple-800 
                                            @else bg-green-100 text-green-800 @endif
                                        ">
                                            {{ str_replace('_', ' ', strtoupper($user->role)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex justify-center gap-2">
                                            {{-- Tombol Edit --}}
                                            <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')" class="text-indigo-600 hover:text-indigo-900 text-xs font-bold bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded transition">
                                                Edit
                                            </button>

                                            {{-- Tombol Hapus --}}
                                            @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pengguna {{ $user->name }}?');" class="inline-block">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-bold bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded transition">
                                                    Hapus
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-gray-400 text-xs font-bold bg-gray-50 px-3 py-1.5 rounded cursor-not-allowed">Anda</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tambah Pengguna Baru --}}
    <div id="modal-tambah" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeModal('modal-tambah')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Tambah Pengguna Baru</h3>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                            <input type="text" name="name" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Contoh: Ahmad Fadli" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Email</label>
                            <input type="email" name="email" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="contoh@polinela.ac.id" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Password</label>
                            <input type="password" name="password" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Min. 6 karakter" required minlength="6">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Role / Jabatan</label>
                            <select name="role" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                                <option value="peminjam">Peminjam (Mahasiswa/Pengguna)</option>
                                <option value="teknisi">Teknisi</option>
                                <option value="kepala_lab">Kepala Lab</option>
                                <option value="ka_prodi">Ka Prodi</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('modal-tambah')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-gray-600">BATAL</button>
                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-xs font-bold shadow-md hover:bg-indigo-700 transition">SIMPAN</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Pengguna --}}
    <div id="modal-edit" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeModal('modal-edit')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Edit Data Pengguna</h3>
                <form id="form-edit" method="POST">
                    @csrf @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Nama Lengkap</label>
                            <input type="text" name="name" id="edit-name" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Email</label>
                            <input type="email" name="email" id="edit-email" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Password Baru <span class="text-gray-400 normal-case">(kosongkan jika tidak diubah)</span></label>
                            <input type="password" name="password" class="w-full border-gray-300 rounded-md mt-1 text-sm" placeholder="Min. 6 karakter" minlength="6">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase">Role / Jabatan</label>
                            <select name="role" id="edit-role" class="w-full border-gray-300 rounded-md mt-1 text-sm" required>
                                <option value="peminjam">Peminjam (Mahasiswa/Pengguna)</option>
                                <option value="teknisi">Teknisi</option>
                                <option value="kepala_lab">Kepala Lab</option>
                                <option value="ka_prodi">Ka Prodi</option>
                                <option value="super_admin">Super Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('modal-edit')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-gray-600">BATAL</button>
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded-md text-xs font-bold shadow-md hover:bg-yellow-600 transition">UPDATE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        function openEditModal(userId, name, email, role) {
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-role').value = role;
            document.getElementById('form-edit').action = '/admin/users/' + userId;
            openModal('modal-edit');
        }
    </script>
</x-app-layout>
