@extends('layouts.admin')

@section('content')
    <div
        class="mb-4 flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <h1 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">Data Santri & Pengguna</h1>
        <a href="{{ route('admin.users.create') }}"
            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Pengguna
        </a>
    </div>

    <!-- Filter & Search -->
    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.users') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
            <div class="flex-1">
                <label for="search" class="sr-only">Cari</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:border-blue-300 focus:ring focus:ring-blue-200 sm:text-sm"
                        placeholder="Cari nama, atau email...">
                </div>
            </div>
            <div class="xl:col-span-1">
                <select name="role"
                    class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="santri" {{ request('role') == 'santri' ? 'selected' : '' }}>Santri</option>
                </select>
            </div>
            <div class="xl:col-span-1">
                <select name="tahun_ajaran_id"
                    class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}"
                            {{ (string) request('tahun_ajaran_id') === (string) $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="xl:col-span-1">
                <select name="tingkatan" id="tingkatan-filter"
                    class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Tingkatan</option>
                    @foreach ($tingkatanOptions as $tingkatan)
                        <option value="{{ $tingkatan }}" {{ request('tingkatan') == $tingkatan ? 'selected' : '' }}>
                            {{ $tingkatan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="xl:col-span-1">
                <select name="kelas" id="kelas-filter"
                    class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Kelas</option>
                </select>
            </div>
            <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kontak
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data
                            Santri</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Akun</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div
                                            class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                            {{ substr($user->nama_santri ?? $user->name, 0, 2) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $user->nama_santri ?? $user->name }}</div>
                                        <div class="text-xs text-gray-500">Terdaftar:
                                            {{ $user->created_at->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                <div class="text-xs text-gray-500">{{ $user->no_telp ?? ($user->no_hp ?? '-') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->role === 'santri')
                                    <div class="text-sm text-gray-900">{{ $user->tingkatan ?? '-' }} •
                                        {{ $user->kelas ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">Ngaji: {{ $user->tingkatan_ngaji ?? '-' }}</div>
                                    <div class="text-xs text-gray-500">TA Masuk: {{ $user->tahunAjaranMasuk->nama ?? '-' }}
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500">Bukan data santri</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $user->role === 'santri' ? 'bg-green-100 text-green-800' : '' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                                <div class="mt-2">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ ($user->status ?? 'aktif') === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($user->status ?? 'aktif') }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="inline-flex items-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-lg p-2 transition-colors"
                                        title="Edit Pengguna">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded-lg p-2 transition-colors"
                                            title="Hapus Pengguna">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900">Tidak ada data pengguna ditemukan.</p>
                                    <p class="text-sm text-gray-500">Coba ubah filter pencarian atau buat pengguna baru.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $users->links() }}
        </div>
    </div>

    <script>
        const kelasOptionsByTingkatan = @json($kelasOptionsByTingkatan ?? []);
        const tingkatanSelect = document.getElementById('tingkatan-filter');
        const kelasSelect = document.getElementById('kelas-filter');
        const selectedKelas = @json(request('kelas'));

        function renderKelasOptions() {
            if (!tingkatanSelect || !kelasSelect) return;

            const tingkatan = tingkatanSelect.value;
            const options = kelasOptionsByTingkatan[tingkatan] || [];
            kelasSelect.innerHTML = '<option value="">Semua Kelas</option>';

            options.forEach((kelas) => {
                const option = document.createElement('option');
                option.value = kelas;
                option.textContent = kelas;
                if (selectedKelas === kelas) {
                    option.selected = true;
                }
                kelasSelect.appendChild(option);
            });
        }

        tingkatanSelect?.addEventListener('change', () => {
            kelasSelect.value = '';
            renderKelasOptions();
        });

        renderKelasOptions();
    </script>
@endsection
