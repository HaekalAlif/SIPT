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

                    <!-- Tahun Masuk -->
                    <div class="md:col-span-2">
                        <label for="tahun_ajaran_masuk_id" class="block text-sm font-medium text-gray-700 mb-1">Tahun
                            Masuk</label>
                        <select name="tahun_ajaran_masuk_id" id="tahun_ajaran_masuk_id" required
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
                            value="{{ old('tempat_lahir', $user->tempat_lahir ?? '') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="Kota Kelahiran">
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-gray-700 mb-1">Tanggal
                            Lahir</label>
                        <input type="date" name="tanggal_lahir" id="tanggal_lahir"
                            value="{{ old('tanggal_lahir', optional($user?->tanggal_lahir)->format('Y-m-d')) }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                        <div class="flex items-center space-x-4 mt-2">
                            <label class="flex items-center">
                                <input type="radio" name="jenis_kelamin" value="L"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin ?? '') == 'L' ? 'checked' : '' }} required
                                    class="text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Laki-laki</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="jenis_kelamin" value="P"
                                    {{ old('jenis_kelamin', $user->jenis_kelamin ?? '') == 'P' ? 'checked' : '' }} required
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
                            value="{{ old('no_telp', $user->no_telp ?? '') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="08xxxxxxxxxx">
                    </div>

                    <!-- Nama Orang Tua -->
                    <div>
                        <label for="nama_orang_tua" class="block text-sm font-medium text-gray-700 mb-1">Nama Wali / Orang
                            Tua</label>
                        <input type="text" name="nama_orang_tua" id="nama_orang_tua"
                            value="{{ old('nama_orang_tua', $user->nama_orang_tua ?? '') }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                            placeholder="Nama Wali">
                    </div>

                    <!-- Tingkatan Sekolah -->
                    <div>
                        <label for="tingkatan" class="block text-sm font-medium text-gray-700 mb-1">Tingkatan
                            Sekolah</label>
                        <select name="tingkatan" id="tingkatan" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                            <option value="">Pilih Tingkatan</option>
                            <option value="MI"
                                {{ old('tingkatan', $user->tingkatan ?? '') == 'MI' ? 'selected' : '' }}>MI</option>
                            <option value="SMP/MTs"
                                {{ old('tingkatan', $user->tingkatan ?? '') == 'SMP/MTs' ? 'selected' : '' }}>SMP / MTs
                            </option>
                            <option value="SMK/MA"
                                {{ old('tingkatan', $user->tingkatan ?? '') == 'SMK/MA' ? 'selected' : '' }}>SMK / MA
                            </option>
                            <option value="Perguruan Tinggi"
                                {{ old('tingkatan', $user->tingkatan ?? '') == 'Perguruan Tinggi' ? 'selected' : '' }}>
                                Perguruan
                                Tinggi</option>
                        </select>
                    </div>

                    <!-- Kelas -->
                    <div>
                        <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                        <select name="kelas" id="kelas" required
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                            <option value="">Pilih Tingkatan terlebih dahulu</option>
                        </select>
                    </div>

                    <!-- Tingkatan Ngaji -->
                    <div>
                        <label for="tingkatan_ngaji" class="block text-sm font-medium text-gray-700 mb-1">Tingkatan
                            Mengaji</label>
                        <select name="tingkatan_ngaji" id="tingkatan_ngaji" required
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
                        <textarea name="alamat" id="alamat" rows="3" required
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
            const tingkatanSelect = document.getElementById('tingkatan');
            const kelasSelect = document.getElementById('kelas');
            const selectedKelas = @json(old('kelas', $user->kelas ?? ''));
            const santriRequiredSelectors = [
                '#tahun_ajaran_masuk_id',
                '#tempat_lahir',
                '#tanggal_lahir',
                'input[name="jenis_kelamin"]',
                '#no_telp',
                '#nama_orang_tua',
                '#tingkatan',
                '#kelas',
                '#tingkatan_ngaji',
                '#alamat',
            ];

            function toggleSantriRequired(isSantri) {
                santriRequiredSelectors.forEach(function(selector) {
                    document.querySelectorAll(selector).forEach(function(el) {
                        el.required = isSantri;
                    });
                });
            }

            const kelasOptionsByTingkatan = {
                MI: [
                    '1 MI', '2 MI', '3 MI',
                    '4 MI', '5 MI', '6 MI'
                ],
                'SMP/MTs': ['1 SMP/MTs', '2 SMP/MTs', '3 SMP/MTs'],
                'SMK/MA': ['1 SMK/MA', '2 SMK/MA', '3 SMK/MA'],
                'Perguruan Tinggi': [
                    'Semester 1 dan 2', 'Semester 3 dan 4', 'Semester 5 dan 6', 'Semester 7 dan 8'
                ],
            };

            function renderKelasOptions() {
                const tingkatan = tingkatanSelect.value;
                const options = kelasOptionsByTingkatan[tingkatan] || [];
                const currentValue = kelasSelect.value || selectedKelas;

                kelasSelect.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = options.length ? 'Pilih Kelas/Semester' :
                    'Pilih Tingkatan terlebih dahulu';
                kelasSelect.appendChild(placeholder);

                options.forEach(function(item) {
                    const option = document.createElement('option');
                    option.value = item;
                    option.textContent = item;
                    if (item === currentValue) {
                        option.selected = true;
                    }
                    kelasSelect.appendChild(option);
                });
            }

            function toggleSantriFields() {
                const isSantri = roleSelect.value === 'santri';
                toggleSantriRequired(isSantri);

                if (isSantri) {
                    santriFields.classList.remove('hidden');
                    santriFields.classList.add('block');
                } else {
                    santriFields.classList.add('hidden');
                    santriFields.classList.remove('block');
                }
            }

            roleSelect.addEventListener('change', toggleSantriFields);
            tingkatanSelect.addEventListener('change', renderKelasOptions);

            // Initial check
            toggleSantriFields();
            renderKelasOptions();
        });
    </script>
@endsection
