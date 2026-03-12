{{-- filepath: resources/views/santri/riwayat-pembayaran.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-6">
        <div class="bg-green-700 text-white p-4 rounded-lg">
            <h1 class="text-xl font-bold">Riwayat Pembayaran</h1>
            <p class="text-green-100 mt-1">Semua riwayat pembayaran Anda</p>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('santri.riwayat-pembayaran') }}" method="GET" class="flex flex-wrap gap-4">
                <div class="flex-1 min-w-0">
                    <select name="bulan"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Semua Bulan</option>
                        @php
                            $bulanList = [
                                'Januari',
                                'Februari',
                                'Maret',
                                'April',
                                'Mei',
                                'Juni',
                                'Juli',
                                'Agustus',
                                'September',
                                'Oktober',
                                'November',
                                'Desember',
                            ];
                        @endphp
                        @foreach ($bulanList as $index => $namaBulan)
                            <option value="{{ $index + 1 }}" {{ request('bulan') == $index + 1 ? 'selected' : '' }}>
                                {{ $namaBulan }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-0">
                    <select name="status"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Semua Status</option>
                        <option value="menunggu_verifikasi"
                            {{ request('status') == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="diterima" {{ request('status') == 'diterima' ? 'selected' : '' }}>Diterima</option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Filter
                </button>
                <a href="{{ route('santri.riwayat-pembayaran') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-lg transition">
                    Reset
                </a>
            </form>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-2">Total Pembayaran</div>
                <div class="text-lg font-bold text-gray-800">{{ $totalPembayaran }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-2">Menunggu Verifikasi</div>
                <div class="text-lg font-bold text-yellow-600">{{ $menungguVerifikasi }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-2">Diterima</div>
                <div class="text-lg font-bold text-green-600">{{ $diterima }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-600 mb-2">Ditolak</div>
                <div class="text-lg font-bold text-red-600">{{ $ditolak }}</div>
            </div>
        </div>

        <!-- Riwayat Pembayaran -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-green-700">Semua Pembayaran</h3>
            </div>
            <div class="p-6">
                @if ($pembayaran->count() > 0)
                    <div class="space-y-4">
                        @foreach ($pembayaran as $payment)
                            <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition">
                                <div class="flex flex-wrap justify-between items-start gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-3">
                                            <h4 class="font-semibold text-lg text-gray-800">
                                                Tagihan #{{ $payment->tagihan->id }}
                                            </h4>
                                            @if ($payment->status == 'menunggu_verifikasi')
                                                <span
                                                    class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-sm font-semibold">
                                                    Menunggu Verifikasi
                                                </span>
                                            @elseif($payment->status == 'diterima')
                                                <span
                                                    class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm font-semibold">
                                                    Diterima
                                                </span>
                                            @elseif($payment->status == 'ditolak')
                                                <span
                                                    class="bg-red-100 text-red-800 px-2 py-1 rounded text-sm font-semibold">
                                                    Ditolak
                                                </span>
                                            @endif
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                            <div>
                                                <div class="text-sm text-gray-600">Tanggal Bayar</div>
                                                <div class="font-semibold">{{ $payment->tanggal_bayar->format('d M Y') }}
                                                </div>
                                            </div>
                                            <div>
                                                <div class="text-sm text-gray-600">Jumlah Bayar</div>
                                                <div class="font-semibold text-green-600">
                                                    Rp{{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Catatan -->
                                        @if ($payment->catatan)
                                            <div class="mb-3">
                                                <div class="text-sm text-gray-600">Catatan</div>
                                                <div class="text-sm bg-gray-100 p-2 rounded">{{ $payment->catatan }}</div>
                                            </div>
                                        @endif

                                        <!-- Catatan Verifikator jika ditolak -->
                                        @if ($payment->status == 'ditolak' && $payment->catatan_verifikator)
                                            <div class="mb-3">
                                                <div class="text-sm text-red-600">Alasan Penolakan</div>
                                                <div class="text-sm bg-red-50 p-2 rounded border border-red-200">
                                                    {{ $payment->catatan_verifikator }}
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col items-end gap-3">
                                        @if ($payment->bukti_pembayaran)
                                            <a href="{{ Storage::url($payment->bukti_pembayaran) }}" target="_blank"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                                <i class="fa fa-file-image mr-1"></i>
                                                Lihat Bukti
                                            </a>
                                        @endif

                                        <a href="{{ route('santri.show-tagihan', $payment->tagihan->id) }}"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-semibold transition">
                                            Detail Tagihan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $pembayaran->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fa fa-credit-card text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-600 mb-2">Belum Ada Pembayaran</h3>
                        <p class="text-gray-500">
                            Anda belum melakukan pembayaran apapun.
                        </p>
                        <div class="mt-6">
                            <a href="{{ route('santri.tagihan-pembayaran') }}"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                                Lihat Tagihan
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
