@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Buat Tagihan Baru</h1>
        <p class="text-gray-600">Buat tagihan manual untuk santri tertentu di luar tagihan reguler.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.tagihan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Pilih Santri / Kartu -->
                <div class="col-span-1 md:col-span-2">
                    <label for="kartu_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Santri (Kartu Pembayaran
                        Aktif)</label>
                    <select name="kartu_id" id="kartu_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">-- Pilih Santri --</option>
                        @foreach ($kartuPembayarans as $kartu)
                            <option value="{{ $kartu->id }}"
                                {{ old('kartu_id', $selectedKartuId ?? '') == $kartu->id ? 'selected' : '' }}>
                                {{ $kartu->user->nama_santri ?? $kartu->user->name }} -
                                {{ $kartu->tahunAjaran->tahun_ajaran }} ({{ $kartu->nomor_kartu }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pastikan santri sudah memiliki Kartu Pembayaran untuk Tahun Ajaran
                        aktif.</p>
                </div>

                <!-- Pilih Jenis Tagihan -->
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Jenis Tagihan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($jenisTagihans as $jenis)
                            <div
                                class="relative flex items-start p-3 rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center h-5">
                                    <input id="jenis_{{ $jenis->id }}" name="jenis_tagihan_ids[]" type="checkbox"
                                        value="{{ $jenis->id }}"
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="jenis_{{ $jenis->id }}"
                                        class="font-medium text-gray-700">{{ $jenis->nama_tagihan }}</label>
                                    <p class="text-gray-500">Rp {{ number_format($jenis->nominal, 0, ',', '.') }}</p>
                                    <span class="text-xs text-blue-600 bg-blue-50 px-1 py-0.5 rounded">
                                        {{ $jenis->kategori->nama ?? 'Umum' }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Status Awal -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Awal</label>
                    <select name="status" id="status" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="belum_bayar">Belum Bayar</option>
                        <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                        <option value="lunas">Lunas</option>
                    </select>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end items-center space-x-4">
                <a href="{{ route('admin.tagihan.index') }}"
                    class="text-gray-600 hover:text-gray-800 font-medium">Batal</a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow-md transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Tagihan
                </button>
            </div>
        </form>
    </div>
@endsection
