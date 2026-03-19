{{-- filepath: resources/views/santri/tagihan-pembayaran.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-4">

        {{-- ─── NOMOR REKENING TUJUAN PEMBAYARAN ─── --}}
        <div class="bg-white rounded-lg shadow border border-gray-100">
            <h2 class="text-base font-bold text-gray-800 px-5 pt-4 pb-2 border-b-2 border-green-600">
                Nomor Rekening Tujuan Pembayaran
            </h2>
            @php
                $metodeAktif = ($metodePembayaranList ?? collect())->first();
                $logoPath = $metodeAktif?->logo_path;
                $logoUrl = null;
                if ($logoPath) {
                    $logoUrl =
                        str_starts_with($logoPath, 'http://') ||
                        str_starts_with($logoPath, 'https://') ||
                        str_starts_with($logoPath, '/')
                            ? $logoPath
                            : Storage::url($logoPath);
                }
            @endphp
            <div class="flex items-center justify-between px-5 py-4">
                <div class="space-y-1.5 text-sm text-gray-700">
                    <p>🏦 &nbsp;<strong>{{ $metodeAktif?->nama_bank ?? '-' }}</strong></p>
                    <p>🗒️ &nbsp;Nomor Rekening: <strong>{{ $metodeAktif?->nomor_rekening ?? '-' }}</strong></p>
                    <p>👤 &nbsp;Atas Nama: <strong>{{ $metodeAktif?->atas_nama ?? '-' }}</strong></p>
                </div>
                @if ($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $metodeAktif?->nama_bank ?? 'Metode Pembayaran' }}"
                        class="h-20 object-contain rounded-lg">
                @endif
            </div>
        </div>

        {{-- ─── TAGIHAN PEMBAYARAN HEADER ─── --}}
        <div class="bg-green-700 text-white p-4 rounded-lg">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-xl font-bold">Tagihan Pembayaran</h1>
                    <p class="text-green-100 mt-1">
                        @if ($selectedTagihan)
                            Detail tagihan #{{ $selectedTagihan->id }}
                        @elseif ($filterKategori)
                            Daftar tagihan kategori: <strong class="capitalize">{{ $filterKategori }}</strong>
                        @else
                            Daftar semua tagihan pembayaran Anda
                        @endif
                    </p>
                </div>

                <!-- Tahun Ajaran Selector -->
                @if ($tahunAjaranList->count() > 1)
                    <div class="bg-green-600 rounded-lg p-3">
                        <form action="{{ route('santri.tagihan-pembayaran') }}" method="GET"
                            class="flex items-center gap-3">
                            @if ($filterKategori)
                                <input type="hidden" name="kategori" value="{{ $filterKategori }}">
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

            <!-- Category Filter Tabs -->
            <div class="flex gap-2 mt-4 flex-wrap">
                <a href="{{ route('santri.tagihan-pembayaran', ['tahun_ajaran' => $selectedTahunAjaran->id]) }}"
                    class="px-4 py-1.5 rounded-full text-sm font-semibold transition {{ !$filterKategori ? 'bg-white text-green-700' : 'bg-green-600 text-green-100 hover:bg-green-500' }}">
                    Semua
                </a>
                <a href="{{ route('santri.tagihan-pembayaran', ['kategori' => 'registrasi', 'tahun_ajaran' => $selectedTahunAjaran->id]) }}"
                    class="px-4 py-1.5 rounded-full text-sm font-semibold transition {{ $filterKategori == 'registrasi' ? 'bg-white text-green-700' : 'bg-green-600 text-green-100 hover:bg-green-500' }}">
                    Registrasi
                </a>
                <a href="{{ route('santri.tagihan-pembayaran', ['kategori' => 'syariah', 'tahun_ajaran' => $selectedTahunAjaran->id]) }}"
                    class="px-4 py-1.5 rounded-full text-sm font-semibold transition {{ $filterKategori == 'syariah' ? 'bg-white text-green-700' : 'bg-green-600 text-green-100 hover:bg-green-500' }}">
                    Syariah
                </a>
                <a href="{{ route('santri.tagihan-pembayaran', ['kategori' => 'lainnya', 'tahun_ajaran' => $selectedTahunAjaran->id]) }}"
                    class="px-4 py-1.5 rounded-full text-sm font-semibold transition {{ $filterKategori == 'lainnya' ? 'bg-white text-green-700' : 'bg-green-600 text-green-100 hover:bg-green-500' }}">
                    Lainnya
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Panel: Tagihan List -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow">
                    <div class="border-b border-gray-200 p-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Daftar Tagihan</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Pilih tagihan untuk melihat detail atau bayar</p>
                            </div>
                            @if ($filterKategori)
                                <a href="{{ route('santri.buat-tagihan', ['kategori' => $filterKategori]) }}"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition flex items-center gap-1">
                                    <i class="fa fa-plus text-xs"></i> Buat
                                </a>
                            @else
                                <a href="{{ route('santri.buat-tagihan') }}"
                                    class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm transition flex items-center gap-1">
                                    <i class="fa fa-plus text-xs"></i> Buat
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="p-4">
                        @if ($tagihans->count() > 0)
                            <div class="space-y-3">
                                @foreach ($tagihans as $tagihan)
                                    @if ($tagihan->status == 'lunas')
                                        <div class="block p-3 rounded-lg border border-gray-300 bg-gray-100 opacity-75">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1 flex items-center gap-3">
                                                    <div
                                                        class="bg-green-600 text-white rounded-full w-6 h-6 flex items-center justify-center flex-shrink-0">
                                                        <i class="fa fa-check text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-600">#{{ $tagihan->id }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $tagihan->created_at->format('d M Y') }}
                                                        </div>
                                                        <div class="text-sm font-semibold text-gray-600">
                                                            Rp{{ number_format($tagihan->total, 0, ',', '.') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span
                                                        class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Lunas</span>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div
                                            class="p-3 rounded-lg border {{ $selectedTagihan && $selectedTagihan->id == $tagihan->id ? 'bg-green-50 border-green-300' : 'border-gray-200' }}">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <div class="font-semibold text-gray-800">#{{ $tagihan->id }}</div>
                                                    <div class="text-sm text-gray-600">
                                                        {{ $tagihan->created_at->format('d M Y') }}
                                                    </div>
                                                    <div class="text-sm font-semibold text-green-600">
                                                        Rp{{ number_format($tagihan->total, 0, ',', '.') }}
                                                    </div>
                                                    @if ($tagihan->status == 'belum_bayar')
                                                        <span
                                                            class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Belum
                                                            Bayar</span>
                                                    @elseif($tagihan->status == 'menunggu_verifikasi')
                                                        <span
                                                            class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">Menunggu</span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-col gap-1 items-end">
                                                    @if ($tagihan->status == 'belum_bayar')
                                                        <a href="{{ route('santri.upload-pembayaran', $tagihan->id) }}"
                                                            class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs font-semibold transition">Bayar</a>
                                                    @endif
                                                    <a href="{{ route('santri.tagihan-pembayaran', ['id' => $tagihan->id, 'tahun_ajaran' => $selectedTahunAjaran->id]) }}"
                                                        class="text-blue-600 hover:underline text-xs">Detail</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fa fa-file-invoice text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-600 font-semibold mb-1">Belum ada tagihan</p>
                                <p class="text-gray-500 text-sm">Tagihan akan muncul otomatis setelah diatur oleh admin.</p>
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
                                <p class="text-gray-500">Klik <strong>Detail</strong> pada tagihan di panel kiri, atau
                                    langsung klik <strong>Bayar</strong> untuk melakukan pembayaran.</p>
                            </div>
                        @else
                            <div class="text-center">
                                <i class="fa fa-file-invoice text-6xl text-gray-300 mb-6"></i>
                                <h3 class="text-xl font-semibold text-gray-800 mb-3">Belum Ada Tagihan</h3>
                                <p class="text-gray-600 max-w-md mx-auto">
                                    Tagihan Anda belum tersedia. Tagihan akan dibuatkan secara otomatis oleh administrator.
                                    Silakan hubungi admin jika tagihan belum muncul.
                                </p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
