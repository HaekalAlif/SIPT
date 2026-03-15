@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.kartu-pembayaran.index') }}"
            class="text-gray-500 hover:text-gray-700 font-medium flex items-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Data Kartu
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Buat Kartu & Tagihan Santri</h1>
        <p class="text-gray-600 mt-1">Pilih santri, tahun ajaran, lalu atur tagihan yang berlaku.</p>
    </div>

    @if (session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">{{ session('error') }}</div>
    @endif

    <form action="{{ route('admin.kartu-pembayaran.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom Kiri: Info Kartu --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-base font-semibold text-gray-800 mb-4 pb-2 border-b">Informasi Kartu</h3>
                    <div class="space-y-4">
                        <!-- Santri Select -->
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Santri <span
                                    class="text-red-500">*</span></label>
                            <select name="user_id" id="user_id" required
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                                <option value="">-- Pilih Santri --</option>
                                @foreach ($santris as $santri)
                                    <option value="{{ $santri->id }}"
                                        data-tingkatan-ngaji="{{ $santri->tingkatan_ngaji ?? '' }}"
                                        {{ old('user_id') == $santri->id ? 'selected' : '' }}>
                                        {{ $santri->tingkatan_ngaji ? ' — ' . $santri->tingkatan_ngaji : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tahun Ajaran Select -->
                        <div>
                            <label for="tahun_ajaran_id" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran
                                <span class="text-red-500">*</span></label>
                            <select name="tahun_ajaran_id" id="tahun_ajaran_id" required
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                                <option value="">-- Pilih Tahun Ajaran --</option>
                                @foreach ($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}"
                                        {{ old('tahun_ajaran_id') == $ta->id || $ta->is_active ? 'selected' : '' }}>
                                        {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Nomor Kartu -->
                        <div>
                            <label for="nomor_kartu" class="block text-sm font-medium text-gray-700 mb-1">Nomor Kartu
                                (Opsional)</label>
                            <input type="text" name="nomor_kartu" id="nomor_kartu" value="{{ old('nomor_kartu') }}"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200"
                                placeholder="Generate otomatis jika kosong">
                        </div>
                    </div>
                </div>

                <!-- Info Santri Terpilih -->
                <div id="santri-info" class="bg-blue-50 border border-blue-100 rounded-xl p-5 hidden">
                    <h4 class="text-sm font-semibold text-blue-800 mb-3">Info Santri Terpilih</h4>
                    <p class="text-sm text-blue-700"><span class="font-medium">Tingkatan Ngaji:</span>
                        <span id="info-tingkatan">-</span>
                    </p>
                    <p class="text-sm text-blue-500 mt-2 text-xs" id="info-khataman"></p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg shadow-md transition-colors flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Buat Kartu & Tagihan
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">Tagihan yang dicentang akan dibuat otomatis</p>
                </div>
            </div>

            {{-- Kolom Kanan: Pilih Tagihan --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex justify-between items-center mb-4 pb-2 border-b">
                        <h3 class="text-base font-semibold text-gray-800">Pilih Tagihan</h3>
                        <div class="flex gap-2 text-xs">
                            <button type="button" onclick="checkAll(true)" class="text-blue-600 hover:underline">Centang
                                Semua</button>
                            <span class="text-gray-300">|</span>
                            <button type="button" onclick="checkAll(false)" class="text-gray-500 hover:underline">Hapus
                                Semua</button>
                        </div>
                    </div>

                    @php $grouped = $jenisTagihans->groupBy(fn($j) => $j->kategori->nama ?? 'Lainnya'); @endphp

                    @foreach ($grouped as $kategori => $items)
                        <div class="mb-5">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">{{ $kategori }}
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($items as $jenis)
                                    @php
                                        $isKhataman =
                                            str_contains(strtolower($jenis->nama_tagihan), 'khataman') ||
                                            str_contains(strtolower($jenis->nama_tagihan), 'haflah');
                                    @endphp
                                    <div data-khataman="{{ $isKhataman ? '1' : '0' }}"
                                        class="khataman-item flex items-start p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                        <input id="jenis_{{ $jenis->id }}" name="jenis_tagihan_ids[]" type="checkbox"
                                            value="{{ $jenis->id }}" checked
                                            class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="jenis_{{ $jenis->id }}" class="ml-3 cursor-pointer">
                                            <span
                                                class="block text-sm font-medium text-gray-800">{{ $jenis->nama_tagihan }}</span>
                                            <span class="text-xs text-gray-500">
                                                Rp {{ number_format($jenis->nominal, 0, ',', '.') }}
                                                {{ $jenis->is_bulanan ? '/ bulan × 12' : '' }}
                                            </span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </form>

    <script>
        const TIDAK_KHATAMAN = ["II Tsanawiyyah", "V Ibtida'iyah", "IV Ibtida'iyah", "III I'dad"];
        const DISABLED_MAP = @json($disabledMap); // { user_id: { ta_id: [jenis_ids] } }
        const userSelect = document.getElementById('user_id');
        const taSelect = document.getElementById('tahun_ajaran_id');

        function checkAll(val) {
            document.querySelectorAll('.khataman-item input[type=checkbox]:not(:disabled)').forEach(cb => cb.checked = val);
        }

        function getDisabledIds() {
            const userId = userSelect.value;
            const taId = taSelect.value;
            if (!userId || !taId) return [];
            return ((DISABLED_MAP[userId] || {})[taId] || []).map(Number);
        }

        function applyMasterTagihan() {
            const disabledIds = getDisabledIds();

            document.querySelectorAll('.khataman-item').forEach(el => {
                const cb = el.querySelector('input[type=checkbox]');
                if (!cb) return;
                const id = Number(cb.value);
                if (disabledIds.includes(id)) {
                    el.classList.add('opacity-40');
                    el.title = 'Dinonaktifkan oleh admin untuk santri ini';
                    cb.checked = false;
                    cb.disabled = true;
                } else {
                    // Only re-enable if not also blocked by khataman rule
                    const isKhataman = el.dataset.khataman === '1';
                    const opt = userSelect.options[userSelect.selectedIndex];
                    const tingkatan = opt ? (opt.dataset.tingkatanNgaji || '') : '';
                    if (!(isKhataman && TIDAK_KHATAMAN.includes(tingkatan))) {
                        el.classList.remove('opacity-40');
                        el.title = '';
                        cb.disabled = false;
                        cb.checked = true;
                    }
                }
            });
        }

        function updateKhatamanVisibility() {
            const opt = userSelect.options[userSelect.selectedIndex];
            const tingkatan = opt ? (opt.dataset.tingkatanNgaji || '') : '';
            const tidakBayar = TIDAK_KHATAMAN.includes(tingkatan);
            const info = document.getElementById('santri-info');
            const infoTingkatan = document.getElementById('info-tingkatan');
            const infoKhataman = document.getElementById('info-khataman');

            if (tingkatan) {
                info.classList.remove('hidden');
                infoTingkatan.textContent = tingkatan;
                infoKhataman.textContent = tidakBayar ?
                    '⛔ Tingkatan ini tidak perlu membayar khataman — dinonaktifkan otomatis.' :
                    '✅ Tingkatan ini perlu membayar khataman.';
            } else {
                info.classList.add('hidden');
            }

            const disabledIds = getDisabledIds();

            document.querySelectorAll('[data-khataman="1"]').forEach(el => {
                const cb = el.querySelector('input[type=checkbox]');
                const id = Number(cb?.value);
                if (disabledIds.includes(id)) return; // already handled by applyMasterTagihan

                if (tidakBayar) {
                    el.classList.add('opacity-40');
                    el.title = 'Tidak perlu khataman untuk ' + tingkatan;
                    if (cb) {
                        cb.checked = false;
                        cb.disabled = true;
                    }
                } else {
                    el.classList.remove('opacity-40');
                    el.title = '';
                    if (cb) {
                        cb.checked = true;
                        cb.disabled = false;
                    }
                }
            });
        }

        userSelect.addEventListener('change', () => {
            applyMasterTagihan();
            updateKhatamanVisibility();
        });
        taSelect.addEventListener('change', () => {
            applyMasterTagihan();
            updateKhatamanVisibility();
        });
        applyMasterTagihan();
        updateKhatamanVisibility();
    </script>
@endsection
