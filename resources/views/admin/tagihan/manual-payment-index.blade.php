@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bayar Manual</h1>
            <p class="text-sm text-gray-500 mt-1">Tampilkan semua santri, cek sisa tagihan, lalu pilih santri untuk
                checklist pembayaran manual.</p>
        </div>
    </div>

    <div class="mb-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.manual-payment.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="text" name="santri" value="{{ request('santri') }}" placeholder="Cari nama santri"
                class="rounded border-gray-300 text-sm">
            <select name="tingkatan" class="rounded border-gray-300 text-sm">
                <option value="">Semua Tingkatan</option>
                @foreach ($tingkatanOptions as $tingkatan)
                    <option value="{{ $tingkatan }}" {{ request('tingkatan') === $tingkatan ? 'selected' : '' }}>
                        {{ ucfirst($tingkatan) }}
                    </option>
                @endforeach
            </select>
            <select name="kelas" class="rounded border-gray-300 text-sm">
                <option value="">Semua Kelas</option>
                @foreach ($kelasOptions as $kelas)
                    <option value="{{ $kelas }}" {{ request('kelas') === $kelas ? 'selected' : '' }}>
                        {{ $kelas }}</option>
                @endforeach
            </select>
            <select name="tingkatan_ngaji" class="rounded border-gray-300 text-sm">
                <option value="">Semua Tingkatan Ngaji</option>
                @foreach ($ngajiOptions as $ngaji)
                    <option value="{{ $ngaji }}" {{ request('tingkatan_ngaji') === $ngaji ? 'selected' : '' }}>
                        {{ $ngaji }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white rounded px-4 py-2 text-sm">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Santri</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tingkatan / Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tingkatan Ngaji</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Tagihan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sudah Dibayar</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Tagihan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($santris as $santri)
                        @php
                            $summary = $summaryByUser[$santri->id] ?? [
                                'total_tagihan_nominal' => 0,
                                'total_sudah_dibayar' => 0,
                                'total_sisa' => 0,
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-gray-800">{{ $santri->nama_santri ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ ucfirst($santri->tingkatan ?? '-') }} / {{ $santri->kelas ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $santri->tingkatan_ngaji ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                Rp {{ number_format($summary['total_tagihan_nominal'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                Rp {{ number_format($summary['total_sudah_dibayar'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span
                                    class="font-semibold {{ $summary['total_sisa'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    Rp {{ number_format($summary['total_sisa'], 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('admin.manual-payment.show', $santri->id) }}"
                                    class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded text-xs font-medium">
                                    Detail & Checklist
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">Data santri tidak ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
            {{ $santris->withQueryString()->links() }}
        </div>
    </div>
@endsection
