{{-- filepath: resources/views/santri/show-tagihan.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-6">
        <div class="bg-green-700 text-white p-4 rounded-lg">
            <h1 class="text-xl font-bold">Detail Tagihan #{{ $tagihan->id }}</h1>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Status Tagihan</div>
                <div class="text-lg font-bold">
                    @if ($tagihan->status == 'belum_bayar')
                        <span class="text-red-600">Belum Bayar</span>
                    @elseif($tagihan->status == 'menunggu_verifikasi')
                        <span class="text-yellow-600">Menunggu Verifikasi</span>
                    @elseif($tagihan->status == 'lunas')
                        <span class="text-green-600">Lunas</span>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Total Tagihan</div>
                <div class="text-lg font-bold text-gray-800">
                    Rp{{ number_format($tagihan->total, 0, ',', '.') }}
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-1">Tanggal Dibuat</div>
                <div class="text-lg font-bold text-gray-800">
                    {{ $tagihan->created_at->format('d M Y') }}
                </div>
            </div>
        </div>

        <!-- Data Santri -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-green-700 border-b border-gray-200 pb-4 mb-4">Data Santri</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="flex mb-2">
                        <span class="w-32 text-gray-600">Nama Santri</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->nama_santri }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-600">Kelas/Tingkatan</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->tingkatan }} - {{ $user->kelas }}</span>
                    </div>
                </div>
                <div>
                    <div class="flex mb-2">
                        <span class="w-32 text-gray-600">Nama Orang Tua</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $user->nama_orang_tua ?? '-' }}</span>
                    </div>
                    <div class="flex">
                        <span class="w-32 text-gray-600">Nomor Kartu</span>
                        <span class="text-gray-600 mr-2">:</span>
                        <span class="font-semibold">{{ $tagihan->kartuPembayaran->nomor_kartu }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Tagihan -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-green-700">Detail Item Tagihan</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-300">
                                <th class="text-left py-3 px-4 font-semibold bg-gray-50">Jenis Pembayaran</th>
                                <th class="text-left py-3 px-4 font-semibold bg-gray-50">Bulan/Periode</th>
                                <th class="text-right py-3 px-4 font-semibold bg-gray-50">Nominal</th>
                                <th class="text-center py-3 px-4 font-semibold bg-gray-50">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tagihan->tagihanDetails as $detail)
                                <tr class="border-b border-gray-200">
                                    <td class="py-3 px-4">{{ $detail->jenisTagihan->nama_tagihan }}</td>
                                    <td class="py-3 px-4">
                                        @if ($detail->bulan)
                                            {{ $detail->bulan }}
                                        @else
                                            <span class="text-gray-500">Sekali bayar</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right font-semibold">
                                        Rp{{ number_format($detail->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($detail->status == 'belum_bayar')
                                            <span
                                                class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Belum
                                                Bayar</span>
                                        @elseif($detail->status == 'lunas')
                                            <span
                                                class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Lunas</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 bg-gray-50">
                                <td class="py-3 px-4 font-bold" colspan="2">Total</td>
                                <td class="py-3 px-4 text-right font-bold text-lg">
                                    Rp{{ number_format($tagihan->total, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        @if ($tagihan->pembayaran->count() > 0)
            <div class="bg-white rounded-lg shadow">
                <div class="border-b border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-green-700">Riwayat Pembayaran</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach ($tagihan->pembayaran as $pembayaran)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-semibold text-gray-800">
                                            Pembayaran #{{ $pembayaran->id }}
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            Tanggal: {{ $pembayaran->tanggal_bayar->format('d M Y') }}
                                        </div>
                                        <div class="text-sm text-gray-600">
                                            Jumlah: <span
                                                class="font-semibold">Rp{{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
                                        </div>
                                        @if ($pembayaran->bukti_pembayaran)
                                            <div class="mt-2">
                                                <a href="{{ Storage::url($pembayaran->bukti_pembayaran) }}" target="_blank"
                                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                                    <i class="fa fa-file-image mr-1"></i>
                                                    Lihat Bukti Pembayaran
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        @if ($pembayaran->status == 'menunggu_verifikasi')
                                            <span
                                                class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded text-sm font-semibold">
                                                Menunggu Verifikasi
                                            </span>
                                        @elseif($pembayaran->status == 'diterima')
                                            <span
                                                class="bg-green-100 text-green-800 px-3 py-1 rounded text-sm font-semibold">
                                                Diterima
                                            </span>
                                        @elseif($pembayaran->status == 'ditolak')
                                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded text-sm font-semibold">
                                                Ditolak
                                            </span>
                                        @endif
                                        @if ($pembayaran->verified_at)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $pembayaran->verified_at->format('d M Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex justify-between items-center bg-white rounded-lg shadow p-6">
            <button type="button" onclick="window.history.back()"
                class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded transition">
                Kembali
            </button>

            @if ($tagihan->status == 'belum_bayar')
                <a href="{{ route('santri.upload-pembayaran', $tagihan->id) }}"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">
                    Upload Bukti Pembayaran
                </a>
            @elseif($tagihan->status == 'menunggu_verifikasi')
                <div class="text-yellow-600 font-semibold">
                    <i class="fa fa-clock mr-2"></i>
                    Menunggu Verifikasi Bendahara
                </div>
            @else
                <div class="text-green-600 font-semibold">
                    <i class="fa fa-check-circle mr-2"></i>
                    Pembayaran Telah Lunas
                </div>
            @endif
        </div>
    </div>
@endsection
