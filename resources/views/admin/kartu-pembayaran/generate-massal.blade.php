@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.kartu-pembayaran.index') }}"
            class="text-gray-500 hover:text-gray-700 font-medium flex items-center mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Data Kartu
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Generate Kartu Pembayaran Massal</h1>
        <p class="text-gray-600 mt-1">Buat kartu pembayaran secara otomatis untuk banyak santri sekaligus.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form action="{{ route('admin.kartu-pembayaran.store-massal') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Tahun Ajaran -->
                <div>
                    <label for="tahun_ajaran_id" class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran <span
                            class="text-red-500">*</span></label>
                    <select name="tahun_ajaran_id" id="tahun_ajaran_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">Pilih Tahun Ajaran</option>
                        @foreach ($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}">{{ $ta->tahun_ajaran }}
                                ({{ $ta->status == 'aktif' ? 'Aktif' : 'Non-aktif' }})</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Kartu akan dibuat untuk tahun ajaran yang dipilih.</p>
                </div>

                <!-- Tingkatan Filter -->
                <div>
                    <label for="tingkatan" class="block text-sm font-medium text-gray-700 mb-1">Filter Tingkatan
                        (Opsional)</label>
                    <select name="tingkatan" id="tingkatan"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">Semua Tingkatan</option>
                        @foreach ($tingkatans as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kelas Filter -->
                <div>
                    <label for="kelas" class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas
                        (Opsional)</label>
                    <select name="kelas" id="kelas"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k }}">{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700">
                    <div class="flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 flex-shrink-0" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>
                            <strong>Catatan:</strong> Sistem akan membuat kartu pembayaran baru hanya untuk santri yang
                            <strong>BELUM</strong> memiliki kartu pada Tahun Ajaran yang dipilih. Santri yang sudah punya
                            kartu akan dilewati.
                        </span>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded-lg shadow-md transition-colors flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Generate Kartu
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
