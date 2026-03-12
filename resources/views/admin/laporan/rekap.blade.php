@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Rekapitulasi Pembayaran</h1>
        <button onclick="window.print()"
            class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Laporan
        </button>
    </div>

    <!-- Filter -->
    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 no-print">
        <form action="{{ route('admin.laporan-rekap') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="tahun_ajaran" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran" id="tahun_ajaran"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @foreach ($tahunAjaran as $ta)
                        <option value="{{ $ta->id }}"
                            {{ $activeTahunAjaran && $activeTahunAjaran->id == $ta->id ? 'selected' : '' }}>
                            {{ $ta->tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition-colors">
                Tampilkan Rekap
            </button>
        </form>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">Total Tagihan</p>
            <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">Total Pemasukan</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">Total Tunggakan</p>
            <p class="text-2xl font-bold text-red-600">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <p class="text-sm text-gray-500 mb-1">Menunggu Verifikasi</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $pembayaranPending }} Pembayaran</p>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Progres Pembayaran</h3>
        @php
            $persen = $totalTagihan > 0 ? round(($totalPemasukan / $totalTagihan) * 100) : 0;
        @endphp
        <div class="flex justify-between text-sm text-gray-600 mb-2">
            <span>Terbayar: {{ $persen }}%</span>
            <span>Rp {{ number_format($totalPemasukan, 0, ',', '.') }} / Rp
                {{ number_format($totalTagihan, 0, ',', '.') }}</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-4">
            <div class="bg-green-500 h-4 rounded-full transition-all" style="width: {{ min($persen, 100) }}%"></div>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
            <div class="text-gray-500">Jumlah Santri: <span class="font-semibold text-gray-800">{{ $santriCount }}</span>
            </div>
            <div class="text-gray-500">Tahun Ajaran: <span
                    class="font-semibold text-gray-800">{{ $activeTahunAjaran?->nama ?? '-' }}</span></div>
        </div>
    </div>
@endsection
