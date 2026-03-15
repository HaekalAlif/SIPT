@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Master Tagihan</h1>
        <p class="text-gray-500 mt-1 text-sm">Atur jenis tagihan yang aktif per santri per tahun ajaran. Yang tidak dicentang
            tidak akan muncul di halaman pembayaran santri tersebut.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter: Tahun Ajaran + Tingkatan + Kelas + Santri --}}
    <form method="GET" action="{{ route('admin.master-tagihan') }}"
        class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Tingkatan</label>
                <select name="tingkatan" id="tingkatan"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm transition">
                    <option value="">-- Semua Tingkatan --</option>
                    @foreach ($tingkatanOptions as $tingkatan)
                        <option value="{{ $tingkatan }}"
                            {{ ($selectedTingkatan ?? '') === $tingkatan ? 'selected' : '' }}>
                            {{ $tingkatan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Kelas</label>
                <select name="kelas" id="kelas"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm transition">
                    <option value="">-- Semua Kelas --</option>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Santri</label>
                <select name="santri_id" id="santri_id"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm transition">
                    <option value="">-- Pilih Santri --</option>
                    @foreach ($santris as $santri)
                        <option value="{{ $santri->id }}" {{ $selectedUserId == $santri->id ? 'selected' : '' }}>
                            {{ $santri->nama_santri }} -
                            {{ $santri->tingkatan_ngaji ? ' (' . $santri->tingkatan_ngaji . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" id="tahun_ajaran_id"
                    class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 text-sm transition">
                    <option value="">-- Pilih Tahun Ajaran --</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ $selectedTaId == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg transition text-sm">
                    Tampilkan
                </button>
            </div>
        </div>
    </form>

    @if ($selectedUserId && $selectedTaId)
        @php
            $selectedSantri = $santris->firstWhere('id', $selectedUserId);
            $selectedTa = $tahunAjarans->firstWhere('id', $selectedTaId);
        @endphp

        <form action="{{ route('admin.master-tagihan.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" value="{{ $selectedUserId }}">
            <input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTaId }}">
            <input type="hidden" name="tingkatan" value="{{ $selectedTingkatan }}">
            <input type="hidden" name="kelas" value="{{ $selectedKelas }}">

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-5 pb-3 border-b gap-3">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">
                            Tagihan untuk:
                            <span class="text-blue-600">{{ $selectedSantri?->nama_santri }}</span>
                        </h3>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Tahun Ajaran: <strong>{{ $selectedTa?->nama }}</strong>
                            &nbsp;·&nbsp; Tingkatan: {{ $selectedSantri?->tingkatan_ngaji ?? '-' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex gap-3 text-xs">
                            <button type="button" onclick="checkAll(true)" class="text-blue-600 hover:underline">Aktifkan
                                Semua</button>
                            <button type="button" onclick="checkAll(false)"
                                class="text-red-500 hover:underline">Nonaktifkan Semua</button>
                        </div>
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-5 rounded-lg transition text-sm">
                            Simpan
                        </button>
                    </div>
                </div>

                @foreach ($grouped as $kategori => $items)
                    <div class="mb-6">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                            <span class="h-px flex-1 bg-gray-100"></span>
                            {{ $kategori }}
                            <span class="h-px flex-1 bg-gray-100"></span>
                        </h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
                            @foreach ($items as $jenis)
                                @php $isActive = !in_array($jenis->id, $disabledIds); @endphp
                                <label
                                    class="jenis-item flex items-start p-3 rounded-lg border cursor-pointer transition
                                    {{ $isActive ? 'border-blue-200 bg-blue-50' : 'border-gray-200 bg-gray-50 opacity-60' }}">
                                    <input type="checkbox" name="active_ids[]" value="{{ $jenis->id }}"
                                        {{ $isActive ? 'checked' : '' }} onchange="updateLabel(this)"
                                        class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <div class="ml-3">
                                        <span
                                            class="block text-sm font-medium text-gray-800">{{ $jenis->nama_tagihan }}</span>
                                        <span class="text-xs text-gray-500">
                                            Rp {{ number_format($jenis->nominal, 0, ',', '.') }}
                                            {{ $jenis->is_bulanan ? '× 12 bln' : '' }}
                                        </span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </form>
    @elseif($selectedTaId && !$selectedUserId)
        <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400">
            Pilih santri terlebih dahulu.
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-100 p-10 text-center text-gray-400">
            Pilih santri dan tahun ajaran untuk mengatur konfigurasi tagihan.
        </div>
    @endif

    <script>
        const kelasOptionsByTingkatan = @json($kelasOptionsByTingkatan ?? []);
        const santriOptions = @json($santriOptions ?? []);
        const tingkatanSelect = document.getElementById('tingkatan');
        const kelasSelect = document.getElementById('kelas');
        const tahunAjaranSelect = document.getElementById('tahun_ajaran_id');
        const santriSelect = document.getElementById('santri_id');
        const selectedKelas = @json($selectedKelas ?? '');
        const selectedSantriId = @json($selectedUserId ?? '');

        function renderKelasOptions() {
            if (!tingkatanSelect || !kelasSelect) return;

            const tingkatan = tingkatanSelect.value;
            const options = kelasOptionsByTingkatan[tingkatan] || [];

            kelasSelect.innerHTML = '<option value="">-- Semua Kelas --</option>';
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
            renderSantriOptions();
        });

        kelasSelect?.addEventListener('change', () => {
            renderSantriOptions();
        });

        tahunAjaranSelect?.addEventListener('change', () => {
            renderSantriOptions();
        });

        function renderSantriOptions() {
            if (!santriSelect || !tingkatanSelect || !kelasSelect || !tahunAjaranSelect) return;

            const selectedTingkatan = tingkatanSelect.value;
            const selectedKelas = kelasSelect.value;
            const selectedTaId = tahunAjaranSelect.value ? parseInt(tahunAjaranSelect.value, 10) : null;

            santriSelect.innerHTML = '<option value="">-- Pilih Santri --</option>';

            const filtered = santriOptions.filter((s) => {
                if (selectedTaId && s.tahun_ajaran_masuk_id && s.tahun_ajaran_masuk_id > selectedTaId) {
                    return false;
                }
                if (selectedTingkatan && s.tingkatan !== selectedTingkatan) {
                    return false;
                }
                if (selectedKelas && s.kelas !== selectedKelas) {
                    return false;
                }
                return true;
            });

            filtered.forEach((s) => {
                const option = document.createElement('option');
                option.value = String(s.id);
                option.textContent = `${s.nama_santri}${s.tingkatan_ngaji ? ' (' + s.tingkatan_ngaji + ')' : ''}`;
                if (String(selectedSantriId) === String(s.id)) {
                    option.selected = true;
                }
                santriSelect.appendChild(option);
            });
        }

        renderKelasOptions();
        renderSantriOptions();

        function checkAll(val) {
            document.querySelectorAll('.jenis-item input[type=checkbox]').forEach(cb => {
                cb.checked = val;
                updateLabel(cb);
            });
        }

        function updateLabel(cb) {
            const label = cb.closest('label');
            if (cb.checked) {
                label.classList.remove('border-gray-200', 'bg-gray-50', 'opacity-60');
                label.classList.add('border-blue-200', 'bg-blue-50');
            } else {
                label.classList.remove('border-blue-200', 'bg-blue-50');
                label.classList.add('border-gray-200', 'bg-gray-50', 'opacity-60');
            }
        }
    </script>
@endsection
