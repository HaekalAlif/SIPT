{{-- filepath: resources/views/santri/profil.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="max-w-5xl mx-auto space-y-4">

        {{-- Header --}}
        <div class="bg-green-700 text-white p-4 rounded-lg">
            <h1 class="text-xl font-bold">Profil Saya</h1>
            <p class="text-green-100 mt-1 text-sm">Perbarui data dan foto profil Anda</p>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-300 text-green-800 px-4 py-3 rounded-lg text-sm">
                <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('santri.profil.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            {{-- ─── PHOTO + NAME BAR ─── --}}
            <div class="bg-white rounded-xl shadow px-5 py-4 flex items-center gap-4">
                <div class="relative flex-shrink-0">
                    @if ($user->foto_profile)
                        <img id="photo-preview" src="{{ Storage::url($user->foto_profile) }}" alt="Foto Profil"
                            class="w-14 h-14 rounded-full object-cover border-2 border-green-500">
                    @else
                        <img id="photo-preview"
                            src="https://ui-avatars.com/api/?name={{ urlencode($user->nama_santri) }}&background=16a34a&color=fff&size=80"
                            alt="Foto Profil" class="w-14 h-14 rounded-full object-cover border-2 border-green-500">
                    @endif
                    <input type="file" id="foto_profile" name="foto_profile" accept="image/*" class="hidden"
                        onchange="previewPhoto(this)">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-gray-800 truncate">{{ $user->nama_santri }}</p>
                    <p class="text-xs text-gray-400">NIS: {{ $user->nis ?? '-' }}</p>
                </div>
                <label for="foto_profile"
                    class="flex-shrink-0 text-xs text-green-600 font-semibold cursor-pointer hover:text-green-800 border border-green-300 rounded-lg px-3 py-1.5">
                    <i class="fa fa-camera mr-1"></i>Ganti Foto
                </label>
            </div>

            {{-- ─── 2-COLUMN GRID ─── --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- LEFT: Data Diri (editable) --}}
                <div class="bg-white rounded-xl shadow p-5 space-y-3">
                    <p class="font-bold text-gray-700 text-xs uppercase tracking-wide border-b pb-2">Data Diri</p>

                    {{-- Nama Santri --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Nama Santri <span
                                class="text-red-400">*</span></label>
                        <input type="text" name="nama_santri" value="{{ old('nama_santri', $user->nama_santri) }}"
                            placeholder="Nama Santri"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 @error('nama_santri') border-red-400 @enderror">
                    </div>

                    {{-- Nama Orang Tua --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Nama Orang Tua</label>
                        <input type="text" name="nama_orang_tua"
                            value="{{ old('nama_orang_tua', $user->nama_orang_tua) }}" placeholder="Nama Orang Tua"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    {{-- No. Telepon --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">No. Telepon</label>
                        <input type="text" name="no_telp" value="{{ old('no_telp', $user->no_telp) }}"
                            placeholder="No. Telepon"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div class="relative">
                        <label class="block text-xs text-gray-500 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm appearance-none bg-white focus:outline-none focus:ring-2 focus:ring-green-400">
                            <option value="">-- Pilih --</option>
                            <option value="L"
                                {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="P"
                                {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    {{-- Tempat Lahir --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $user->tempat_lahir) }}"
                            placeholder="Tempat Lahir"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Alamat</label>
                        <input type="text" name="alamat" value="{{ old('alamat', $user->alamat) }}"
                            placeholder="Alamat"
                            class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>
                </div>

                {{-- RIGHT: Info Akademik (admin-only) + Info Akun (read-only) --}}
                <div class="space-y-4">

                    {{-- Info Akademik --}}
                    <div class="bg-white rounded-xl shadow p-5 space-y-3">
                        <p class="font-bold text-gray-700 text-xs uppercase tracking-wide border-b pb-2">
                            Info Akademik
                        </p>

                        {{-- Tingkatan --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tingkatan</label>
                            <div
                                class="w-full border border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500 flex items-center gap-2">
                                <i class="fa fa-lock text-gray-300 text-xs"></i>
                                {{ $user->tingkatan ?? '-' }}
                            </div>
                        </div>

                        {{-- Kelas --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Kelas</label>
                            <div
                                class="w-full border border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500 flex items-center gap-2">
                                <i class="fa fa-lock text-gray-300 text-xs"></i>
                                {{ $user->kelas ? 'Kelas ' . $user->kelas : '-' }}
                            </div>
                        </div>

                        {{-- Tingkatan Ngaji --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Tingkatan Ngaji</label>
                            <div
                                class="w-full border border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500 flex items-center gap-2">
                                <i class="fa fa-lock text-gray-300 text-xs"></i>
                                {{ $user->tingkatan_ngaji ?? '-' }}
                            </div>
                        </div>
                    </div>

                    {{-- Info Akun --}}
                    <div class="bg-white rounded-xl shadow p-5 space-y-3">
                        <p class="font-bold text-gray-700 text-xs uppercase tracking-wide border-b pb-2">Info Akun</p>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">NIS</label>
                            <input type="text" value="{{ $user->nis ?? '-' }}" disabled
                                class="w-full border border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Email</label>
                            <input type="text" value="{{ $user->email }}" disabled
                                class="w-full border border-gray-100 bg-gray-50 rounded-lg px-3 py-2 text-sm text-gray-500 cursor-not-allowed">
                        </div>
                    </div>

                </div>
            </div>

            {{-- ─── SUBMIT ─── --}}
            <div class="pb-4">
                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition text-sm">
                    <i class="fa fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>

        </form>
    </div>

    @push('scripts')
        <script>
            function previewPhoto(input) {
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('photo-preview').src = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
        </script>
    @endpush
@endsection
