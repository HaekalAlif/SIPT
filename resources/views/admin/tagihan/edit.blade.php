@extends('layouts.admin')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Tagihan #{{ $tagihan->id }}</h1>
        <p class="text-gray-600">Perbarui informasi status tagihan.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-2xl">
        <form action="{{ route('admin.tagihan.update', $tagihan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <!-- Info Readonly -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Santri</label>
                    <div class="text-lg font-semibold text-gray-800">
                        {{ $tagihan->kartuPembayaran->user->nama_santri ?? '-' }}
                        <span class="text-sm font-normal text-gray-500">({{ $tagihan->kartuPembayaran->no_kartu }})</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Total Tagihan</label>
                    <div class="text-lg font-semibold text-gray-800">
                        Rp
                        {{ number_format($tagihan->total_tagihan ?? $tagihan->tagihanDetails->sum('nominal'), 0, ',', '.') }}
                    </div>
                </div>

                <!-- Status Select -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status Tagihan</label>
                    <select name="status" id="status"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-200">
                        <option value="belum_bayar" {{ $tagihan->status == 'belum_bayar' ? 'selected' : '' }}>Belum Bayar
                        </option>
                        <option value="menunggu_verifikasi"
                            {{ $tagihan->status == 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="lunas" {{ $tagihan->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                    </select>
                    <p class="text-xs text-yellow-600 mt-2">
                        <span class="font-bold">Perhatian:</span> Mengubah status menjadi "Lunas" akan otomatis menandai
                        semua detail item tagihan ini menjadi lunas.
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end items-center space-x-4">
                <a href="{{ route('admin.tagihan.show', $tagihan->id) }}"
                    class="text-gray-600 hover:text-gray-800 font-medium">Batal</a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg shadow-md transition-colors flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection
