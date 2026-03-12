{{-- filepath: resources/views/santri/dashboard.blade.php --}}
@extends('layouts.santri')

@section('content')
    @if (!$tahunAjaranAktif)
        <!-- No Active Academic Year -->
        <div class="text-center py-12">
            <div class="bg-red-50 border border-red-200 rounded-lg p-8 mb-6">
                <i class="fa fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <h3 class="text-xl font-bold text-red-700 mb-2">Tidak Ada Tahun Ajaran Aktif</h3>
                <p class="text-red-600">Silakan hubungi administrator untuk mengaktifkan tahun ajaran.</p>
            </div>
            <a href="{{ route('logout') }}"
                class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Logout
            </a>
        </div>
    @else
        <div class="space-y-6">
            <!-- Welcome Card -->
            <div class="bg-green-600 rounded-lg shadow p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold mb-2">Selamat Datang, {{ Auth::user()->nama_santri }}!</h2>
                        <p class="text-green-100">Kelola pembayaran pesantren Anda dengan mudah</p>
                        <p class="text-green-100 text-sm">Tahun Ajaran: {{ $tahunAjaranAktif->nama }}</p>
                    </div>
                    <div class="text-6xl opacity-20">
                        <i class="fa fa-mosque"></i>
                    </div>
                </div>
            </div>

            @if (!$userHasTahunAjaran)
                <!-- Warning: No Tahun Ajaran Set -->
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa fa-info-circle text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-yellow-800">
                                Tahun ajaran Anda belum diatur. Silakan hubungi administrator untuk mengatur tahun ajaran
                                masuk Anda sebelum membuat tagihan.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('santri.tagihan-pembayaran', ['kategori' => 'registrasi']) }}"
                        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition group">
                        <div class="flex items-center gap-4">
                            <div class="bg-blue-100 p-3 rounded-full group-hover:bg-blue-200 transition">
                                <i class="fa fa-clipboard-check text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Pembayaran Registrasi</h3>
                                <p class="text-sm text-gray-600">Bayar biaya pendaftaran</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('santri.tagihan-pembayaran', ['kategori' => 'syariah']) }}"
                        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition group">
                        <div class="flex items-center gap-4">
                            <div class="bg-green-100 p-3 rounded-full group-hover:bg-green-200 transition">
                                <i class="fa fa-calendar-alt text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Pembayaran Syariah</h3>
                                <p class="text-sm text-gray-600">SPP & biaya bulanan</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('santri.tagihan-pembayaran', ['kategori' => 'lainnya']) }}"
                        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition group">
                        <div class="flex items-center gap-4">
                            <div class="bg-purple-100 p-3 rounded-full group-hover:bg-purple-200 transition">
                                <i class="fa fa-file-invoice text-purple-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Tagihan Lainnya</h3>
                                <p class="text-sm text-gray-600">Pembayaran tambahan</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('santri.tagihan-pembayaran') }}"
                        class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition group">
                        <div class="flex items-center gap-4">
                            <div class="bg-orange-100 p-3 rounded-full group-hover:bg-orange-200 transition">
                                <i class="fa fa-list text-orange-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Lihat Semua Tagihan</h3>
                                <p class="text-sm text-gray-600">Kelola pembayaran</p>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Total Tagihan Ringkasan -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Tagihan Keseluruhan</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-400">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Tagihan</p>
                                    <p class="text-xs text-gray-400">Semua tahun ajaran</p>
                                    <p class="text-lg font-bold text-gray-800 mt-2">
                                        Rp{{ number_format($totalTagihanNominal, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-2xl text-red-300"><i class="fa fa-file-invoice-dollar"></i></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-400">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Sudah Dibayar</p>
                                    <p class="text-xs text-gray-400">Status lunas</p>
                                    <p class="text-lg font-bold text-green-600 mt-2">
                                        Rp{{ number_format($totalSudahDibayar, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-2xl text-green-300"><i class="fa fa-check-circle"></i></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-400">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Sisa yang Harus Dibayar</p>
                                    <p class="text-xs text-gray-400">Belum lunas</p>
                                    <p class="text-lg font-bold text-orange-600 mt-2">
                                        Rp{{ number_format($totalSisa, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="text-2xl text-orange-300"><i class="fa fa-money-bill-wave"></i></div>
                            </div>
                        </div>
                    </div>

                    <!-- Per Kategori -->
                    @if (count($tagihanPerKategori) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            @foreach ($tagihanPerKategori as $kat => $data)
                                @php
                                    $allLunas = $data['sisa'] === 0 && $data['total_nominal'] > 0;
                                    $icon = str_contains($kat, 'registrasi')
                                        ? 'fa-clipboard-check text-blue-500'
                                        : (str_contains($kat, 'syariah')
                                            ? 'fa-calendar-alt text-green-500'
                                            : 'fa-file-invoice text-purple-500');
                                @endphp
                                <div class="bg-white rounded-lg shadow p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center gap-2">
                                            <i class="fa {{ $icon }}"></i>
                                            <span class="font-semibold text-gray-800 capitalize">{{ $kat }}</span>
                                        </div>
                                        @if ($allLunas)
                                            <span
                                                class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">
                                                <i class="fa fa-check mr-1"></i>Lunas
                                            </span>
                                        @else
                                            <span
                                                class="bg-orange-100 text-orange-700 text-xs font-semibold px-2 py-1 rounded-full">
                                                Belum Lunas
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-1 text-sm">
                                        <div class="flex justify-between text-gray-600">
                                            <span>Total</span>
                                            <span
                                                class="font-semibold">Rp{{ number_format($data['total_nominal'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between text-gray-600">
                                            <span>Dibayar</span>
                                            <span
                                                class="font-semibold text-green-600">Rp{{ number_format($data['sudah_dibayar'], 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex justify-between border-t pt-1">
                                            <span class="font-semibold text-gray-700">Sisa</span>
                                            <span
                                                class="font-bold {{ $data['sisa'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                                Rp{{ number_format($data['sisa'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                    @if ($data['sisa'] > 0)
                                        <a href="{{ route('santri.tagihan-pembayaran', ['kategori' => $kat]) }}"
                                            class="mt-3 block text-center bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-3 py-1.5 rounded transition">
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Status Tagihan Tahun Ajaran Aktif -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Tagihan - {{ $tahunAjaranAktif->nama }}
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Belum Bayar</p>
                                    <p class="text-2xl font-bold text-red-600 mt-2">
                                        {{ $belumBayar }}
                                    </p>
                                </div>
                                <div class="text-2xl text-red-400">
                                    <i class="fa fa-clock"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Menunggu Verifikasi</p>
                                    <p class="text-2xl font-bold text-yellow-600 mt-2">
                                        {{ $menungguVerifikasi }}
                                    </p>
                                </div>
                                <div class="text-2xl text-yellow-400">
                                    <i class="fa fa-hourglass-half"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg shadow p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Sudah Lunas</p>
                                    <p class="text-2xl font-bold text-green-600 mt-2">
                                        {{ $lunas }}
                                    </p>
                                </div>
                                <div class="text-2xl text-green-400">
                                    <i class="fa fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Active Tagihan -->
                @if ($activTagihans->count() > 0)
                    <div class="bg-white rounded-lg shadow">
                        <div class="border-b border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-800">Tagihan Aktif - Perlu Diperhatikan</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left py-3 px-6 font-semibold text-gray-700">ID Tagihan</th>
                                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Kategori</th>
                                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Total</th>
                                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Status</th>
                                        <th class="text-left py-3 px-6 font-semibold text-gray-700">Tanggal</th>
                                        <th class="text-center py-3 px-6 font-semibold text-gray-700">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentTagihan as $tagihan)
                                        @php
                                            $kategori =
                                                $tagihan->tagihanDetails->first()?->jenisTagihan?->kategori?->nama ??
                                                'Unknown';
                                        @endphp
                                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                                            <td class="py-3 px-6 font-semibold">#{{ $tagihan->id }}</td>
                                            <td class="py-3 px-6">{{ $kategori }}</td>
                                            <td class="py-3 px-6 font-semibold">
                                                Rp{{ number_format($tagihan->total, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3 px-6">
                                                @if ($tagihan->status == 'belum_bayar')
                                                    <span
                                                        class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                        Belum Bayar
                                                    </span>
                                                @elseif($tagihan->status == 'menunggu_verifikasi')
                                                    <span
                                                        class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                                        Menunggu Verifikasi
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-6 text-sm text-gray-600">
                                                {{ $tagihan->created_at->format('d M Y') }}
                                            </td>
                                            <td class="py-3 px-6 text-center">
                                                @if ($tagihan->status == 'belum_bayar')
                                                    <a href="{{ route('santri.upload-pembayaran', $tagihan->id) }}"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-semibold transition">Bayar</a>
                                                @else
                                                    <a href="{{ route('santri.tagihan-pembayaran', ['id' => $tagihan->id]) }}"
                                                        class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Detail</a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    @endif
@endsection
