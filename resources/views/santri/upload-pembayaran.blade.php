{{-- filepath: resources/views/santri/upload-pembayaran.blade.php --}}
@extends('layouts.santri')

@section('content')
    <div class="space-y-6">
        <div class="bg-green-700 text-white p-4 rounded-lg">
            <h1 class="text-xl font-bold">Upload Bukti Pembayaran</h1>
        </div>

        <!-- Info Tagihan -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center gap-2 text-blue-800">
                <i class="fa fa-info-circle"></i>
                <span class="font-semibold">Informasi Tagihan</span>
            </div>
            <div class="mt-2 text-sm text-blue-700">
                @php
                    $kategoriName = $tagihan->tagihanDetails->first()->jenisTagihan->kategori->nama ?? 'Unknown';
                @endphp
                Tagihan {{ $kategoriName }} dengan total
                <strong>Rp.{{ number_format($tagihan->total, 0, ',', '.') }}</strong>
            </div>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center gap-2 text-red-800 mb-2">
                    <i class="fa fa-exclamation-triangle"></i>
                    <span class="font-semibold">Error!</span>
                </div>
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Success Messages -->
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-2 text-green-800">
                    <i class="fa fa-check-circle"></i>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <!-- Form Upload -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-green-700">Form Upload Bukti Pembayaran</h3>
            </div>

            <form action="{{ route('santri.store-pembayaran', $tagihan->id) }}" method="POST" enctype="multipart/form-data"
                class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Tanggal Bayar -->
                    <div>
                        <label for="tanggal_bayar" class="block text-sm font-semibold text-gray-700 mb-2">Tanggal
                            Bayar</label>
                        <input type="date" id="tanggal_bayar" name="tanggal_bayar"
                            value="{{ old('tanggal_bayar', date('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            required>
                        @error('tanggal_bayar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jumlah Bayar -->
                    <div>
                        <label for="jumlah_bayar" class="block text-sm font-semibold text-gray-700 mb-2">Jumlah
                            Bayar</label>
                        <input type="number" id="jumlah_bayar" name="jumlah_bayar"
                            value="{{ old('jumlah_bayar', $tagihan->total) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            required min="1">
                        @error('jumlah_bayar')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Upload Bukti -->
                <div class="mb-6">
                    <label for="bukti_pembayaran" class="block text-sm font-semibold text-gray-700 mb-2">Bukti
                        Pembayaran</label>
                    <div class="border-dashed border-2 border-gray-300 rounded-lg p-8 text-center">
                        <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" class="hidden"
                            onchange="previewImage(this)" required>
                        <div id="upload-placeholder">
                            <i class="fa fa-cloud-upload text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 mb-2">Klik untuk upload bukti pembayaran</p>
                            <p class="text-sm text-gray-500">File yang didukung: JPG, PNG, JPEG (Max: 2MB)</p>
                            <button type="button" onclick="document.getElementById('bukti_pembayaran').click()"
                                class="mt-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                                Pilih File
                            </button>
                        </div>
                        <div id="image-preview" class="hidden">
                            <img id="preview-img" src="" alt="Preview" class="max-w-xs max-h-64 mx-auto rounded">
                            <p id="file-name" class="mt-2 text-sm text-gray-600"></p>
                            <button type="button" onclick="removeImage()"
                                class="mt-2 text-red-600 hover:text-red-800 text-sm">
                                Hapus File
                            </button>
                        </div>
                    </div>
                    @error('bukti_pembayaran')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Catatan Penting -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-2">
                        <i class="fa fa-info-circle text-blue-600 mt-1"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-2">Informasi Penting:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Pembayaran dilakukan melalui <strong>Transfer Bank</strong> ke rekening yang tertera di
                                    halaman konfirmasi tagihan</li>
                                <li>Pastikan bukti transfer jelas dan dapat dibaca</li>
                                <li>Jumlah pembayaran harus sesuai dengan total tagihan</li>
                                <li>Bukti pembayaran akan diverifikasi oleh bendahara</li>
                                <li>Status pembayaran dapat dilihat di menu tagihan pembayaran</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Summary Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3">Ringkasan Pembayaran</h4>
                    <div class="space-y-2">
                        @foreach ($tagihan->tagihanDetails->groupBy('jenis_tagihan_id') as $jenisTagihanId => $details)
                            @php
                                $jenisTagihan = $details->first()->jenisTagihan;
                            @endphp
                            <div class="flex justify-between text-sm">
                                <span>{{ $jenisTagihan->nama_tagihan }}</span>
                                <span>Rp.{{ number_format($details->sum('nominal'), 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <hr>
                        <div class="flex justify-between font-semibold">
                            <span>Total</span>
                            <span>Rp.{{ number_format($tagihan->total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('santri.tagihan-pembayaran', ['id' => $tagihan->id]) }}"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded transition"
                        onclick="console.log('Submit button clicked'); return validateForm();">
                        Upload Bukti Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function validateForm() {
            console.log('Validating form...');

            // Check required fields
            const tanggalBayar = document.getElementById('tanggal_bayar').value;
            const jumlahBayar = document.getElementById('jumlah_bayar').value;
            const buktiBayar = document.getElementById('bukti_pembayaran').files[0];

            console.log('Form values:', {
                tanggal_bayar: tanggalBayar,
                jumlah_bayar: jumlahBayar,
                bukti_file: buktiBayar ? buktiBayar.name + ' (' + buktiBayar.size + ' bytes)' : 'No file'
            });

            if (!tanggalBayar) {
                alert('Tanggal bayar harus diisi');
                return false;
            }
            if (!jumlahBayar) {
                alert('Jumlah bayar harus diisi');
                return false;
            }
            if (!buktiBayar) {
                alert('Bukti pembayaran harus diupload');
                return false;
            }

            console.log('Form validation passed');
            return true;
        }

        function previewImage(input) {
            console.log('Preview image called');
            if (input.files && input.files[0]) {
                const file = input.files[0];
                console.log('File selected:', file.name, file.size, 'bytes');

                // Check file size (2MB limit)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File terlalu besar! Maksimal 2MB');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('upload-placeholder').classList.add('hidden');
                    document.getElementById('image-preview').classList.remove('hidden');
                    document.getElementById('preview-img').src = e.target.result;
                    document.getElementById('file-name').textContent = file.name;
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            document.getElementById('bukti_pembayaran').value = '';
            document.getElementById('upload-placeholder').classList.remove('hidden');
            document.getElementById('image-preview').classList.add('hidden');
            document.getElementById('preview-img').src = '';
            document.getElementById('file-name').textContent = '';
        }
    </script>
@endsection
