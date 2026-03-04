{{-- filepath: resources/views/santri/tagihan-pembayaran.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-6">
        <div class="bg-green-700 text-white p-4 rounded-lg flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold">Tagihan Pembayaran</h1>
                <p class="text-green-100 mt-1">
                    @if ($selectedTagihan)
                        Detail tagihan #{{ $selectedTagihan->id }}
                    @else
                        Daftar semua tagihan pembayaran Anda
                    @endif
                </p>
            </div>

            <!-- Tahun Ajaran Selector -->
            @if ($tahunAjaranList->count() > 1)
                <div class="bg-green-600 rounded-lg p-3">
                    <form action="{{ route('santri.tagihan-pembayaran') }}" method="GET" class="flex items-center gap-3">
                        @if (request('id'))
                            <input type="hidden" name="id" value="{{ request('id') }}">
                        @endif
                        <label for="tahun_ajaran" class="text-green-100 text-sm font-semibold">Tahun Ajaran:</label>
                        <select name="tahun_ajaran" id="tahun_ajaran" onchange="this.form.submit()"
                            class="bg-white text-gray-800 px-3 py-1 rounded border-0 focus:ring-2 focus:ring-green-300">
                            @foreach ($tahunAjaranList as $ta)
                                <option value="{{ $ta->id }}"
                                    {{ $selectedTahunAjaran->id == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel: Tagihan List -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow">
                    <div class="border-b border-gray-200 p-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Daftar Tagihan</h3>
                            <a href="{{ route('santri.buat-tagihan') }}"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition">
                                <i class="fa fa-plus mr-1"></i> Buat Baru
                            </a>
                        </div>
                    </div>
                    <div class="p-4">
                        @if ($tagihans->count() > 0)
                            <div class="space-y-3">
                                @foreach ($tagihans as $tagihan)
                                    <a href="{{ route('santri.tagihan-pembayaran', ['id' => $tagihan->id, 'tahun_ajaran' => $selectedTahunAjaran->id]) }}"
                                        class="block p-3 rounded-lg border transition {{ $selectedTagihan && $selectedTagihan->id == $tagihan->id ? 'bg-green-50 border-green-200' : 'hover:bg-gray-50 border-gray-200' }}">
                                        <div class="flex justify-between items-start">
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-800">#{{ $tagihan->id }}</div>
                                                <div class="text-sm text-gray-600">
                                                    {{ $tagihan->created_at->format('d M Y') }}
                                                </div>
                                                <div class="text-sm font-semibold text-green-600">
                                                    Rp{{ number_format($tagihan->total, 0, ',', '.') }}
                                                </div>
                                            </div>
                                            <div>
                                                @if ($tagihan->status == 'belum_bayar')
                                                    <span
                                                        class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Belum
                                                        Bayar</span>
                                                @elseif($tagihan->status == 'menunggu_verifikasi')
                                                    <span
                                                        class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">Menunggu</span>
                                                @elseif($tagihan->status == 'lunas')
                                                    <span
                                                        class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Lunas</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fa fa-file-invoice text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 mb-4">Belum ada tagihan untuk tahun ajaran ini</p>
                                <a href="{{ route('santri.buat-tagihan') }}"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                                    Buat Tagihan Pertama
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Panel: Detail/Info -->
            <div class="lg:col-span-2">
                @if ($selectedTagihan)
                    <!-- Detail Tagihan -->
                    <div class="space-y-4">
                        <!-- Info Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="text-sm text-gray-600 mb-1">Status Tagihan</div>
                                <div class="text-lg font-bold">
                                    @if ($selectedTagihan->status == 'belum_bayar')
                                        <span class="text-red-600">Belum Bayar</span>
                                    @elseif($selectedTagihan->status == 'menunggu_verifikasi')
                                        <span class="text-yellow-600">Menunggu Verifikasi</span>
                                    @elseif($selectedTagihan->status == 'lunas')
                                        <span class="text-green-600">Lunas</span>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="text-sm text-gray-600 mb-1">Total Tagihan</div>
                                <div class="text-lg font-bold text-gray-800">
                                    Rp{{ number_format($selectedTagihan->total, 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="bg-white rounded-lg shadow p-4">
                                <div class="text-sm text-gray-600 mb-1">Tanggal Dibuat</div>
                                <div class="text-lg font-bold text-gray-800">
                                    {{ $selectedTagihan->created_at->format('d M Y') }}
                                </div>
                            </div>
                        </div>

                        <!-- Detail Item Tagihan -->
                        <div class="bg-white rounded-lg shadow">
                            <div class="border-b border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-green-700">Detail Item Tagihan</h3>
                            </div>
                            <div class="p-6">
                                <div class="overflow-x-auto">
                                    <table class="w-full">
                                        <thead>
                                            <tr class="border-b border-gray-300">
                                                <th class="text-left py-3 px-4 font-semibold bg-gray-50">Jenis Pembayaran
                                                </th>
                                                <th class="text-left py-3 px-4 font-semibold bg-gray-50">Bulan/Periode</th>
                                                <th class="text-right py-3 px-4 font-semibold bg-gray-50">Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($selectedTagihan->tagihanDetails as $detail)
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
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="border-t-2 border-gray-300 bg-gray-50">
                                                <td class="py-3 px-4 font-bold" colspan="2">Total</td>
                                                <td class="py-3 px-4 text-right font-bold text-lg">
                                                    Rp{{ number_format($selectedTagihan->total, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Riwayat Pembayaran -->
                        @if ($selectedTagihan->pembayaran->count() > 0)
                            <div class="bg-white rounded-lg shadow">
                                <div class="border-b border-gray-200 p-6">
                                    <h3 class="text-lg font-semibold text-green-700">Riwayat Pembayaran</h3>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-4">
                                        @foreach ($selectedTagihan->pembayaran as $pembayaran)
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
                                                                <a href="{{ Storage::url($pembayaran->bukti_pembayaran) }}"
                                                                    target="_blank"
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
                                                            <span
                                                                class="bg-red-100 text-red-800 px-3 py-1 rounded text-sm font-semibold">
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
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex justify-between items-center">
                                <a href="{{ route('santri.tagihan-pembayaran', ['tahun_ajaran' => $selectedTahunAjaran->id]) }}"
                                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded transition">
                                    <i class="fa fa-arrow-left mr-2"></i>Kembali ke Daftar
                                </a>

                                @if ($selectedTagihan->status == 'belum_bayar')
                                    <a href="{{ route('santri.upload-pembayaran', $selectedTagihan->id) }}"
                                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition">
                                        <i class="fa fa-upload mr-2"></i>Upload Bukti Pembayaran
                                    </a>
                                @elseif($selectedTagihan->status == 'menunggu_verifikasi')
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
                    </div>
                @else
                    <!-- Info Panel when no tagihan selected -->
                    <div class="bg-white rounded-lg shadow p-8">
                        <!-- Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-sm text-blue-600 mb-2">Total Tagihan</div>
                                <div class="text-xl font-bold text-blue-800">{{ $totalTagihan }}</div>
                            </div>

                            <div class="bg-yellow-50 rounded-lg p-4">
                                <div class="text-sm text-yellow-600 mb-2">Belum Bayar</div>
                                <div class="text-xl font-bold text-yellow-800">{{ $belumBayar }}</div>
                            </div>

                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="text-sm text-orange-600 mb-2">Menunggu Verifikasi</div>
                                <div class="text-xl font-bold text-orange-800">{{ $menungguVerifikasi }}</div>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-sm text-green-600 mb-2">Lunas</div>
                                <div class="text-xl font-bold text-green-800">{{ $lunas }}</div>
                            </div>
                        </div>

                        @if ($tagihans->count() > 0)
                            <div class="text-center">
                                <i class="fa fa-hand-pointer text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Pilih Tagihan</h3>
                                <p class="text-gray-500">Klik pada salah satu tagihan di panel kiri untuk melihat detailnya
                                </p>
                            </div>
                        @else
                            <div class="text-center">
                                <i class="fa fa-file-invoice text-6xl text-gray-300 mb-6"></i>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Belum Ada Tagihan</h3>
                                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                    Anda belum memiliki tagihan untuk tahun ajaran ini. Mulai dengan membuat tagihan
                                    pembayaran pertama Anda.
                                </p>
                                <a href="{{ route('santri.buat-tagihan') }}"
                                    class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                                    <i class="fa fa-plus mr-2"></i>Buat Tagihan Baru
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
