@extends('layouts.admin')

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-800">Setting Metode Pembayaran</h1>
            <span class="text-sm text-gray-500">Kelola rekening tujuan yang tampil di halaman santri</span>
        </div>

        @if (session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-800">
                <ul class="list-disc ml-5 space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Metode Pembayaran</h2>
            <form action="{{ route('admin.settings.metode-pembayaran.store') }}" method="POST" enctype="multipart/form-data"
                class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Metode</label>
                    <input type="text" name="nama_metode" value="{{ old('nama_metode') }}" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200"
                        placeholder="Contoh: Transfer Bank BRI">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                    <input type="text" name="nama_bank" value="{{ old('nama_bank') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200"
                        placeholder="Contoh: Bank BRI">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                    <input type="text" name="nomor_rekening" value="{{ old('nomor_rekening') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200"
                        placeholder="Contoh: 1234-5678-9012-3456">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Atas Nama</label>
                    <input type="text" name="atas_nama" value="{{ old('atas_nama') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200"
                        placeholder="Contoh: Siti Mutoharoh, S.E">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logo Bank</label>
                    <input type="file" name="logo_file" accept="image/*"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                        <input type="number" name="urutan" value="{{ old('urutan', 0) }}" min="0"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="is_active"
                            class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200">
                            <option value="1" {{ old('is_active', '1') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                    <textarea name="keterangan" rows="2"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-200"
                        placeholder="Opsional, contoh instruksi transfer.">{{ old('keterangan') }}</textarea>
                </div>

                <div class="md:col-span-2 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-5 rounded-lg transition">
                        Simpan Metode
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Daftar Metode Pembayaran</h2>

            @if ($metodePembayaran->count() === 0)
                <div class="text-sm text-gray-500">Belum ada metode pembayaran.</div>
            @else
                <div class="space-y-4">
                    @foreach ($metodePembayaran as $metode)
                        <form action="{{ route('admin.settings.metode-pembayaran.update', $metode->id) }}" method="POST"
                            enctype="multipart/form-data"
                            class="rounded-lg border border-gray-200 p-4 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                            @csrf
                            @method('PUT')

                            <div class="md:col-span-3">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Metode</label>
                                <input type="text" name="nama_metode" value="{{ $metode->nama_metode }}" required
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-200">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Bank</label>
                                <input type="text" name="nama_bank" value="{{ $metode->nama_bank }}"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-200">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">No. Rekening</label>
                                <input type="text" name="nomor_rekening" value="{{ $metode->nomor_rekening }}"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-200">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Atas Nama</label>
                                <input type="text" name="atas_nama" value="{{ $metode->atas_nama }}"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-200">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan</label>
                                <input type="number" name="urutan" value="{{ $metode->urutan }}" min="0"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-200">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                                <select name="is_active"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-200">
                                    <option value="1" {{ $metode->is_active ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ !$metode->is_active ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>

                            <div class="md:col-span-6">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Logo Bank (Upload
                                    Baru)</label>
                                <input type="file" name="logo_file" accept="image/*"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-200 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-blue-700 hover:file:bg-blue-100">
                                @if ($metode->logo_path)
                                    <div class="mt-2 flex items-center gap-2">
                                        <img src="{{ Storage::url($metode->logo_path) }}"
                                            alt="{{ $metode->nama_metode }}"
                                            class="h-10 w-auto object-contain rounded border border-gray-200">
                                        <span class="text-xs text-gray-500">{{ $metode->logo_path }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="md:col-span-6">
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Keterangan</label>
                                <input type="text" name="keterangan" value="{{ $metode->keterangan }}"
                                    class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-200">
                            </div>

                            <div class="md:col-span-12 flex justify-between items-center gap-3 pt-1">
                                <div class="text-xs text-gray-400">
                                    Dibuat: {{ $metode->created_at?->format('d M Y H:i') }}
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit"
                                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded transition">
                                        Update
                                    </button>
                                </div>
                            </div>
                        </form>

                        <form action="{{ route('admin.settings.metode-pembayaran.destroy', $metode->id) }}"
                            method="POST" class="flex justify-end -mt-2"
                            onsubmit="return confirm('Hapus metode pembayaran ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 text-sm font-semibold">
                                Hapus Metode
                            </button>
                        </form>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $metodePembayaran->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
