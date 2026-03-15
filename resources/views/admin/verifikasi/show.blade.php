@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.verifikasi-pembayaran') }}"
            class="text-gray-500 hover:text-gray-700 font-medium flex items-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar Verifikasi
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Pembayaran</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Payment Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Informasi Pembayaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tanggal Pembayaran</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d F Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nominal</p>
                        <p class="text-2xl font-bold text-blue-600">Rp
                            {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 mb-1">Catatan Santri</p>
                        <p class="text-base text-gray-700 bg-gray-50 p-3 rounded-lg">
                            {{ $pembayaran->keterangan ?? 'Tidak ada catatan.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Santri & Tagihan Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Informasi Santri &
                    Tagihan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Nama Santri</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $pembayaran->tagihan->kartuPembayaran->user->nama_santri ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tahun Ajaran</p>
                        <p class="text-base font-medium text-gray-900">
                            {{ $pembayaran->tagihan->kartuPembayaran->tahunAjaran->tahun_ajaran ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Tagihan</p>
                        <p class="text-base font-medium text-gray-900">
                            @if ($pembayaran->tagihan->tagihanDetails->count() > 0)
                                <ul>
                                    @foreach ($pembayaran->tagihan->tagihanDetails as $detail)
                                        <li>- {{ $detail->jenisTagihan->nama_tagihan ?? 'Unknown' }}
                                            ({{ number_format($detail->nominal, 0, ',', '.') }})
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                Tagihan #{{ $pembayaran->tagihan_id }}
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action & Proof -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Proof Image -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-100">Bukti Transfer</h3>
                @if ($pembayaran->bukti_pembayaran)
                    <div class="rounded-lg overflow-hidden border border-gray-200 mb-4">
                        <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank">
                            <img src="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" alt="Bukti Pembayaran"
                                class="w-full h-auto hover:opacity-90 transition-opacity">
                        </a>
                    </div>
                    <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank"
                        class="block w-full text-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Lihat Ukuran Penuh
                    </a>
                @else
                    <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center text-gray-400">
                        Tidak ada bukti gambar
                    </div>
                @endif
            </div>

            <!-- Action Panel -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Aksi Verifikasi</h3>

                <form action="{{ route('admin.verifikasi-pembayaran.process', $pembayaran->id) }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-2 gap-3">
                        <button type="submit" name="status" value="ditolak"
                            class="flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tolak
                        </button>
                        <button type="submit" name="status" value="diterima"
                            class="flex items-center justify-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Terima
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
