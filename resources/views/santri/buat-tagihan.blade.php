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
                        <span class="w-36 text-gray-600">Nama Santri</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->nama_santri }}</span>
                    </div>
                    <div class="flex mt-2">
                        <span class="w-36 text-gray-600">Kelas/Tingkatan</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->tingkatan }} - {{ $user->kelas }}</span>
                    </div>
                </div>
                <div>
                    <div class="flex">
                        <span class="w-36 text-gray-600">Nama Orang Tua</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->nama_orang_tua ?? '-' }}</span>
                    </div>
                    <div class="flex mt-2">
                        <span class="w-36 text-gray-600">No.Telp/hp</span>
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
                                @php
                                    $jenisTagihanSyariah = $kategori->jenisTagihan->where('is_bulanan', true)->values();
                                    $bulanList = [
                                        'Juli',
                                        'Agustus',
                                        'September',
                                        'Oktober',
                                        'November',
                                        'Desember',
                                        'Januari',
                                        'Februari',
                                        'Maret',
                                        'April',
                                        'Mei',
                                        'Juni',
                                    ];
                                @endphp
                                @if ($jenisTagihanSyariah->isEmpty())
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-400 text-sm">
                                            Tidak ada tagihan syariah yang tersedia untuk Anda saat ini.
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($jenisTagihanSyariah as $jenisBulanan)
                                        <tr class="bg-gray-100 border-y border-gray-300">
                                            <td colspan="4" class="py-2 px-4 text-sm font-semibold text-gray-700">
                                                {{ $jenisBulanan->nama_tagihan }}
                                                <span class="ml-2 text-xs font-normal text-gray-500">(Rp
                                                    {{ number_format($jenisBulanan->nominal, 0, ',', '.') }}/bulan)</span>
                                            </td>
                                        </tr>

                                        @foreach ($bulanList as $bulan)
                                            @php
                                                $sudahBayar =
                                                    isset($paidBulanMap[$jenisBulanan->id]) &&
                                                    in_array($bulan, $paidBulanMap[$jenisBulanan->id]);
                                            @endphp
                                            <tr
                                                class="border-b border-gray-200 {{ $sudahBayar ? 'opacity-60 bg-gray-50' : '' }}">
                                                <td class="py-3 px-4">{{ $bulan }}</td>
                                                <td class="py-3 px-4">Rp
                                                    {{ number_format($jenisBulanan->nominal, 0, ',', '.') }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    @if ($sudahBayar)
                                                        <input type="checkbox" disabled checked
                                                            class="w-5 h-5 border-gray-300 rounded">
                                                    @else
                                                        <input type="checkbox"
                                                            name="syariah_items[{{ $jenisBulanan->id }}][{{ $bulan }}]"
                                                            value="1"
                                                            class="w-5 h-5 text-green-600 border-green-300 rounded focus:ring-green-500">
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    @if ($sudahBayar)
                                                        <span
                                                            class="bg-green-600 text-white px-3 py-1 rounded text-sm font-semibold">SUDAH
                                                            TERBAYAR</span>
                                                    @else
                                                        <span
                                                            class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm font-semibold">BELUM
                                                            TERBAYAR</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endif
                            @else
                                @forelse ($kategori->jenisTagihan->where('is_bulanan', false) as $jenisTagihan)
                                    @php
                                        $sudahBayar = in_array($jenisTagihan->id, $paidJenisIds ?? []);
                                    @endphp
                                    <tr class="border-b border-gray-200 {{ $sudahBayar ? 'opacity-60 bg-gray-50' : '' }}">
                                        <td class="py-3 px-4">{{ $jenisTagihan->nama_tagihan }}</td>
                                        <td class="py-3 px-4">Rp {{ number_format($jenisTagihan->nominal, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($sudahBayar)
                                                <input type="checkbox" disabled checked
                                                    class="w-5 h-5 border-gray-300 rounded">
                                            @else
                                                <input type="checkbox" name="jenis_tagihan_ids[]"
                                                    value="{{ $jenisTagihan->id }}"
                                                    class="w-5 h-5 text-green-600 border-green-300 rounded focus:ring-green-500">
                                            @endif
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($sudahBayar)
                                                <span
                                                    class="bg-green-600 text-white px-3 py-1 rounded text-sm font-semibold">SUDAH
                                                    TERBAYAR</span>
                                            @else
                                                <span
                                                    class="bg-red-100 text-red-700 px-3 py-1 rounded text-sm font-semibold">BELUM
                                                    TERBAYAR</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-8 text-center text-gray-400 text-sm">
                                            Tidak ada tagihan yang tersedia untuk Anda saat ini.
                                        </td>
                                    </tr>
                                @endforelse
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
        // Pilih semua checkbox sekaligus
        document.addEventListener('DOMContentLoaded', function() {
            const checkAll = document.getElementById('check-all');
            if (checkAll) {
                checkAll.addEventListener('change', function() {
                    document.querySelectorAll('input[type="checkbox"]:not([disabled])').forEach(cb => {
                        cb.checked = this.checked;
                    });
                });
            }
        });
    </script>
@endsection
