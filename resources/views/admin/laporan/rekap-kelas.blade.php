@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center no-print">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Rekap Pembayaran Per Kelas</h1>
            <p class="text-sm text-gray-500 mt-1">Preview rekap per kelas dengan format mengikuti kartu pembayaran
                santri.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.laporan-rekap-kelas.pdf', request()->only(['tahun_ajaran_id', 'tingkatan', 'kelas'])) }}"
                @class([
                    'font-medium py-2 px-4 rounded-lg flex items-center transition-colors',
                    'bg-red-600 hover:bg-red-700 text-white' => $hasRequiredFilters,
                    'bg-gray-300 text-gray-500 pointer-events-none' => !$hasRequiredFilters,
                ])>
                <i class="fa fa-file-pdf mr-2"></i>
                Download PDF
            </a>
        </div>
    </div>

    <div class="mb-5 bg-white p-4 rounded-xl shadow-sm border border-gray-100 no-print">
        <form action="{{ route('admin.laporan-rekap-kelas') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs text-gray-500 mb-1">Tahun Pelajaran</label>
                <select name="tahun_ajaran_id" class="w-full rounded border-gray-300 text-sm">
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}"
                            {{ $selectedTahunAjaran && $selectedTahunAjaran->id === $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1">Tingkatan</label>
                <select name="tingkatan" id="tingkatan-filter" class="w-full rounded border-gray-300 text-sm">
                    <option value="">Pilih Tingkatan</option>
                    @foreach ($tingkatanOptions as $tingkatan)
                        <option value="{{ $tingkatan }}" {{ $selectedTingkatan === $tingkatan ? 'selected' : '' }}>
                            {{ $tingkatan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-500 mb-1">Kelas</label>
                <select name="kelas" id="kelas-filter" class="w-full rounded border-gray-300 text-sm">
                    <option value="">Pilih Tingkatan dulu</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded px-4 py-2 text-sm">
                    Tampilkan Preview
                </button>
            </div>
        </form>
    </div>

    <div class="mb-3 text-sm text-gray-600 no-print">
        <span class="font-semibold">Kelas:</span> {{ $selectedKelas ?: '-' }}
        <span class="mx-2">|</span>
        <span class="font-semibold">Tingkatan:</span> {{ $selectedTingkatan ?: '-' }}
        <span class="mx-2">|</span>
        <span class="font-semibold">Tahun Pelajaran:</span> {{ $selectedTahunAjaran->nama ?? '-' }}
    </div>

    @if (!$hasRequiredFilters)
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-amber-800 no-print">
            Pilih <span class="font-semibold">Tingkatan</span> lalu <span class="font-semibold">Kelas</span> terlebih
            dahulu, lalu klik <span class="font-semibold">Tampilkan Preview</span>.
        </div>
    @else
        @include('admin.laporan.partials.rekap-kelas-preview')
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const kelasByTingkatan = @json($kelasOptionsByTingkatan);
            const tingkatanEl = document.getElementById('tingkatan-filter');
            const kelasEl = document.getElementById('kelas-filter');
            const selectedKelas = @json($selectedKelas);

            function renderKelasOptions() {
                const tingkatan = tingkatanEl.value;
                const kelasList = kelasByTingkatan[tingkatan] || [];
                const currentValue = kelasEl.value || selectedKelas || '';

                kelasEl.innerHTML = '';

                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.textContent = kelasList.length ? 'Pilih Kelas' : 'Pilih Tingkatan dulu';
                kelasEl.appendChild(placeholder);

                kelasList.forEach(function(kelas) {
                    const option = document.createElement('option');
                    option.value = kelas;
                    option.textContent = kelas;
                    if (kelas === currentValue) {
                        option.selected = true;
                    }
                    kelasEl.appendChild(option);
                });
            }

            tingkatanEl.addEventListener('change', function() {
                kelasEl.value = '';
                renderKelasOptions();
            });

            renderKelasOptions();
        });
    </script>
@endsection
