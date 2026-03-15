@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Laporan Pemasukan</h1>

    </div>

    <!-- Filter Date -->
    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100 no-print">
        <form action="{{ route('admin.laporan-pemasukan') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <div class="flex-1">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-md shadow-sm transition-colors">
                Tampilkan
            </button>
        </form>
    </div>

    <!-- Summary Card -->
    <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h3 class="text-lg font-semibold text-blue-800">Total Pemasukan</h3>
                <p class="text-sm text-blue-600">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} -
                    {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="text-3xl font-bold text-blue-900">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Santri
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tagihan
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nominal
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pembayarans as $index => $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $loop->iteration }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $p->tagihan->kartuPembayaran->user->nama_santri ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $p->tagihan->jenisTagihan->nama ?? 'Tagihan #' . $p->tagihan_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                Tidak ada data pemasukan pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($pembayarans->count() > 0)
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-700">Total Periode Ini:</td>
                            <td class="px-6 py-4 font-bold text-blue-800">Rp
                                {{ number_format($totalPemasukan, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="no-print mt-4">
        {{ $pembayarans->withQueryString()->links() }}
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .sidebar,
            header,
            .no-print {
                display: none !important;
            }

            #main-content,
            #main-content * {
                visibility: visible;
            }

            #main-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }
        }
    </style>
@endsection
