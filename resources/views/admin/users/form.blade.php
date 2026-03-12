@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $user ? 'Edit Pengguna: ' . $user->nama_santri : 'Tambah Pengguna Baru' }}</h1>
            <a href="{{ route('admin.users') }}" class="text-gray-500 hover:text-gray-700 font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ $user ? route('admin.users.update', $user->id) : route('admin.users.store') }}" method="POST">
            @csrf
            @if ($user)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Data Akun -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Informasi Akun</h3>
                </div>

                <!-- Nama Lengkap -->
                <div class="col-span-1 md:col-span-2">
                    <label for="nama_santri" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_santri" id="nama_santri"
                        value="{{ old('nama_santri', $user->nama_santri ?? '') }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                        placeholder="Nama lengkap pengguna">
                    @error('nama_santri')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}"
                        required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                        placeholder="email@example.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role / Peran</label>
                    <select name="role" id="role" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">Pilih Role</option>
                        <option value="admin" {{ old('role', $user->role ?? '') == 'admin' ? 'selected' : '' }}>Admin
                            (Administrator)</option>
                        <option value="santri" {{ old('role', $user->role ?? '') == 'santri' ? 'selected' : '' }}>Santri
                            (Siswa)</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        Password {{ $user ? '(Kosongkan jika tidak diubah)' : '' }}
                    </label>
                    <input type="password" name="password" id="password" {{ $user ? '' : 'required' }}
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                        placeholder="********">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi
                        Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        {{ $user ? '' : 'required' }}
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                        placeholder="********">
                </div>

                <!-- Status -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Akun</label>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="status" value="aktif"
                                {{ old('status', $user->status ?? 'aktif') == 'aktif' ? 'checked' : '' }}
                                class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Aktif</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="status" value="nonaktif"
                                {{ old('status', $user->status ?? '') == 'nonaktif' ? 'checked' : '' }}
                                class="text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-700">Non-Aktif / Alumni</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Bagian Santri (Show/Hide based on Role) -->
            <div id="santri-fields"
                class="mt-8 transition-all duration-300 {{ old('role', $user->role ?? '') == 'santri' ? 'block' : 'hidden' }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Data Santri</h3>
                    </div>

                    <!-- NIS -->
                    <div>
                        <label for="nis" class="block text-sm font-medium text-gray-700 mb-1">Nomor Induk Santri
                            (NIS)</label>
                        <input type="text" name="nis" id="nis" value="{{ old('nis', $user->nis ?? '') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="Nomor Induk Santri">
                        @error('nis')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Masuk -->
                    <div>
                        <label for="tahun_ajaran_masuk_id" class="block text-sm font-medium text-gray-700 mb-1">Tahun
                            Masuk</label>
                        <select name="tahun_ajaran_masuk_id" id="tahun_ajaran_masuk_id"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                            <option value="">Pilih Tahun Ajaran</option>
                            @foreach ($tahunAjaran as $ta)
                                <option value="{{ $ta->id }}"
                                    {{ old('tahun_ajaran_masuk_id', $user->tahun_ajaran_masuk_id ?? '') == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->tahun_ajaran }} ({{ $ta->status == 'aktif' ? 'Aktif' : 'Tidak Aktif' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" id="tempat_lahir"
                            value="{{ old('tempat_lahir', $user->tempat_lahir ?? '') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="Kota Kelahiran">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                            Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <div class="flex items-center space-x-4 mt-2">
                            <label class="flex items-center">
                                <input type="radio" name="jenis_kelamin" value="L"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin ?? '') == 'L' ? 'checked' : '' }}
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Laki-laki</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="jenis_kelamin" value="P"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin ?? '') == 'P' ? 'checked' : '' }}
                                    class="text-pink-600 focus:ring-pink-500">
                                <span class="ml-2 text-sm text-gray-700">Perempuan</span>
                            </label>
                        </div>
                    </div>

                    <!-- No HP -->
                    <div>
                        <label for="no_telp" class="block text-sm font-medium text-gray-700 mb-1">No. Handphone /
                            WA</label>
                        <input type="text" name="no_telp" id="no_telp"
                            value="{{ old('no_telp', $user->no_telp ?? '') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <!-- Nama Orang Tua -->
                    <div>
                        <label for="nama_orang_tua" class="block text-sm font-medium text-gray-700 mb-1">Nama Wali / Orang
                            Tua</label>
                        <input type="text" name="nama_orang_tua" id="nama_orang_tua"
                            value="{{ old('nama_orang_tua', $user->nama_orang_tua ?? '') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="Nama Wali">
                    </div>

                    <!-- Tingkatan Sekolah -->
                    <div>
                        <label for="tingkatan" class="block text-sm font-medium text-gray-700 mb-1">Tingkatan
                            Sekolah</label>
                        <select name="tingkatan" id="tingkatan"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                            <option value="">Pilih Tingkatan</option>
                            <option value="SD"
                                {{ old('tingkatan', $user->tingkatan ?? '') == 'SD' ? 'selected' : '' }}>SD / MI</option>
                            <option value="SMP"
                                {{ old('tingkatan', $user->tingkatan ?? '') == 'SMP' ? 'selected' : '' }}>SMP / MTs
                            </option>
                            <option value="SMA"
                                {{ old('tingkatan', $user->tingkatan ?? '') == 'SMA' ? 'selected' : '' }}>SMA / SMK / MA
                            </option>
                            <option value="KULIAH"
                                {{ old('tingkatan', $user->tingkatan ?? '') == 'KULIAH' ? 'selected' : '' }}>Perguruan
                                Tinggi</option>
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <input type="text" name="kelas" id="kelas"
                            value="{{ old('kelas', $user->kelas ?? '') }}"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="Contoh: 1, 2, 3, 10, 11...">
                    </div>

                    <!-- Tingkatan Ngaji -->
                    <div>
                        <label for="tingkatan_ngaji" class="block text-sm font-medium text-gray-700 mb-1">Tingkatan
                            Mengaji</label>
                        <select name="tingkatan_ngaji" id="tingkatan_ngaji"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                            <option value="">Pilih Tingkatan Ngaji</option>
                            <option value="I Tsanawiyyah"
                                {{ old('tingkatan_ngaji', $user->tingkatan_ngaji ?? '') == 'I Tsanawiyyah' ? 'selected' : '' }}>
                                I Tsanawiyyah</option>
                            <option value="II Tsanawiyyah"
                                {{ old('tingkatan_ngaji', $user->tingkatan_ngaji ?? '') == 'II Tsanawiyyah' ? 'selected' : '' }}>
                                II Tsanawiyyah</option>
                            <option value="III Tsanawiyyah"
                                {{ old('tingkatan_ngaji', $user->tingkatan_ngaji ?? '') == 'III Tsanawiyyah' ? 'selected' : '' }}>
                                III Tsanawiyyah</option>
                            <option value="VI Ibtida'iyah"
                                {{ old('tingkatan_ngaji', $user->tingkatan_ngaji ?? '') == "VI Ibtida'iyah" ? 'selected' : '' }}>
                                VI Ibtida'iyah</option>
                            <option value="V Ibtida'iyah"
                                {{ old('tingkatan_ngaji', $user->tingkatan_ngaji ?? '') == "V Ibtida'iyah" ? 'selected' : '' }}>
                                V Ibtida'iyah</option>
                            <option value="IV Ibtida'iyah"
                                {{ old('tingkatan_ngaji', $user->tingkatan_ngaji ?? '') == "IV Ibtida'iyah" ? 'selected' : '' }}>
                                IV Ibtida'iyah</option>
                            <option value="III I'dad"
                                {{ old('tingkatan_ngaji', $user->tingkatan_ngaji ?? '') == "III I'dad" ? 'selected' : '' }}>
                                III I'dad</option>
                            <option value="PTQ"
                                {{ old('tingkatan_ngaji', $user->tingkatan_ngaji ?? '') == 'PTQ' ? 'selected' : '' }}>PTQ
                            </option>
                        </select>
                    </div>

                    <!-- Alamat -->
                    <div class="col-span-1 md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="3"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="Alamat lengkap domisili santri">{{ old('alamat', $user->alamat ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <button type="button" onclick="history.back()"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-6 rounded-lg mr-4 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow-md transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ $user ? 'Simpan Perubahan' : 'Simpan Pengguna Baru' }}
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const santriFields = document.getElementById('santri-fields');

            function toggleSantriFields() {
                if (roleSelect.value === 'santri') {
                    santriFields.classList.remove('hidden');
                    santriFields.classList.add('block');
                } else {
                    santriFields.classList.add('hidden');
                    santriFields.classList.remove('block');
                }
            }

            roleSelect.addEventListener('change', toggleSantriFields);

            // Initial check
            toggleSantriFields();
        });
    </script>
@endsection
