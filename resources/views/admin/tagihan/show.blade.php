@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Detail Tagihan #{{ $tagihan->id }}</h1>
            <a href="{{ route('admin.tagihan.index') }}"
                class="text-gray-500 hover:text-gray-700 font-medium flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Info Santri & Tagihan -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Informasi Santri</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="text-gray-500">Nama Santri:</p>
                        <p class="font-medium text-gray-800">{{ $tagihan->kartuPembayaran->user->nama_santri ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">NIS:</p>
                        <p class="font-medium text-gray-800">{{ $tagihan->kartuPembayaran->user->nis ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tahun Ajaran:</p>
                        <p class="font-medium text-gray-800">
                            {{ $tagihan->kartuPembayaran->tahunAjaran->tahun_ajaran ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">No. Kartu:</p>
                        <p class="font-medium text-gray-800">{{ $tagihan->kartuPembayaran->no_kartu ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Ringkasan Tagihan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <p class="text-gray-500">Total Tagihan:</p>
                        <p class="font-bold text-gray-800">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-500">Jenis:</p>
                        <p class="font-medium text-gray-800">
                            {{ $tagihan->tagihanDetails->count() > 0 ? $tagihan->tagihanDetails[0]->jenisTagihan->nama : 'N/A' }}
                        </p>
                    </div>
                    <div class="flex justify-between">
                        <p class="text-gray-500">Status:</p>
                        <span
                            class="px-2 py-0.5 rounded text-xs font-semibold
                            {{ $tagihan->status === 'lunas' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $tagihan->status === 'menunggu_verifikasi' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $tagihan->status === 'belum_bayar' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $tagihan->status)) }}
                        </span>
                    </div>
                    <div class="mt-4 pt-3 border-t border-gray-100">
                        @if ($tagihan->status !== 'lunas')
                            <a href="#"
                                class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition-colors">
                                Catat Pembayaran Manual
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Item Tagihan -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Rincian Item Tagihan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Item Tagihan</th>
                                <th scope="col"
                                    class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah (Rp)</th>
                                <th scope="col"
                                    class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($tagihan->tagihanDetails as $detail)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $detail->jenisTagihan->nama ?? 'Item Hapus' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">
                                        Rp {{ number_format($detail->jumlah, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <form
                                            action="{{ route('admin.tagihan.delete-detail', ['tagihan' => $tagihan->id, 'detail' => $detail->id]) }}"
                                            method="POST"
                                            onsubmit="return confirm('Hapus item ini? Total tagihan akan berkurang.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 text-xs font-bold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-gray-50">
                                <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right">Total Tagihan</td>
                                <td class="px-4 py-3 text-sm font-bold text-gray-900 text-right">
                                    Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                                </td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Riwayat Pembayaran -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Riwayat Pembayaran</h3>
                @if ($tagihan->pembayaran->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs text-gray-500">Tanggal</th>
                                    <th class="px-4 py-2 text-left text-xs text-gray-500">No. Ref</th>
                                    <th class="px-4 py-2 text-left text-xs text-gray-500">Metode</th>
                                    <th class="px-4 py-2 text-right text-xs text-gray-500">Jumlah</th>
                                    <th class="px-4 py-2 text-center text-xs text-gray-500">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($tagihan->pembayaran as $bayar)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($bayar->tanggal_bayar)->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $bayar->no_referensi ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ ucfirst($bayar->metode_pembayaran) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 text-right font-medium">
                                            Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span
                                                class="px-2 py-0.5 rounded text-xs font-semibold
                                            {{ $bayar->status === 'diterima' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $bayar->status === 'ditolak' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $bayar->status === 'menunggu_verifikasi' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                                {{ ucfirst(str_replace('_', ' ', $bayar->status)) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-gray-500 italic text-center py-4">Belum ada riwayat pembayaran untuk tagihan ini.
                    </p>
                @endif
            </div>
        </div>
    </div>
@endsection
