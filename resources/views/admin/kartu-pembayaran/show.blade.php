@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <a href="{{ route('admin.kartu-pembayaran.index') }}"
                class="text-gray-500 hover:text-gray-700 font-medium flex items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Data Kartu
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Detail Kartu Pembayaran</h1>
            <p class="text-gray-600 mt-1">Informasi lengkap kartu pembayaran santri.</p>
        </div>
        {{-- tombol cetak dihilangkan --}}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Student Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:col-span-1">
            @php $santri = $kartuPembayaran->user; @endphp
            <div class="flex items-center space-x-4 mb-6">
                <div
                    class="h-16 w-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold">
                    {{ strtoupper(substr($santri->nama_santri ?? ($santri->name ?? '?'), 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">{{ $santri->nama_santri ?? ($santri->name ?? '-') }}</h3>
                    <p class="text-sm text-gray-500">{{ $santri->email }}</p>
                </div>
            </div>

            <div class="space-y-3">
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600 text-sm">Kelas / Tingkatan</span>
                    <span class="font-medium text-gray-800">
                        {{ $santri->kelas ?? '-' }}{{ $santri->tingkatan ? ' - ' . $santri->tingkatan : '' }}
                    </span>
                </div>
                <div class="flex justify-between border-b pb-2">
                    <span class="text-gray-600 text-sm">Tahun Masuk</span>
                    <span class="font-medium text-gray-800">
                        {{ $santri->tahunAjaranMasuk?->nama ?? '-' }}
                    </span>
                </div>
                <div class="flex justify-between pt-2">
                    <span class="text-gray-600 text-sm">Status Santri</span>
                    <span
                        class="px-2 py-1 text-xs font-semibold rounded-full {{ ($santri->status ?? '') === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                        {{ $santri->status ? ucfirst($santri->status) : 'Aktif' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Card Details -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:col-span-2">
            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Informasi Kartu</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Nomor Kartu</p>
                    <p class="text-xl font-mono font-bold text-blue-600">{{ $kartuPembayaran->nomor_kartu }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Tahun Ajaran</p>
                    <p class="text-lg font-medium text-gray-800">{{ $kartuPembayaran->tahunAjaran->nama ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Tanggal Dibuat</p>
                    <p class="text-gray-800">{{ $kartuPembayaran->created_at->format('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Tagihan</p>
                    <p class="text-xl font-bold text-gray-800">Rp
                        {{ number_format($kartuPembayaran->tagihan->sum('total'), 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Summary Stats for Bills -->
            <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-3 rounded-lg text-center">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Total Tagihan</p>
                    <p class="text-lg font-bold text-gray-800">{{ $kartuPembayaran->tagihan->count() }}</p>
                </div>
                <div class="bg-green-50 p-3 rounded-lg text-center">
                    <p class="text-xs text-green-600 uppercase font-semibold">Lunas</p>
                    <p class="text-lg font-bold text-green-700">
                        {{ $kartuPembayaran->tagihan->where('status', 'lunas')->count() }}</p>
                </div>
                <div class="bg-blue-50 p-3 rounded-lg text-center">
                    <p class="text-xs text-blue-600 uppercase font-semibold">Terbayar</p>
                    <p class="text-lg font-bold text-blue-700">Rp
                        {{ number_format($kartuPembayaran->tagihan->flatMap->pembayaran->where('status', 'diterima')->sum('jumlah_bayar'), 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-yellow-50 p-3 rounded-lg text-center">
                    <p class="text-xs text-yellow-600 uppercase font-semibold">Pending</p>
                    <p class="text-lg font-bold text-yellow-700">
                        {{ $kartuPembayaran->tagihan->whereIn('status', ['menunggu_verifikasi', 'belum_bayar'])->count() }}
                    </p>
                </div>
                <div class="bg-red-50 p-3 rounded-lg text-center">
                    <p class="text-xs text-red-600 uppercase font-semibold">Sisa Tagihan</p>
                    <p class="text-lg font-bold text-red-700">Rp
                        {{ number_format($kartuPembayaran->tagihan->where('status', '!=', 'lunas')->sum('total'), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bills / Tagihan Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">Daftar Tagihan</h3>
            <a href="{{ route('admin.tagihan.create', ['kartu_id' => $kartuPembayaran->id]) }}"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-lg flex items-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Tagihan
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-sm">
                        <th class="p-4 font-semibold border-b">ID Tagihan</th>
                        <th class="p-4 font-semibold border-b">Tanggal</th>
                        <th class="p-4 font-semibold border-b">Detail Item</th>
                        <th class="p-4 font-semibold border-b text-right">Total</th>
                        <th class="p-4 font-semibold border-b text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($kartuPembayaran->tagihan as $tagihan)
                        <tr class="hover:bg-gray-50 transition-colors border-b last:border-0">
                            <td class="p-4 font-mono text-gray-600">#{{ $tagihan->id }}</td>
                            <td class="p-4 text-gray-800">{{ $tagihan->created_at->format('d M Y') }}</td>
                            <td class="p-4">
                                <ul class="list-disc list-inside text-gray-600 text-xs">
                                    @foreach ($tagihan->tagihanDetails as $detail)
                                        <li>
                                            {{ $detail->jenisTagihan->nama_tagihan ?? 'Tagihan' }}
                                            @if ($detail->bulan)
                                                ({{ ucfirst($detail->bulan) }})
                                            @endif
                                            - Rp {{ number_format($detail->nominal, 0, ',', '.') }}
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="p-4 text-right font-medium text-gray-800">
                                Rp {{ number_format($tagihan->total, 0, ',', '.') }}
                            </td>
                            <td class="p-4 text-center">
                                @if ($tagihan->status == 'lunas')
                                    <span
                                        class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">Lunas</span>
                                @elseif($tagihan->status == 'menunggu_verifikasi')
                                    <span
                                        class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">Verifikasi</span>
                                @elseif($tagihan->status == 'belum_bayar')
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">Belum
                                        Bayar</span>
                                @else
                                    <span
                                        class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">{{ ucfirst($tagihan->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-2 text-gray-300"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p>Belum ada tagihan pada kartu ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
