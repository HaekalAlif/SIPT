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
    $payCellWrap = 'height:36px; overflow:hidden; line-height:1.15; padding-top:3px;';
    $nowrap = 'display:block; white-space:nowrap;';
@endphp

<div id="print-area">
    <div
        style="font-family: Arial, Helvetica, sans-serif; font-size: 11px; line-height: 1.3;
        max-width: 960px; margin: 16px auto 0; border: 2px solid #000; background: #fff; color: #000;">

        <table style="width:100%; border-collapse:collapse; border-bottom: 2px solid #000;">
            <tr>
                <td style="width:80px; padding:6px 10px; {{ $vtop }}">
                    <img src="{{ asset('logo/logo.png') }}" alt="Logo" style="width:80px; object-fit:contain;">
                </td>
                <td style="text-align:center; padding:6px 8px; padding-top:12px; {{ $vtop }}">
                    <div style="font-weight:bold; font-size:13px;">KARTU PEMBAYARAN ADMINISTRASI KEUANGAN</div>
                    <div style="font-weight:bold; font-size:12px;">PONDOK PESANTREN "DARUL ULUM"</div>
                    <div style="font-size:11px;">SUMBERGEDE SEKAMPUNG LAMPUNG TIMUR</div>
                    <div style="font-weight:bold; font-size:11px;">TAHUN PELAJARAN {{ strtoupper($tahunAjaran->nama) }}
                    </div>
                </td>
                <td style="width:200px; text-align:center; padding:6px 8px; {{ $vtop }}">
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

        <table style="width:100%; border-collapse:collapse; table-layout:fixed;">
            <colgroup>
                <col style="width:38%">
                <col style="width:31%">
                <col style="width:31%">
            </colgroup>
            <tr style="vertical-align:top;">
                <td style="{{ $vtop }} border-right:2px solid #000; padding:0;">
                    <table style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th colspan="3"
                                    style="{{ $thBg }}{{ $bold }}{{ $fnt11 }}{{ $center }}{{ $pad3 }} border-bottom:1px solid #000;">
                                    SYAHRIYAH</th>
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
                                        {{ strtoupper($bulan) }}</td>
                                    <td
                                        style="{{ $fnt10 }}{{ $center }} border:1px solid #000; padding:0; {{ $vmid }} height:36px;">
                                        @if ($s && $s['lunas'])
                                            <div style="{{ $payCellWrap }}">
                                                <span style="{{ $bold }}{{ $nowrap }}">Rp.
                                                    {{ number_format($s['nominal'], 0, ',', '.') }}</span>
                                                <span style="font-size:9px; color:#444; {{ $nowrap }}">Tgl.
                                                    {{ $s['tanggal'] ?? '-' }}</span>
                                            </div>
                                        @else
                                            <div style="{{ $payCellWrap }}">
                                                <span style="color:#bbb; {{ $nowrap }}">Rp.</span>
                                                <span
                                                    style="font-size:9px; color:#bbb; {{ $nowrap }}">Tgl.</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td
                                        style="border:1px solid #000; {{ $center }}{{ $vmid }} padding:3px; width:50px;">
                                        @if ($s && $s['lunas'])
                                            <div
                                                style="width:22px; height:22px; border-radius:50%; border:1px solid #2a7a2a; background:#d4f1d4; color:#2a7a2a; font-weight:bold; font-size:12px; display:flex; align-items:center; justify-content:center; margin:auto;">
                                                ✓</div>
                                        @else
                                            <div
                                                style="width:22px; height:22px; border-radius:50%; border:1px solid #aaa; background:#f5f5f5; margin:auto;">
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>

                <td style="{{ $vtop }} border-right:2px solid #000; padding:0;">
                    <table style="width:100%; border-collapse:collapse; height:100%;">
                        @foreach ($middleSections as $sec)
                            @php $d = $cari($sec['keys']); @endphp
                            <tr>
                                <td colspan="2"
                                    style="{{ $thBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad3 }} border:1px solid #000;">
                                    {{ $sec['label'] }}</td>
                            </tr>
                            <tr>
                                <td
                                    style="{{ $subBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000;">
                                    Bayar</td>
                                <td
                                    style="{{ $subBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000;">
                                    TTD</td>
                            </tr>
                            <tr>
                                <td
                                    style="{{ $fnt10 }}{{ $center }} border:1px solid #000; padding:0; {{ $vmid }} height:36px;">
                                    @if ($d && $d['lunas'])
                                        <div style="{{ $payCellWrap }}">
                                            <span style="{{ $bold }}{{ $nowrap }}">Rp.
                                                {{ number_format($d['nominal'], 0, ',', '.') }}</span>
                                            <span style="font-size:9px; color:#444; {{ $nowrap }}">Tgl.
                                                {{ $d['tanggal'] ?? '-' }}</span>
                                        </div>
                                    @else
                                        <div style="{{ $payCellWrap }}">
                                            <span style="color:#bbb; {{ $nowrap }}">Rp.</span>
                                            <span style="font-size:9px; color:#bbb; {{ $nowrap }}">Tgl.</span>
                                        </div>
                                    @endif
                                </td>
                                <td
                                    style="border:1px solid #000; {{ $center }}{{ $vmid }} padding:3px;">
                                    @if ($d && $d['lunas'])
                                        <div
                                            style="width:22px; height:22px; border-radius:50%; border:1px solid #2a7a2a; background:#d4f1d4; color:#2a7a2a; font-weight:bold; font-size:12px; display:flex; align-items:center; justify-content:center; margin:auto;">
                                            ✓</div>
                                    @else
                                        <div
                                            style="width:22px; height:22px; border-radius:50%; border:1px solid #aaa; background:#f5f5f5; margin:auto;">
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="{{ $grayBg }} border:1px solid #000; height:14px;">
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </td>

                <td style="{{ $vtop }} padding:0;">
                    <table style="width:100%; border-collapse:collapse; height:100%;">
                        @foreach ($rightSections as $idx => $sec)
                            @php $d = $cari($sec['keys']); @endphp
                            <tr>
                                <td colspan="2"
                                    style="{{ $thBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad3 }} border:1px solid #000;">
                                    {{ $sec['label'] }}</td>
                            </tr>
                            <tr>
                                <td
                                    style="{{ $subBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000;">
                                    Bayar</td>
                                <td
                                    style="{{ $subBg }}{{ $bold }}{{ $fnt10 }}{{ $center }}{{ $pad2 }} border:1px solid #000;">
                                    TTD</td>
                            </tr>
                            <tr>
                                <td
                                    style="{{ $fnt10 }}{{ $center }} border:1px solid #000; padding:0; {{ $vmid }} height:36px;">
                                    @if ($d && $d['lunas'])
                                        <div style="{{ $payCellWrap }}">
                                            <span style="{{ $bold }}{{ $nowrap }}">Rp.
                                                {{ number_format($d['nominal'], 0, ',', '.') }}</span>
                                            <span style="font-size:9px; color:#444; {{ $nowrap }}">Tgl.
                                                {{ $d['tanggal'] ?? '-' }}</span>
                                        </div>
                                    @else
                                        <div style="{{ $payCellWrap }}">
                                            <span style="color:#bbb; {{ $nowrap }}">Rp.</span>
                                            <span style="font-size:9px; color:#bbb; {{ $nowrap }}">Tgl.</span>
                                        </div>
                                    @endif
                                </td>
                                <td
                                    style="border:1px solid #000; {{ $center }}{{ $vmid }} padding:3px;">
                                    @if ($d && $d['lunas'])
                                        <div
                                            style="width:22px; height:22px; border-radius:50%; border:1px solid #2a7a2a; background:#d4f1d4; color:#2a7a2a; font-weight:bold; font-size:12px; display:flex; align-items:center; justify-content:center; margin:auto;">
                                            ✓</div>
                                    @else
                                        <div
                                            style="width:22px; height:22px; border-radius:50%; border:1px solid #aaa; background:#f5f5f5; margin:auto;">
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @if ($idx < count($rightSections) - 1)
                                <tr>
                                    <td colspan="2"
                                        style="{{ $grayBg }} border:1px solid #000; height:14px;">
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </td>
            </tr>
        </table>

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
    </div>
</div>
