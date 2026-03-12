{{-- filepath: resources/views/santri/kartu-pembayaran.blade.php --}}
@extends('layouts.santri')

@push('styles')
    <style>
        @media print {
            body * {
                visibility: hidden !important;
            }

            #print-area,
            #print-area * {
                visibility: visible !important;
            }

            #print-area {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')

    {{-- ─── Top bar (no print) ─── --}}
    <div class="no-print space-y-4">
        <div class="bg-green-700 text-white p-4 rounded-lg flex justify-between items-center">
            <div>
                <h1 class="text-xl font-bold">Kartu Pembayaran</h1>
                <p class="text-green-100 mt-1">Laporan kartu pembayaran administrasi keuangan</p>
            </div>
            <button onclick="window.print()"
                class="bg-white text-green-700 font-semibold px-5 py-2 rounded-lg hover:bg-green-50 transition flex items-center gap-2">
                <i class="fa fa-print"></i> Cetak Kartu
            </button>
        </div>

        @if (isset($error))
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <i class="fa fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-red-800 mb-2">{{ $error }}</h3>
                <a href="{{ route('santri.dashboard') }}"
                    class="mt-4 inline-block bg-green-600 text-white px-5 py-2 rounded-lg">Kembali</a>
            </div>
        @else
            @if ($allTahunAjaran->count() > 0)
                <div class="bg-white rounded-lg shadow p-4 flex items-center gap-4">
                    <span class="font-semibold text-gray-700">Tahun Pelajaran:</span>
                    <form method="GET" action="{{ route('santri.kartu-pembayaran') }}">
                        <select name="tahun_ajaran_id" onchange="this.form.submit()"
                            class="border border-gray-300 rounded px-3 py-1 focus:ring-2 focus:ring-green-500">
                            @foreach ($allTahunAjaran as $ta)
                                <option value="{{ $ta->id }}"
                                    {{ $tahunAjaran && $tahunAjaran->id == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            @endif
        @endif
    </div>

    @if (!isset($error) && $kartuPembayaran)
        <div id="print-area">
            @php
                $bulanList = [
                    'Juli',
                    'Agustus',
                    'September',
                    'Oktober',
                    'November',
                    'Desember',
                    'Januari',
                    'Februari',
                    'Maret',
                    'April',
                    'Mei',
                    'Juni',
                ];

                // $syariahPembayaran and $nonSyariahPembayaran are passed directly from the controller
                // (built dynamically from verified tagihan records — do NOT read from model properties)

                $middleSections = [
                    ['label' => 'PENDAFTARAN', 'keys' => ['pendaftaran', 'registrasi']],
                    ['label' => 'KTK, SPP, RAPORT', 'keys' => ['ktk', 'spp', 'raport', 'raprot']],
                    ['label' => 'DPP PESANTREN', 'keys' => ['dpp', 'pesantren']],
                    ['label' => 'FASILITAS KAMAR', 'keys' => ['fasilitas', 'kamar']],
                    ['label' => "TA'ARUF", 'keys' => ['taaruf', "ta'aruf", 'taruf', 'aruf', 'ta,aruf']],
                ];

                $rightSections = [
                    ['label' => 'INFAQ', 'keys' => ['infaq', 'infak']],
                    ['label' => 'SERAGAM', 'keys' => ['seragam']],
                    ['label' => 'RAMADHAN', 'keys' => ['ramadhan', 'ramadan']],
                    ['label' => 'IMTIHAN AWAL', 'keys' => ['imtihan awal']],
                    ['label' => 'IMTIHAN TSANI', 'keys' => ['imtihan tsani', 'tsani']],
                    ['label' => 'HAFLAH AKHIR SANAH', 'keys' => ['haflah', 'akhir sanah']],
                ];

                $cari = function (array $keys) use ($nonSyariahPembayaran): ?array {
                    foreach ($nonSyariahPembayaran as $catKey => $data) {
                        foreach ($keys as $k) {
                            if (str_contains(strtolower($catKey), strtolower($k))) {
                                return $data;
                            }
                        }
                    }
                    return null;
                };

                $kelasLabel = $user->tingkatan ? strtoupper($user->tingkatan) : '';
                if ($user->kelas) {
                    $kelasLabel = $user->kelas . ($kelasLabel ? '-' . $kelasLabel : '');
                }

                // CSS shorthand variables (inline style strings)
                $border = 'border: 1px solid #000;';
                $thBg = 'background-color: #d0d0d0;';
                $subBg = 'background-color: #e8e8e8;';
                $grayBg = 'background-color: #b8b8b8;';
                $center = 'text-align: center;';
                $bold = 'font-weight: bold;';
                $fnt10 = 'font-size: 10px;';
                $fnt11 = 'font-size: 11px;';
                $pad2 = 'padding: 2px 4px;';
                $pad3 = 'padding: 3px 4px;';
                $vmid = 'vertical-align: middle;';
                $vtop = 'vertical-align: top;';
            @endphp

            {{-- ╔═══════════════════════════════════════════════════════════════╗
     ║               KARTU PEMBAYARAN — outer wrapper               ║
     ╚═══════════════════════════════════════════════════════════════╝ --}}
            <div
                style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; line-height: 1.3;
            max-width: 960px; margin: 16px auto 0; border: 2px solid #000; background: #fff; color: #000;">

                {{-- ═══ HEADER ═══ --}}
                <table style="width:100%; border-collapse:collapse; border-bottom: 2px solid #000;">
                    <tr>
                        {{-- Logo --}}
                        <td style="width:70px; padding:6px 10px; {{ $vtop }}">
                            <img src="/logo/logo.png" alt="Logo"
                                style="height:60px; width:60px; object-fit:contain;"
                                onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                            <div
                                style="display:none; height:60px; width:60px; border:1px solid #000;
                            text-align:center; font-size:9px; line-height:60px;">
                                LOGO</div>
                        </td>
                        {{-- Title --}}
                        <td style="text-align:center; padding:6px 8px; {{ $vtop }}">
                            <div style="font-weight:bold; font-size:13px;">KARTU PEMBAYARAN ADMINISTRASI KEUANGAN</div>
                            <div style="font-weight:bold; font-size:12px;">PONDOK PESANTREN "DARUL ULUM"</div>
                            <div style="font-size:11px;">SUMBERGEDE SEKAMPUNG LAMPUNG TIMUR</div>
                            <div style="font-weight:bold; font-size:11px;">TAHUN PELAJARAN
                                {{ strtoupper($tahunAjaran->nama) }}</div>
                        </td>
                        {{-- Kelas + Nomor Urut --}}
                        <td style="width:90px; text-align:center; padding:6px 8px; {{ $vtop }}">
                            @if ($kelasLabel)
                                <div
                                    style="border:1px solid #000; font-weight:bold; font-size:13px; padding:2px 6px; margin-bottom:3px;">
                                    {{ $kelasLabel }}
                                </div>
                            @endif
                            <div style="border:1px solid #000; padding:3px 5px; font-size:10px; line-height:1.5;">
                                <div style="font-size:9px; color:#555;">Nomor Urut</div>
                                <div style="font-weight:bold; font-size:14px;">{{ $kartuPembayaran->nomor_kartu ?? '-' }}
                                </div>
                                <div style="font-size:9px; color:#555;">Kartu</div>
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- ═══ INFO SANTRI ═══ --}}
                <table style="width:100%; border-collapse:collapse; border-bottom:2px solid #000;">
                    <tr>
                        <td style="width:50%; padding:4px 10px; font-size:11px;">
                            <span style="font-weight:bold;">NAMA SANTRI</span> :
                            <span style="border-bottom:1px dotted #000;">{{ strtoupper($user->nama_santri) }}</span>
                        </td>
                        <td style="width:50%; padding:4px 10px; font-size:11px;">
                            <span style="font-weight:bold;">NAMA ORANG TUA</span> :
                            {{ strtoupper($user->nama_orang_tua ?? '...................................') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:2px 10px 4px; font-size:11px;">
                            <span style="font-weight:bold;">KELAS/TINGKAT</span> :
                            {{ $user->kelas ?? '.....' }}&nbsp;(MI/SMP/MTs/MA/PT)
                        </td>
                        <td style="padding:2px 10px 4px; font-size:11px;">
                            <span style="font-weight:bold;">No. HP</span> :
                            {{ $user->no_telp ?? '...................................' }}
                        </td>
                    </tr>
                </table>

                {{-- ═══ 3-COLUMN PAYMENT BODY ═══ --}}
                <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
                    <colgroup>
                        <col style="width:38%">
                        <col style="width:31%">
                        <col style="width:31%">
                    </colgroup>
                    <tr style="vertical-align:top;">

                        {{-- ─── LEFT: SYAHRIYAH ─── --}}
                        <td style="{{ $vtop }} border-right:2px solid #000; padding:0;">
                            <table style="width:100%; border-collapse:collapse;">
                                <thead>
                                    <tr>
                                        <th colspan="3"
                                            style="{{ $thBg }}{{ $bold }}{{ $fnt11 }}{{ $center }}{{ $pad3 }} border-bottom:1px solid #000;">
                                            SYAHRIYAH
                                        </th>
                                    </tr>
                                    <tr>
                                        <th
                                            style="{{ $thBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000; width:28%;">
                                            BULAN</th>
                                        <th
                                            style="{{ $thBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000; width:42%;">
                                            Bayar</th>
                                        <th
                                            style="{{ $thBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000; width:30%;">
                                            TTD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bulanList as $bulan)
                                        @php $s = $syariahPembayaran[$bulan] ?? null; @endphp
                                        <tr>
                                            <td
                                                style="{{ $bold }}{{ $fnt10 }}{{ $pad2 }} border:1px solid #000; white-space:nowrap; {{ $vmid }}">
                                                {{ strtoupper($bulan) }}
                                            </td>
                                            <td
                                                style="{{ $fnt10 }}{{ $center }} border:1px solid #000; padding:3px; {{ $vmid }} height:36px;">
                                                @if ($s && $s['lunas'])
                                                    <span style="{{ $bold }}">Rp.
                                                        {{ number_format($s['nominal'], 0, ',', '.') }}</span><br>
                                                    <span style="font-size:9px; color:#444;">Tgl.
                                                        {{ $s['tanggal'] ?? '-' }}</span>
                                                @else
                                                    <span style="color:#bbb;">Rp.</span><br>
                                                    <span style="font-size:9px; color:#bbb;">Tgl.</span>
                                                @endif
                                            </td>
                                            <td
                                                style="border:1px solid #000; {{ $center }}{{ $vmid }} padding:3px; width:50px;">
                                                @if ($s && $s['lunas'])
                                                    <div
                                                        style="width:22px; height:22px; border-radius:50%; border:1px solid #2a7a2a;
                                                background:#d4f1d4; color:#2a7a2a; font-weight:bold; font-size:12px;
                                                display:flex; align-items:center; justify-content:center; margin:auto;">
                                                        ✓</div>
                                                @else
                                                    <div
                                                        style="width:22px; height:22px; border-radius:50%; border:1px solid #aaa;
                                                background:#f5f5f5; margin:auto;">
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </td>

                        {{-- ─── MIDDLE: 5 sections ─── --}}
                        <td style="{{ $vtop }} border-right:2px solid #000; padding:0;">
                            <table style="width:100%; border-collapse:collapse; height:100%;">
                                @foreach ($middleSections as $idx => $sec)
                                    @php $d = $cari($sec['keys']); @endphp
                                    {{-- Section header --}}
                                    <tr>
                                        <td colspan="2"
                                            style="{{ $thBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad3 }} border:1px solid #000;">
                                            {{ $sec['label'] }}
                                        </td>
                                    </tr>
                                    {{-- Sub-header --}}
                                    <tr>
                                        <td
                                            style="{{ $subBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000;">
                                            Bayar</td>
                                        <td
                                            style="{{ $subBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000;">
                                            TTD</td>
                                    </tr>
                                    {{-- Data row --}}
                                    <tr>
                                        <td
                                            style="{{ $fnt10 }}{{ $center }} border:1px solid #000; padding:3px; {{ $vmid }} height:36px;">
                                            @if ($d && $d['lunas'])
                                                <span style="{{ $bold }}">Rp.
                                                    {{ number_format($d['nominal'], 0, ',', '.') }}</span><br>
                                                <span style="font-size:9px; color:#444;">Tgl.
                                                    {{ $d['tanggal'] ?? '-' }}</span>
                                            @else
                                                <span style="color:#bbb;">Rp.</span><br>
                                                <span style="font-size:9px; color:#bbb;">Tgl.</span>
                                            @endif
                                        </td>
                                        <td
                                            style="border:1px solid #000; {{ $center }}{{ $vmid }} padding:3px;">
                                            @if ($d && $d['lunas'])
                                                <div
                                                    style="width:22px; height:22px; border-radius:50%; border:1px solid #2a7a2a;
                                            background:#d4f1d4; color:#2a7a2a; font-weight:bold; font-size:12px;
                                            display:flex; align-items:center; justify-content:center; margin:auto;">
                                                    ✓</div>
                                            @else
                                                <div
                                                    style="width:22px; height:22px; border-radius:50%; border:1px solid #aaa;
                                            background:#f5f5f5; margin:auto;">
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    {{-- Gray fill --}}
                                    <tr>
                                        <td colspan="2" style="{{ $grayBg }} border:1px solid #000; height:14px;">
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>

                        {{-- ─── RIGHT: 6 sections ─── --}}
                        <td style="{{ $vtop }} padding:0;">
                            <table style="width:100%; border-collapse:collapse; height:100%;">
                                @foreach ($rightSections as $idx => $sec)
                                    @php $d = $cari($sec['keys']); @endphp
                                    {{-- Section header --}}
                                    <tr>
                                        <td colspan="2"
                                            style="{{ $thBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad3 }} border:1px solid #000;">
                                            {{ $sec['label'] }}
                                        </td>
                                    </tr>
                                    {{-- Sub-header --}}
                                    <tr>
                                        <td
                                            style="{{ $subBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000;">
                                            Bayar</td>
                                        <td
                                            style="{{ $subBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000;">
                                            TTD</td>
                                    </tr>
                                    {{-- Data row --}}
                                    <tr>
                                        <td
                                            style="{{ $fnt10 }}{{ $center }} border:1px solid #000; padding:3px; {{ $vmid }} height:36px;">
                                            @if ($d && $d['lunas'])
                                                <span style="{{ $bold }}">Rp.
                                                    {{ number_format($d['nominal'], 0, ',', '.') }}</span><br>
                                                <span style="font-size:9px; color:#444;">Tgl.
                                                    {{ $d['tanggal'] ?? '-' }}</span>
                                            @else
                                                <span style="color:#bbb;">Rp.</span><br>
                                                <span style="font-size:9px; color:#bbb;">Tgl.</span>
                                            @endif
                                        </td>
                                        <td
                                            style="border:1px solid #000; {{ $center }}{{ $vmid }} padding:3px;">
                                            @if ($d && $d['lunas'])
                                                <div
                                                    style="width:22px; height:22px; border-radius:50%; border:1px solid #2a7a2a;
                                            background:#d4f1d4; color:#2a7a2a; font-weight:bold; font-size:12px;
                                            display:flex; align-items:center; justify-content:center; margin:auto;">
                                                    ✓</div>
                                            @else
                                                <div
                                                    style="width:22px; height:22px; border-radius:50%; border:1px solid #aaa;
                                            background:#f5f5f5; margin:auto;">
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                    {{-- Gray fill (between sections, not after last) --}}
                                    @if ($idx < count($rightSections) - 1)
                                        <tr>
                                            <td colspan="2"
                                                style="{{ $grayBg }} border:1px solid #000; height:14px;"></td>
                                        </tr>
                                    @endif
                                @endforeach
                            </table>
                        </td>

                    </tr>
                </table>{{-- end 3-column --}}

                {{-- ═══ FOOTER ═══ --}}
                <table style="width:100%; border-collapse:collapse; border-top:2px solid #000;">
                    <tr>
                        <td style="padding:6px 10px; font-size:10px; vertical-align:bottom;">
                            <strong>Catatan ; </strong>Setiap Membayar, Kartu ini HARUS Dibawa
                        </td>
                        <td style="padding:6px 10px; font-size:10px; text-align:right; vertical-align:top;">
                            Sekampung, ......... {{ \Carbon\Carbon::now()->format('Y') }}<br>
                            <strong>Bendahara,</strong><br><br><br>
                            <strong>SITI MUTHOHAROH</strong>
                        </td>
                    </tr>
                </table>

            </div>{{-- end kartu-outer --}}

            <div class="no-print" style="display:flex; justify-content:space-between; margin-top:16px;">
                <a href="{{ route('santri.dashboard') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-5 py-2 rounded transition">
                    ← Dashboard
                </a>
                <button onclick="window.print()"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded transition">
                    <i class="fa fa-print mr-2"></i>Cetak Kartu
                </button>
            </div>

        </div>{{-- end print-area --}}
    @elseif(!isset($error))
        <div class="bg-white rounded-lg shadow p-12 text-center mt-4">
            <i class="fa fa-credit-card text-6xl text-gray-400 mb-6"></i>
            <h3 class="text-xl font-semibold text-gray-800 mb-3">Kartu Pembayaran Belum Tersedia</h3>
            <p class="text-gray-600 max-w-md mx-auto">
                Kartu pembayaran untuk tahun ajaran {{ $tahunAjaran?->nama }} belum dibuat. Silakan hubungi admin.
            </p>
            <a href="{{ route('santri.dashboard') }}"
                class="mt-6 inline-block bg-green-600 text-white font-semibold px-6 py-3 rounded-lg">
                Kembali ke Dashboard
            </a>
        </div>
    @endif
@endsection
