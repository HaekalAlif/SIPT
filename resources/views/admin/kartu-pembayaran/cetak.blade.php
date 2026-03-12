<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Pembayaran - {{ $kartuPembayaran->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }

        body {
            font-family: 'Times New Roman', Times, serif;
        }
    </style>
</head>

<body class="bg-gray-100 p-8 print:p-0 print:bg-white text-gray-900">

    <div class="max-w-3xl mx-auto bg-white shadow-lg p-8 print:shadow-none print:w-full print:max-w-none">

        <!-- Header -->
        <div class="border-b-2 border-gray-800 pb-4 mb-6 flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <!-- Logo Place Holder -->
                <div
                    class="w-16 h-16 bg-gray-200 flex items-center justify-center rounded-full text-gray-500 font-bold text-xs">
                    LOGO</div>
                <div>
                    <h1 class="text-2xl font-bold uppercase tracking-wide">Pondok Pesantren Al-Hidayah</h1>
                    <p class="text-sm text-gray-600">Jl. Raya Pesantren No. 123, Kota Santri</p>
                    <p class="text-sm text-gray-600">Telp: (021) 1234567 • Email: admin@ponpes-alhidayah.id</p>
                </div>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-bold text-gray-800 uppercase border-2 border-gray-800 px-4 py-1 inline-block">
                    Kartu Pembayaran</h2>
            </div>
        </div>

        <!-- Student Info -->
        <div class="mb-6 grid grid-cols-2 gap-4 text-sm">
            <div>
                <table class="w-full">
                    <tr>
                        <td class="font-bold py-1 w-24">Nama</td>
                        <td class="py-1">: {{ $kartuPembayaran->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold py-1">NISN</td>
                        <td class="py-1">: {{ $kartuPembayaran->user->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold py-1">Kelas</td>
                        <td class="py-1">: {{ $kartuPembayaran->user->kelas ?? '-' }} -
                            {{ $kartuPembayaran->user->tingkatan ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full">
                    <tr>
                        <td class="font-bold py-1 w-32">No. Kartu</td>
                        <td class="py-1">: {{ $kartuPembayaran->nomor_kartu }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold py-1">Tahun Ajaran</td>
                        <td class="py-1">: {{ $kartuPembayaran->tahunAjaran->tahun_ajaran }}
                            ({{ $kartuPembayaran->tahunAjaran->semester }})</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment Table -->
        <div class="mb-8">
            <table class="w-full border-collapse border border-gray-800 text-sm">
                <thead>
                    <tr class="bg-gray-100 text-center">
                        <th class="border border-gray-800 p-2 w-12">No</th>
                        <th class="border border-gray-800 p-2">Jenis Pembayaran / Bulan</th>
                        <th class="border border-gray-800 p-2 w-32">Nominal</th>
                        <th class="border border-gray-800 p-2 w-32">Tanggal Bayar</th>
                        <th class="border border-gray-800 p-2 w-24">Status</th>
                        <th class="border border-gray-800 p-2 w-24">Paraf</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 1; @endphp
                    @foreach ($kartuPembayaran->tagihan as $tagihan)
                        @foreach ($tagihan->tagihanDetails as $detail)
                            <tr>
                                <td class="border border-gray-800 p-2 text-center">{{ $no++ }}</td>
                                <td class="border border-gray-800 p-2">
                                    {{ $detail->jenisTagihan->nama ?? 'Tagihan' }}
                                    @if ($detail->bulan)
                                        <span
                                            class="font-semibold">({{ \Carbon\Carbon::parse($detail->bulan)->translatedFormat('F') }})</span>
                                    @endif
                                    <div class="text-xs text-gray-500 italic mt-1">#Inv-{{ $tagihan->id }}</div>
                                </td>
                                <td class="border border-gray-800 p-2 text-right">
                                    Rp {{ number_format($detail->nominal, 0, ',', '.') }}
                                </td>
                                <td class="border border-gray-800 p-2 text-center">
                                    @if ($tagihan->status == 'lunas')
                                        {{ $tagihan->updated_at->format('d/m/Y') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="border border-gray-800 p-2 text-center font-bold">
                                    @if ($tagihan->status == 'lunas')
                                        <span class="text-green-800">LUNAS</span>
                                    @elseif($tagihan->status == 'menunggu_verifikasi')
                                        <span class="text-yellow-700 text-xs">MENUNGGU</span>
                                    @elseif($tagihan->status == 'belum_bayar')
                                        <span class="text-red-700 text-xs">BELUM</span>
                                    @else
                                        <span class="text-gray-600 text-xs">{{ strtoupper($tagihan->status) }}</span>
                                    @endif
                                </td>
                                <td class="border border-gray-800 p-2"></td> <!-- Empty for manual paraf -->
                            </tr>
                        @endforeach
                    @endforeach

                    <!-- Filler rows if empty -->
                    @if ($kartuPembayaran->tagihan->count() == 0)
                        <tr>
                            <td colspan="6" class="border border-gray-800 p-4 text-center text-gray-500 italic">Belum
                                ada data tagihan.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Signature -->
        <div class="flex justify-end mt-12 pr-12">
            <div class="text-center w-48">
                <p class="mb-16">Bendahara Pesantren,</p>
                <p class="font-bold underline decoration-dotted">{{ Auth::user()->name }}</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 border-t border-gray-300 pt-2 text-center text-xs text-gray-500">
            Dicetak pada: {{ now()->format('d F Y H:i') }}
        </div>

    </div>

    <!-- Print Controls -->
    <div class="fixed bottom-8 right-8 flex space-x-4 no-print">
        <button onclick="window.print()"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full shadow-lg flex items-center transition-transform hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Cetak Sekarang
        </button>
        <button onclick="window.close()"
            class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-full shadow-lg transition-transform hover:scale-105">
            Tutup
        </button>
    </div>

</body>

</html>
