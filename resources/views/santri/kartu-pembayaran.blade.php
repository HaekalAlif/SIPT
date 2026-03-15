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
            <div class="flex items-center gap-2">
                @if (!isset($error) && $kartuPembayaran)
                    <a href="{{ route('santri.kartu-pembayaran.pdf', ['tahun_ajaran_id' => $tahunAjaran?->id]) }}"
                        class="bg-blue-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fa fa-file-pdf"></i> Download PDF
                    </a>
                @endif
            </div>
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
        @include('santri.partials.kartu-pembayaran-preview')
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
