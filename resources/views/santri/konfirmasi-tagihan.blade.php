{{-- filepath: resources/views/santri/konfirmasi-tagihan.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-6">
        <div class="bg-green-700 text-white p-4 rounded-lg">
            <h1 class="text-xl font-bold">Nomor Rekening Tujuan Pembayaran</h1>
        </div>

        <!-- Rekening Pembayaran -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa fa-university text-blue-600 text-lg"></i>
                        <span class="font-bold text-lg">Bank BRI</span>
                    </div>
                    <div class="text-sm text-gray-600 mb-1">— Nomor Rekening: 1234-5678-9012-3456</div>
                    <div class="text-sm text-gray-600">👤 Atas Nama: Yayasan Darul Ulum</div>
                </div>
                <div class="bg-blue-600 text-white px-4 py-8 rounded">
                    <div class="text-center">
                        <div class="font-bold text-lg">BRI</div>
                        <div class="text-xs">BANK BRI</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ringkasan Pembuatan Tagihan Pembayaran -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-green-700">Ringkasan Pembuatan Tagihan Pembayaran</h3>
            </div>

            <div class="p-6">
                <!-- Tagihan Details -->
                <div class="mb-6">
                    @php
                        $kategoriName = $tagihan->tagihanDetails->first()->jenisTagihan->kategori->nama ?? 'Unknown';
                    @endphp
                    <h4 class="font-semibold text-gray-800 mb-4">Tagihan {{ $kategoriName }}</h4>

                    <div class="space-y-2">
                        @foreach ($tagihan->tagihanDetails->groupBy('jenis_tagihan_id') as $jenisTagihanId => $details)
                            @php
                                $jenisTagihan = $details->first()->jenisTagihan;
                                $isMonthly = $jenisTagihan->is_bulanan;
                            @endphp

                            @if ($isMonthly)
                                <!-- For monthly payments, show all months -->
                                @foreach ($details as $detail)
                                    <div class="flex justify-between items-center py-2">
                                        <span>{{ $detail->bulan }}</span>
                                        <span class="font-semibold">RP.
                                            {{ number_format($detail->nominal, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                            @else
                                <!-- For one-time payments -->
                                <div class="flex justify-between items-center py-2">
                                    <span>{{ $jenisTagihan->nama_tagihan }}</span>
                                    <span class="font-semibold">RP.
                                        {{ number_format($details->first()->nominal, 0, ',', '.') }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <hr class="my-4">

                <!-- Total -->
                <div class="flex justify-between items-center font-bold text-lg">
                    <span>Total Tagihan</span>
                    <span>Rp.{{ number_format($tagihan->total, 0, ',', '.') }}.00</span>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4 mt-8">
                    <button type="button" onclick="window.history.back()"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">
                        Batal
                    </button>
                    <a href="{{ route('santri.upload-pembayaran', $tagihan->id) }}"
                        class="bg-gray-800 hover:bg-gray-900 text-white font-semibold px-6 py-2 rounded transition">
                        Upload Bukti
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
