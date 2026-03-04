{{-- filepath: resources/views/santri/buat-tagihan.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-6">
        <div class="bg-green-700 text-white p-4 rounded-lg">
            <h1 class="text-xl font-bold">
                @if ($kategoriNama == 'registrasi')
                    Pembayaran Registrasi
                @elseif($kategoriNama == 'syariah')
                    Pembayaran Syariah
                @else
                    Pembayaran Tagihan Lainnya
                @endif
            </h1>
        </div>

        <!-- Filter Data Pembayaran Santri -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Filter Data Pembayaran Santri</h2>
                <div class="flex gap-2 items-center">
                    <span class="text-gray-600">Tahun</span>
                    <select class="border border-gray-300 rounded px-3 py-1 text-sm bg-gray-100" disabled>
                        <option>{{ $tahunAjaranAktif->nama ?? '2025/2026' }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Data Santri -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h3 class="text-lg font-semibold text-green-700">Data Santri</h3>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex">
                        <span class="w-32 text-gray-600">Nama Santri</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->nama_santri }}</span>
                    </div>
                    <div class="flex mt-2">
                        <span class="w-32 text-gray-600">Kelas/Tingkatan</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->tingkatan }} - {{ $user->kelas }}</span>
                    </div>
                </div>
                <div>
                    <div class="flex">
                        <span class="w-32 text-gray-600">Nama Orang Tua</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->nama_orang_tua ?? '-' }}</span>
                    </div>
                    <div class="flex mt-2">
                        <span class="w-32 text-gray-600">No.Telp/hp</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->no_telp ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Pembayaran -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-green-700">
                    @if ($kategoriNama == 'registrasi')
                        Pembayaran Registrasi
                    @elseif($kategoriNama == 'syariah')
                        Pembayaran Syariah
                    @else
                        Pembayaran Tagihan Lainnya
                    @endif
                </h3>
            </div>

            <form action="{{ route('santri.store-tagihan') }}" method="POST" class="p-6">
                @csrf

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-300">
                                @if ($kategoriNama == 'syariah')
                                    <th class="text-left py-3 px-4 bg-gray-100 font-semibold">Bulan</th>
                                @else
                                    <th class="text-left py-3 px-4 bg-gray-100 font-semibold">Jenis Pembayaran</th>
                                @endif
                                <th class="text-left py-3 px-4 bg-gray-100 font-semibold">Nominal</th>
                                <th class="text-center py-3 px-4 bg-gray-100 font-semibold">Tambah</th>
                                <th class="text-center py-3 px-4 bg-gray-100 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($kategoriNama == 'syariah')
                                @foreach (['Januari', 'Febuary', 'Maret', 'APRIL', 'Mei', 'Juni', 'July', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $bulan)
                                    @php
                                        $jenisTagihan = $kategori->jenisTagihan->where('is_bulanan', true)->first();
                                    @endphp
                                    @if ($jenisTagihan)
                                        <tr class="border-b border-gray-200">
                                            <td class="py-3 px-4">{{ $bulan }}</td>
                                            <td class="py-3 px-4">RP.
                                                {{ number_format($jenisTagihan->nominal, 0, ',', '.') }}</td>
                                            <td class="py-3 px-4 text-center">
                                                <input type="checkbox" name="jenis_tagihan_ids[]"
                                                    value="{{ $jenisTagihan->id }}"
                                                    class="w-5 h-5 text-green-600 border-green-300 rounded focus:ring-green-500"
                                                    data-bulan="{{ $bulan }}">
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <span
                                                    class="bg-green-600 text-white px-3 py-1 rounded text-sm font-semibold">BELUM
                                                    TERBAYAR</span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                @foreach ($kategori->jenisTagihan->where('is_bulanan', false) ?? [] as $jenisTagihan)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-3 px-4">{{ $jenisTagihan->nama_tagihan }}</td>
                                        <td class="py-3 px-4">RP. {{ number_format($jenisTagihan->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="checkbox" name="jenis_tagihan_ids[]"
                                                value="{{ $jenisTagihan->id }}"
                                                class="w-5 h-5 text-green-600 border-green-300 rounded focus:ring-green-500">
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <span
                                                class="bg-green-600 text-white px-3 py-1 rounded text-sm font-semibold">BELUM
                                                TERBAYAR</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4 mt-6 pt-4 border-t border-gray-200">
                    <button type="button" onclick="window.history.back()"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">
                        Batal buat
                    </button>
                    <button type="submit"
                        class="bg-gray-800 hover:bg-gray-900 text-white font-semibold px-6 py-2 rounded transition">
                        Buat Tagihan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Handle bulk syariah selection for displaying single jenis_tagihan
        @if ($kategoriNama == 'syariah')
            document.addEventListener('DOMContentLoaded', function() {
                const checkboxes = document.querySelectorAll('input[name="jenis_tagihan_ids[]"]');
                let firstChecked = false;

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked && !firstChecked) {
                            firstChecked = true;
                            // Disable other checkboxes or allow multiple selection
                        }
                    });
                });
            });
        @endif
    </script>
@endsection
