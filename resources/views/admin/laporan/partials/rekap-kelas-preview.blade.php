<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
    <table class="min-w-[1900px] w-full border-collapse text-[11px]">
        <thead>
            <tr class="bg-gray-100 text-gray-700">
                <th class="border border-gray-300 px-2 py-2" rowspan="2">No</th>
                <th class="border border-gray-300 px-2 py-2 min-w-[240px]" rowspan="2">Nama</th>
                <th class="border border-gray-300 px-2 py-2" rowspan="2">Pembayaran</th>
                <th class="border border-gray-300 px-2 py-2" colspan="7">SANTRI BARU</th>
                <th class="border border-gray-300 px-2 py-2" rowspan="2">RAMADHAN</th>
                <th class="border border-gray-300 px-2 py-2" colspan="2">IMTIHAN</th>
                <th class="border border-gray-300 px-2 py-2" rowspan="2">HAFLAH AKHIR SANAH</th>
                <th class="border border-gray-300 px-2 py-2" colspan="{{ count($bulanList) }}">SYAHRIYAH PESANTREN</th>
            </tr>
            <tr class="bg-gray-50 text-gray-600">
                <th class="border border-gray-300 px-2 py-1">PENDAFTARAN</th>
                <th class="border border-gray-300 px-2 py-1">KTK, SPP, RAPORT</th>
                <th class="border border-gray-300 px-2 py-1">DPP PESANTREN</th>
                <th class="border border-gray-300 px-2 py-1">FASILITAS KAMAR</th>
                <th class="border border-gray-300 px-2 py-1">INFAQ</th>
                <th class="border border-gray-300 px-2 py-1">SERAGAM</th>
                <th class="border border-gray-300 px-2 py-1">TA'ARUF</th>
                <th class="border border-gray-300 px-2 py-1">AWAL</th>
                <th class="border border-gray-300 px-2 py-1">TSANI</th>
                @foreach ($bulanList as $bulan)
                    <th class="border border-gray-300 px-2 py-1">{{ substr($bulan, 0, 3) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                @php
                    $user = $row['user'];
                    $sectionLabels = [
                        'PENDAFTARAN',
                        'KTK, SPP, RAPORT',
                        'DPP PESANTREN',
                        'FASILITAS KAMAR',
                        'INFAQ',
                        'SERAGAM',
                        "TA'ARUF",
                        'IMTIHAN AWAL',
                        'IMTIHAN TSANI',
                        'RAMADHAN',
                        'HAFLAH AKHIR SANAH',
                    ];
                    $cellData = [];
                    foreach ($sectionLabels as $label) {
                        $cellData[$label] = $row['sections'][$label] ?? ['nominal' => 0, 'tanggal' => null];
                    }
                @endphp
                <tr class="hover:bg-gray-50 align-top">
                    <td class="border border-gray-300 px-2 py-2 text-center">{{ $loop->iteration }}</td>
                    <td class="border border-gray-300 px-2 py-2 min-w-[240px]">
                        <div class="font-semibold">{{ $user->nama_santri }}</div>
                        <div class="text-[10px] text-gray-500">Bp/Ibu: {{ $user->nama_orang_tua ?? '-' }}</div>
                        <div class="text-[10px] text-gray-500">No.Hp: {{ $user->no_telp ?? '-' }}</div>
                    </td>
                    <td class="border border-gray-300 px-2 py-2 min-w-[120px]">
                        <div class="font-semibold">Jumlah (Rp)</div>
                        <div>Rp {{ number_format($row['total_dibayar'], 0, ',', '.') }}</div>
                    </td>

                    @foreach (['PENDAFTARAN', 'KTK, SPP, RAPORT', 'DPP PESANTREN', 'FASILITAS KAMAR', 'INFAQ', 'SERAGAM', "TA'ARUF"] as $label)
                        @php $d = $cellData[$label]; @endphp
                        <td class="border border-gray-300 px-2 py-2 min-w-[95px]">
                            @if (($d['nominal'] ?? 0) > 0)
                                <div>Rp {{ number_format($d['nominal'], 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-500">{{ $d['tanggal'] ?: '-' }}</div>
                            @else
                                <div class="text-gray-300">-</div>
                            @endif
                        </td>
                    @endforeach

                    @php $d = $cellData['RAMADHAN']; @endphp
                    <td class="border border-gray-300 px-2 py-2 min-w-[95px]">
                        @if (($d['nominal'] ?? 0) > 0)
                            <div>Rp {{ number_format($d['nominal'], 0, ',', '.') }}</div>
                            <div class="text-[10px] text-gray-500">{{ $d['tanggal'] ?: '-' }}</div>
                        @else
                            <div class="text-gray-300">-</div>
                        @endif
                    </td>

                    @foreach (['IMTIHAN AWAL', 'IMTIHAN TSANI'] as $label)
                        @php $d = $cellData[$label]; @endphp
                        <td class="border border-gray-300 px-2 py-2 min-w-[95px]">
                            @if (($d['nominal'] ?? 0) > 0)
                                <div>Rp {{ number_format($d['nominal'], 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-500">{{ $d['tanggal'] ?: '-' }}</div>
                            @else
                                <div class="text-gray-300">-</div>
                            @endif
                        </td>
                    @endforeach

                    @php $d = $cellData['HAFLAH AKHIR SANAH']; @endphp
                    <td class="border border-gray-300 px-2 py-2 min-w-[95px]">
                        @if (($d['nominal'] ?? 0) > 0)
                            <div>Rp {{ number_format($d['nominal'], 0, ',', '.') }}</div>
                            <div class="text-[10px] text-gray-500">{{ $d['tanggal'] ?: '-' }}</div>
                        @else
                            <div class="text-gray-300">-</div>
                        @endif
                    </td>

                    @foreach ($bulanList as $bulan)
                        @php $s = $row['syariah'][$bulan] ?? ['nominal' => 0, 'tanggal' => null]; @endphp
                        <td class="border border-gray-300 px-2 py-2 min-w-[82px] text-center">
                            @if (($s['nominal'] ?? 0) > 0)
                                <div>Rp {{ number_format($s['nominal'], 0, ',', '.') }}</div>
                                <div class="text-[10px] text-gray-500">{{ $s['tanggal'] ?: '-' }}</div>
                            @else
                                <div class="text-gray-300">-</div>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 3 + 7 + 1 + 2 + 1 + count($bulanList) }}"
                        class="border border-gray-300 px-4 py-8 text-center text-gray-500">
                        Belum ada data santri untuk filter yang dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
