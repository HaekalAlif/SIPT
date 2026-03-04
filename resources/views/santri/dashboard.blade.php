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

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('santri.buat-tagihan', ['kategori' => 'registrasi']) }}"
                    class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition group">
                    <div class="flex items-center gap-4">
                        <div class="bg-blue-100 p-3 rounded-full group-hover:bg-blue-200 transition">
                            <i class="fa fa-clipboard-check text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Pembayaran Registrasi</h3>
                            <p class="text-sm text-gray-600">Bayar biaya pendaftaran & fasilitas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('santri.buat-tagihan', ['kategori' => 'syariah']) }}"
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

                <a href="{{ route('santri.buat-tagihan', ['kategori' => 'lainnya']) }}"
                    class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition group">
                    <div class="flex items-center gap-4">
                        <div class="bg-purple-100 p-3 rounded-full group-hover:bg-purple-200 transition">
                            <i class="fa fa-file-invoice text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Tagihan Lainnya</h3>
                            <p class="text-sm text-gray-600">Pembayaran tambahan lainnya</p>
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
                            <p class="text-sm text-gray-600">Kelola tagihan pembayaran</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Status Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Tagihan</p>
                            <p class="text-xl font-bold text-gray-800">
                                {{ $tagihans->where('status', '!=', 'lunas')->count() }}
                            </p>
                        </div>
                        <div class="text-2xl text-blue-500">
                            <i class="fa fa-file-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Menunggu Verifikasi</p>
                            <p class="text-xl font-bold text-yellow-600">
                                {{ $tagihans->where('status', 'menunggu_verifikasi')->count() }}
                            </p>
                        </div>
                        <div class="text-2xl text-yellow-500">
                            <i class="fa fa-clock"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Sudah Lunas</p>
                            <p class="text-xl font-bold text-green-600">
                                {{ $tagihans->where('status', 'lunas')->count() }}
                            </p>
                        </div>
                        <div class="text-2xl text-green-500">
                            <i class="fa fa-check-circle"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Total Tahun Ini</p>
                            <p class="text-xl font-bold text-purple-600">
                                Rp{{ number_format($tagihans->where('status', 'lunas')->sum('total'), 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="text-2xl text-purple-500">
                            <i class="fa fa-money-bill-wave"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tagihan -->
            @if ($tagihans->count() > 0)
                <div class="bg-white rounded-lg shadow">
                    <div class="border-b border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800">Tagihan Terbaru</h3>
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
                                @foreach ($tagihans->take(5) as $tagihan)
                                    @php
                                        $kategori =
                                            $tagihan->tagihanDetails->first()->jenisTagihan->kategori->nama ??
                                            'Unknown';
                                    @endphp
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6">#{{ $tagihan->id }}</td>
                                        <td class="py-3 px-6">{{ $kategori }}</td>
                                        <td class="py-3 px-6 font-semibold">
                                            Rp{{ number_format($tagihan->total, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-6">
                                            @if ($tagihan->status == 'belum_bayar')
                                                <span
                                                    class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs font-semibold">Belum
                                                    Bayar</span>
                                            @elseif($tagihan->status == 'menunggu_verifikasi')
                                                <span
                                                    class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-semibold">Menunggu
                                                    Verifikasi</span>
                                            @elseif($tagihan->status == 'lunas')
                                                <span
                                                    class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">Lunas</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-sm text-gray-600">
                                            {{ $tagihan->created_at->format('d M Y') }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <a href="{{ route('santri.show-tagihan', $tagihan->id) }}"
                                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                                Detail
                                            </a>
                                            @if ($tagihan->status == 'belum_bayar')
                                                <a href="{{ route('santri.upload-pembayaran', $tagihan->id) }}"
                                                    class="btn btn-warning">
                                                    | Bayar
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fa fa-file-invoice"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Tagihan</h3>
                    <p class="text-gray-500 mb-6">Mulai dengan membuat tagihan pembayaran pertama Anda</p>
                    <a href="{{ route('santri.buat-tagihan', ['kategori' => 'registrasi']) }}"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                        Buat Tagihan Pertama
                    </a>
                </div>
            @endif
    @endif
@endsection
