{{-- filepath: resources/views/santri/kartu-pembayaran.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-6">
        <div class="bg-green-700 text-white p-4 rounded-lg">
            <h1 class="text-xl font-bold">Kartu Pembayaran</h1>
            <p class="text-green-100 mt-1">Laporan kartu pembayaran seperti bentuk fisik</p>
        </div>

        @if(isset($error))
            <!-- Error Message -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="text-center">
                    <i class="fa fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                    <h3 class="text-lg font-semibold text-red-800 mb-3">{{ $error }}</h3>
                    <p class="text-red-600 mb-6">
                        Silakan hubungi admin untuk setup tahun ajaran masuk Anda.
                    </p>
                    <a href="{{ route('santri.dashboard') }}"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @else
            <!-- Pilihan Tahun Ajaran -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-green-700">Pilih Tahun Ajaran</h3>
                </div>
                <form method="GET" action="{{ route('santri.kartu-pembayaran') }}" class="flex gap-4 items-center">
                    <select name="tahun_ajaran_id" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500" onchange="this.form.submit()">
                        @foreach($allTahunAjaran as $ta)
                            <option value="{{ $ta->id }}" {{ $tahunAjaran && $tahunAjaran->id == $ta->id ? 'selected' : '' }}>
                                {{ $ta->nama }} {{ $ta->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <noscript>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Tampilkan</button>
                    </noscript>
                </form>
            </div>

            @if($kartuPembayaran)
                <!-- Kartu Pembayaran Format Fisik -->
                <div class="bg-white rounded-lg shadow p-8">
                    <!-- Header Kartu -->
                    <div class="text-center border-2 border-black p-4 mb-6">
                        <div class="flex justify-between items-center mb-4">
                            <div class="text-left">
                                <img src="/logo/pesantren-logo.png" alt="Logo" class="h-16 w-16 mb-2" onerror="this.style.display='none'">
                            </div>
                            <div class="text-center flex-1">
                                <h2 class="font-bold text-lg">KARTU PEMBAYARAN ADMINISTRASI KEUANGAN</h2>
                                <h3 class="font-bold text-md">PONDOK PESANTREN "DARUL ULUM"</h3>
                                <p class="text-sm">SUMBERGEDE SEKAMPUNG LAMPUNG TIMUR</p>
                                <p class="text-sm font-bold">TAHUN PELAJARAN {{ strtoupper($tahunAjaran->nama) }}</p>
                            </div>
                            <div class="text-right">
                                <div class="border border-black p-2">
                                    <p class="font-bold">{{ substr($kartuPembayaran->nomor_kartu, -1) }}</p>
                                    <p class="text-xs">Nomor Urut Kartu</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Info Santri -->
                        <div class="grid grid-cols-2 gap-4 text-left text-sm">
                            <div>
                                <p><strong>NAMA SANTRI:</strong> {{ strtoupper($user->nama_santri) }}</p>
                            </div>
                            <div>
                                <p><strong>NAMA ORANG TUA:</strong> {{ strtoupper($user->nama_orang_tua ?? '...................................') }}</p>
                            </div>
                            <div>
                                <p><strong>KELAS/TINGKAT:</strong> {{ $user->tingkatan }}/{{ $user->kelas ?? '-' }} (MI/SMP/MTS/MA/SJ)</p>
                            </div>
                            <div>
                                <p><strong>No. HP:</strong> {{ $user->no_telp ?? '...................................' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Pembayaran -->
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse border border-black text-xs">
                            <thead>
                                <tr class="bg-purple-200">
                                    <th class="border border-black px-2 py-1 text-center font-bold" rowspan="2">BULAN</th>
                                    @foreach($kategoriTagihan as $kategori)
                                        @php
                                            $spanCount = match($kategori->nama) {
                                                'Syariah' => 2, // Bayar, TTD 
                                                'Registrasi' => 2, // Bayar, TTD
                                                'Infaq' => 2, // Bayar, TTD
                                                'Seragam' => 2, // Bayar, TTD
                                                'Kitab/SPP' => 2, // Bayar, TTD  
                                                'DPP Pesantren' => 2, // Bayar, TTD
                                                'Fasilitas Kamar' => 2, // Bayar, TTD
                                                'Imtihan' => 4, // Awal dan Tsani masing-masing Bayar, TTD
                                                'Ta\'aruf' => 2, // Bayar, TTD
                                                'Haflah' => 2, // Bayar, TTD
                                                default => 2
                                            };
                                        @endphp
                                        <th class="border border-black px-1 py-1 text-center font-bold" colspan="{{ $spanCount }}">
                                            {{ strtoupper($kategori->nama) }}
                                        </th>
                                    @endforeach
                                </tr>
                                <tr class="bg-purple-200">
                                    @foreach($kategoriTagihan as $kategori)
                                        @if($kategori->nama == 'Imtihan')
                                            <th class="border border-black px-1 py-1 text-center text-xs">Bayar</th>
                                            <th class="border border-black px-1 py-1 text-center text-xs">TTD</th>
                                            <th class="border border-black px-1 py-1 text-center text-xs">Bayar</th>
                                            <th class="border border-black px-1 py-1 text-center text-xs">TTD</th>
                                        @else
                                            <th class="border border-black px-1 py-1 text-center text-xs">Bayar</th>
                                            <th class="border border-black px-1 py-1 text-center text-xs">TTD</th>
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $bulanNames = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 
                                                   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
                                    $bulanNumbers = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];
                                @endphp
                                @foreach($bulanNames as $index => $bulanName)
                                    @php $bulanNumber = $bulanNumbers[$index]; @endphp
                                    <tr>
                                        <td class="border border-black px-2 py-1 font-bold bg-purple-100">{{ strtoupper($bulanName) }}</td>
                                        @foreach($kategoriTagihan as $kategori)
                                            @php
                                                $sudahBayar = isset($pembayaranData[$bulanNumber][$kategori->id]['sudah_bayar']) 
                                                              && $pembayaranData[$bulanNumber][$kategori->id]['sudah_bayar'];
                                                $jumlah = $pembayaranData[$bulanNumber][$kategori->id]['jumlah'] ?? '';
                                                $tanggalBayar = $pembayaranData[$bulanNumber][$kategori->id]['tanggal_bayar'] ?? '';
                                            @endphp
                                            
                                            @if($kategori->nama == 'Imtihan')
                                                <!-- Imtihan Awal -->
                                                <td class="border border-black px-1 py-1 text-center text-xs">
                                                    @if($sudahBayar && $bulanNumber == 12) {{-- Desember untuk Imtihan Awal --}}
                                                        <div class="text-xs">{{ number_format($jumlah) }}</div>
                                                        <div class="text-xs">{{ $tanggalBayar ? \Carbon\Carbon::parse($tanggalBayar)->format('d.m') : '' }}</div>
                                                    @endif
                                                </td>
                                                <td class="border border-black px-1 py-1 text-center">
                                                    @if($sudahBayar && $bulanNumber == 12)
                                                        <div class="w-6 h-6 border border-black rounded-full mx-auto bg-blue-200"></div>
                                                    @endif
                                                </td>
                                                <!-- Imtihan Tsani -->
                                                <td class="border border-black px-1 py-1 text-center text-xs">
                                                    @if($sudahBayar && $bulanNumber == 6) {{-- Juni untuk Imtihan Tsani --}}
                                                        <div class="text-xs">{{ number_format($jumlah) }}</div>
                                                        <div class="text-xs">{{ $tanggalBayar ? \Carbon\Carbon::parse($tanggalBayar)->format('d.m') : '' }}</div>
                                                    @endif
                                                </td>
                                                <td class="border border-black px-1 py-1 text-center">
                                                    @if($sudahBayar && $bulanNumber == 6)
                                                        <div class="w-6 h-6 border border-black rounded-full mx-auto bg-blue-200"></div>
                                                    @endif
                                                </td>
                                            @elseif($kategori->nama == 'Registrasi')
                                                <!-- Registrasi hanya dibayar 1 kali (Juli) -->
                                                <td class="border border-black px-1 py-1 text-center text-xs">
                                                    @if($sudahBayar && $bulanNumber == 7)
                                                        <div class="text-xs">{{ number_format($jumlah) }}</div>
                                                        <div class="text-xs">{{ $tanggalBayar ? \Carbon\Carbon::parse($tanggalBayar)->format('d.m') : '' }}</div>
                                                    @endif
                                                </td>
                                                <td class="border border-black px-1 py-1 text-center">
                                                    @if($sudahBayar && $bulanNumber == 7)
                                                        <div class="w-6 h-6 border border-black rounded-full mx-auto bg-blue-200"></div>
                                                    @endif
                                                </td>
                                            @elseif($kategori->nama == 'Syariah')
                                                <!-- Syariah dibayar per bulan -->
                                                <td class="border border-black px-1 py-1 text-center text-xs">
                                                    @if($sudahBayar)
                                                        <div class="text-xs">{{ number_format($jumlah) }}</div>
                                                        <div class="text-xs">{{ $tanggalBayar ? \Carbon\Carbon::parse($tanggalBayar)->format('d.m') : '' }}</div>
                                                    @endif
                                                </td>
                                                <td class="border border-black px-1 py-1 text-center">
                                                    @if($sudahBayar)
                                                        <div class="w-6 h-6 border border-black rounded-full mx-auto bg-blue-200"></div>
                                                    @endif
                                                </td>
                                            @else
                                                <!-- Lainnya dibayar 1 kali per tahun ajaran (Juli) -->
                                                <td class="border border-black px-1 py-1 text-center text-xs">
                                                    @if($sudahBayar && $bulanNumber == 7)
                                                        <div class="text-xs">{{ number_format($jumlah) }}</div>
                                                        <div class="text-xs">{{ $tanggalBayar ? \Carbon\Carbon::parse($tanggalBayar)->format('d.m') : '' }}</div>
                                                    @endif
                                                </td>
                                                <td class="border border-black px-1 py-1 text-center">
                                                    @if($sudahBayar && $bulanNumber == 7)
                                                        <div class="w-6 h-6 border border-black rounded-full mx-auto bg-blue-200"></div>
                                                    @endif
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer -->
                    <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="border-b border-black pb-2 mb-2">
                                <strong>Catatan:</strong> Setiap Membayar, Kartu ini HARUS Dibawa
                            </p>
                        </div>
                        <div class="text-right">
                            <p>Sekampung, ......... {{ date('Y') }}</p>
                            <p class="mt-8"><strong>Bendahara,</strong></p>
                            <br><br>
                            <p><strong>SITI MUTHOHAROH</strong></p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-between items-center mt-8">
                        <a href="{{ route('santri.dashboard') }}"
                            class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded transition">
                            Kembali ke Dashboard
                        </a>
                        <div class="space-x-3">
                            <button onclick="window.print()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded transition">
                                <i class="fa fa-print mr-2"></i>Cetak Kartu
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <!-- Belum Ada Kartu -->
                <div class="bg-white rounded-lg shadow p-12">
                    <div class="text-center">
                        <i class="fa fa-credit-card text-6xl text-gray-400 mb-6"></i>
                        <h3 class="text-xl font-semibold text-gray-800 mb-3">Kartu Pembayaran Belum Tersedia</h3>
                        <p class="text-gray-600 mb-6 max-w-md mx-auto">
                            Kartu pembayaran untuk tahun ajaran {{ $tahunAjaran->nama }} belum dibuat. 
                            Silakan hubungi admin atau bendahara untuk pembuatan kartu pembayaran.
                        </p>
                        <div class="mt-8">
                            <a href="{{ route('santri.dashboard') }}"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                                Kembali ke Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>

    @push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .print-section, .print-section * {
                visibility: visible;
            }
            .print-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
    @endpush
@endsection