@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-600">Selamat datang kembali, admin!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Card: Total Santri -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-blue-100 p-3 rounded-lg text-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-500">Total Santri</span>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_santri'] }}</h3>
                    <p class="text-xs text-green-500 mt-1 flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                        </svg>
                        Aktif
                    </p>
                </div>
            </div>
        </div>

        <!-- Card: Menunggu Verifikasi -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-yellow-100 p-3 rounded-lg text-yellow-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-500">Menunggu Verifikasi</span>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['menunggu_verifikasi'] }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Pembayaran baru</p>
                </div>
            </div>
        </div>

        <!-- Card: Pemasukan Bulan Ini -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-green-100 p-3 rounded-lg text-green-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-500">Pemasukan Bulan Ini</span>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">Rp
                        {{ number_format($stats['pemasukan_bulan_ini'], 0, ',', '.') }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Total terverifikasi</p>
                </div>
            </div>
        </div>

        <!-- Card: Tagihan Belum Lunas -->
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-red-100 p-3 rounded-lg text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-500">Tagihan Belum Lunas</span>
            </div>
            <div class="flex items-end justify-between">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['tagihan_belum_lunas'] }}</h3>
                    <p class="text-xs text-red-500 mt-1">Perlu ditindaklanjuti</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity / Verification Request Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Verification Requests -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Perlu Verifikasi Segera</h3>
                <a href="{{ route('admin.verifikasi-pembayaran') }}"
                    class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3 font-medium">Santri</th>
                            <th class="px-6 py-3 font-medium">Tanggal</th>
                            <th class="px-6 py-3 font-medium">Nominal</th>
                            <th class="px-6 py-3 font-medium">Status</th>
                            <th class="px-6 py-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($pending_payments as $payment)
                            @php
                                $santri = $payment->tagihan?->kartuPembayaran?->user;
                                $namaSantri = $santri?->nama_santri ?? ($santri?->name ?? 'Santri');
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold mr-3">
                                            {{ strtoupper(substr($namaSantri, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $namaSantri }}</p>
                                            <p class="text-xs text-gray-500">{{ $santri?->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($payment->tanggal_bayar)->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                    Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">
                                        {{ ucfirst(str_replace('_', ' ', $payment->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.verifikasi-pembayaran.show', $payment->id) }}"
                                        class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Proses
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Tidak ada pembayaran yang perlu diverifikasi saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Santri Terbaru</h3>
                <a href="{{ route('admin.users') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Lihat
                    Semua</a>
            </div>
            <div class="p-6">
                <ul class="space-y-4">
                    @forelse ($recent_users as $recentUser)
                        <li class="flex items-center">
                            <div
                                class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-sm mr-3">
                                {{ strtoupper(substr($recentUser->nama_santri ?? ($recentUser->name ?? '?'), 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    {{ $recentUser->nama_santri ?? ($recentUser->name ?? '-') }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $recentUser->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </li>
                    @empty
                        <li class="text-sm text-gray-400 text-center py-4">Belum ada santri terdaftar.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
@endsection
