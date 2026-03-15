<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Kartu Pembayaran</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
        }

        table {
            border-collapse: collapse;
        }

        .thbg {
            background-color: #d0d0d0;
        }

        .subbg {
            background-color: #e8e8e8;
        }

        .graybg {
            background-color: #b8b8b8;
        }

        .bold {
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .vmid {
            vertical-align: middle;
        }

        .vtop {
            vertical-align: top;
        }

        .check {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid #2a7a2a;
            background-color: #d4f1d4;
            color: #2a7a2a;
            font-weight: bold;
            font-size: 11px;
            text-align: center;
            line-height: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        .uncheck {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 1px solid #aaaaaa;
            background-color: #f5f5f5;
            margin-left: auto;
            margin-right: auto;
        }

        .pay-cell {
            height: 36px;
            padding: 0;
            overflow: hidden;
        }

        .pay-content {
            height: 36px;
            line-height: 1.15;
            overflow: hidden;
            text-align: center;
            white-space: nowrap;
            padding-top: 3px;
    </style>
</head>

<body>
    @php
        $nonSyariahPembayaran = $nonSyariahPembayaran ?? [];
        $syariahPembayaran = $syariahPembayaran ?? [];

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

        // Embed logo as base64 so DomPDF can always load it regardless of OS path format
        $logoPath = public_path('logo/logo.png');
        $logoSrc = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
    @endphp

    {{-- ===== HEADER ===== --}}
    <table style="width:100%; border:2px solid #000;">
        <tr>
            <td style="width:80px; padding:6px 10px; vertical-align:top;">
                @if ($logoSrc)
                    <img src="{{ $logoSrc }}" alt="Logo" style="width:70px; height:70px; object-fit:contain;">
                @endif
            </td>
            <td style="text-align:center; padding:6px 8px; padding-top:12px; vertical-align:top;">
                <div style="font-weight:bold; font-size:13px;">KARTU PEMBAYARAN ADMINISTRASI KEUANGAN</div>
                <div style="font-weight:bold; font-size:12px;">PONDOK PESANTREN "DARUL ULUM"</div>
                <div style="font-size:11px;">SUMBERGEDE SEKAMPUNG LAMPUNG TIMUR</div>
                <div style="font-weight:bold; font-size:11px;">TAHUN PELAJARAN {{ strtoupper($tahunAjaran->nama) }}
                </div>
            </td>
            <td style="width:200px; text-align:center; padding:6px 8px; vertical-align:top;">
                @if ($kelasLabel)
                    <div
                        style="border:1px solid #000; font-weight:bold; font-size:13px; padding:2px 6px; margin-bottom:3px;">
                        {{ $kelasLabel }}
                    </div>
                @endif
                <div style="border:1px solid #000; padding:3px 5px; font-size:10px; line-height:1.5;">
                    <div style="font-size:9px; color:#555;">Nomor Urut Kartu</div>
                    <div style="font-weight:bold; font-size:14px;">{{ $kartuPembayaran->nomor_kartu ?? '-' }}</div>
                </div>
            </td>
        </tr>
    </table>

    {{-- ===== STUDENT INFO ===== --}}
    <table style="width:100%; border:2px solid #000; border-top:0;">
        <tr>
            <td style="width:50%; padding:4px 10px; font-size:11px; border-right:1px solid #000;">
                <span class="bold">NAMA SANTRI</span> :
                <span style="border-bottom:1px dotted #000;">{{ strtoupper($user->nama_santri) }}</span>
            </td>
            <td style="width:50%; padding:4px 10px; font-size:11px;">
                <span class="bold">NAMA ORANG TUA</span> :
                {{ strtoupper($user->nama_orang_tua ?? '...................................') }}
            </td>
        </tr>
        <tr>
            <td style="padding:2px 10px 4px; font-size:11px; border-right:1px solid #000;">
                <span class="bold">KELAS/TINGKAT</span> : {{ $user->kelas ?? '.....' }}&nbsp;(MI/SMP/MTs/MA/PT)
            </td>
            <td style="padding:2px 10px 4px; font-size:11px;">
                <span class="bold">No. HP</span> : {{ $user->no_telp ?? '...................................' }}
            </td>
        </tr>
    </table>

    {{-- ===== MAIN 3-COLUMN TABLE ===== --}}
    <table style="width:100%; border:2px solid #000; border-top:0; table-layout:fixed;">
        <colgroup>
            <col style="width:38%">
            <col style="width:31%">
            <col style="width:31%">
        </colgroup>
        <tr class="vtop">

            {{-- LEFT: Syahriyah --}}
            <td class="vtop" style="border-right:2px solid #000; padding:0;">
                <table style="width:100%;">
                    <thead>
                        <tr>
                            <th colspan="3" class="thbg bold center"
                                style="font-size:11px; padding:3px 4px; border-bottom:1px solid #000;">
                                SYAHRIYAH</th>
                        </tr>
                        <tr>
                            <th class="thbg bold center"
                                style="font-size:10px; padding:2px 4px; border:1px solid #000; width:28%;">BULAN</th>
                            <th class="thbg bold center"
                                style="font-size:10px; padding:2px 4px; border:1px solid #000; width:42%;">Bayar</th>
                            <th class="thbg bold center"
                                style="font-size:10px; padding:2px 4px; border:1px solid #000; width:30%;">TTD</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($bulanList as $bulan)
                            @php $s = $syariahPembayaran[$bulan] ?? null; @endphp
                            <tr>
                                <td class="bold vmid"
                                    style="font-size:10px; padding:2px 4px; border:1px solid #000; white-space:nowrap;">
                                    {{ strtoupper($bulan) }}</td>
                                <td class="center vmid pay-cell" style="font-size:10px; border:1px solid #000;">
                                    @if ($s && $s['lunas'])
                                        <div class="pay-content">
                                            <span class="bold" style="display:block;">Rp.
                                                {{ number_format($s['nominal'], 0, ',', '.') }}</span>
                                            <span style="font-size:9px; color:#444; display:block;">Tgl.
                                                {{ $s['tanggal'] ?? '-' }}</span>
                                        </div>
                                    @else
                                        <div class="pay-content">
                                            <span style="color:#bbb; display:block;">Rp.</span>
                                            <span style="font-size:9px; color:#bbb; display:block;">Tgl.</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="center vmid" style="border:1px solid #000; padding:3px; width:50px;">
                                    @if ($s && $s['lunas'])
                                        <div class="check">&#10003;</div>
                                    @else
                                        <div class="uncheck"></div>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>

            {{-- MIDDLE --}}
            <td class="vtop" style="border-right:2px solid #000; padding:0;">
                <table style="width:100%;">
                    @foreach ($middleSections as $sec)
                        @php $d = $cari($sec['keys']); @endphp
                        <tr>
                            <td colspan="2" class="thbg bold center"
                                style="font-size:10px; padding:3px 4px; border:1px solid #000;">
                                {{ $sec['label'] }}</td>
                        </tr>
                        <tr>
                            <td class="subbg bold center"
                                style="font-size:10px; padding:2px 4px; border:1px solid #000;">Bayar</td>
                            <td class="subbg bold center"
                                style="font-size:10px; padding:2px 4px; border:1px solid #000;">TTD</td>
                        </tr>
                        <tr>
                            <td class="center vmid pay-cell" style="font-size:10px; border:1px solid #000;">
                                @if ($d && $d['lunas'])
                                    <div class="pay-content">
                                        <span class="bold" style="display:block;">Rp.
                                            {{ number_format($d['nominal'], 0, ',', '.') }}</span>
                                        <span style="font-size:9px; color:#444; display:block;">Tgl.
                                            {{ $d['tanggal'] ?? '-' }}</span>
                                    </div>
                                @else
                                    <div class="pay-content">
                                        <span style="color:#bbb; display:block;">Rp.</span>
                                        <span style="font-size:9px; color:#bbb; display:block;">Tgl.</span>
                                    </div>
                                @endif
                            </td>
                            <td class="center vmid" style="border:1px solid #000; padding:3px;">
                                @if ($d && $d['lunas'])
                                    <div class="check">&#10003;</div>
                                @else
                                    <div class="uncheck"></div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="graybg" style="border:1px solid #000; height:14px;"></td>
                        </tr>
                    @endforeach
                </table>
            </td>

            {{-- RIGHT --}}
            <td class="vtop" style="padding:0;">
                <table style="width:100%;">
                    @foreach ($rightSections as $idx => $sec)
                        @php $d = $cari($sec['keys']); @endphp
                        <tr>
                            <td colspan="2" class="thbg bold center"
                                style="font-size:10px; padding:3px 4px; border:1px solid #000;">
                                {{ $sec['label'] }}</td>
                        </tr>
                        <tr>
                            <td class="subbg bold center"
                                style="font-size:10px; padding:2px 4px; border:1px solid #000;">Bayar</td>
                            <td class="subbg bold center"
                                style="font-size:10px; padding:2px 4px; border:1px solid #000;">TTD</td>
                        </tr>
                        <tr>
                            <td class="center vmid pay-cell" style="font-size:10px; border:1px solid #000;">
                                @if ($d && $d['lunas'])
                                    <div class="pay-content">
                                        <span class="bold" style="display:block;">Rp.
                                            {{ number_format($d['nominal'], 0, ',', '.') }}</span>
                                        <span style="font-size:9px; color:#444; display:block;">Tgl.
                                            {{ $d['tanggal'] ?? '-' }}</span>
                                    </div>
                                @else
                                    <div class="pay-content">
                                        <span style="color:#bbb; display:block;">Rp.</span>
                                        <span style="font-size:9px; color:#bbb; display:block;">Tgl.</span>
                                    </div>
                                @endif
                            </td>
                            <td class="center vmid" style="border:1px solid #000; padding:3px;">
                                @if ($d && $d['lunas'])
                                    <div class="check">&#10003;</div>
                                @else
                                    <div class="uncheck"></div>
                                @endif
                            </td>
                        </tr>
                        @if ($idx < count($rightSections) - 1)
                            <tr>
                                <td colspan="2" class="graybg" style="border:1px solid #000; height:14px;"></td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </td>

        </tr>
    </table>

    {{-- ===== FOOTER ===== --}}
    <table style="width:100%; border:2px solid #000; border-top:0;">
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

</body>

</html>
