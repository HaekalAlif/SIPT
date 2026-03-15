@extends('layouts.admin')

@section('content')
    <div x-data="{ openModal: false, editMode: false, item: { kategori_id: '', nama_tagihan: '', nominal: '', is_bulanan: false, target_scope: 'all', target_value: '' } }">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Data Jenis Tagihan</h1>
            <button
                @click="openModal = true; editMode = false; item = { kategori_id: '', nama_tagihan: '', nominal: '', is_bulanan: false, target_scope: 'all', target_value: '' }"
                class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg flex items-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Jenis Tagihan
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Kategori</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                                Tagihan</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nominal (Rp)</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Berlaku Untuk</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($jenisTagihan as $jenis)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $jenis->kategori->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $jenis->nama_tagihan }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($jenis->nominal, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    @if (($jenis->target_scope ?? 'all') === 'tingkatan')
                                        Tingkatan: {{ $jenis->target_value }}
                                    @elseif(($jenis->target_scope ?? 'all') === 'ngaji')
                                        Tingkatan Ngaji: {{ $jenis->target_value }}
                                    @else
                                        Semua Jenjang
                                    @endif
                                    @if ($jenis->is_bulanan)
                                        <span
                                            class="ml-2 inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-700">Bulanan</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button
                                            @click="openModal = true; editMode = true; item = { id: {{ $jenis->id }}, kategori_id: '{{ $jenis->kategori_id }}', nama_tagihan: @js($jenis->nama_tagihan), nominal: {{ $jenis->nominal }}, is_bulanan: {{ $jenis->is_bulanan ? 'true' : 'false' }}, target_scope: @js($jenis->target_scope ?? 'all'), target_value: @js($jenis->target_value ?? '') }"
                                            class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 rounded-lg p-2 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.jenis-tagihan.destroy', $jenis->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis tagihan ini?');"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 rounded-lg p-2 transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada data jenis tagihan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $jenisTagihan->links() }}
            </div>
        </div>

        <!-- Modal -->
        <div x-show="openModal" class="fixed inset-0 overflow-y-auto z-50" style="display: none;">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="openModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form
                        :action="editMode ? '{{ route('admin.jenis-tagihan') }}/' + item.id :
                            '{{ route('admin.jenis-tagihan.store') }}'"
                        method="POST">
                        @csrf
                        <template x-if="editMode">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900"
                                x-text="editMode ? 'Edit Jenis Tagihan' : 'Tambah Jenis Tagihan'"></h3>
                            <div class="mt-4 space-y-4">
                                <!-- Kategori -->
                                <div>
                                    <label for="kategori_id"
                                        class="block text-sm font-medium text-gray-700">Kategori</label>
                                    <select name="kategori_id" id="kategori_id" x-model="item.kategori_id" required
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($kategoriTagihan as $kat)
                                            <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Nama Tagihan -->
                                <div>
                                    <label for="nama_tagihan" class="block text-sm font-medium text-gray-700">Nama
                                        Tagihan</label>
                                    <input type="text" name="nama_tagihan" id="nama_tagihan" x-model="item.nama_tagihan"
                                        required
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Contoh: SPP Januari">
                                </div>
                                <!-- Nominal -->
                                <div>
                                    <label for="nominal" class="block text-sm font-medium text-gray-700">Nominal Tagihan
                                        (Rp)</label>
                                    <input type="number" name="nominal" id="nominal" x-model="item.nominal" required
                                        class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        placeholder="Contoh: 150000">
                                </div>

                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="hidden" name="is_bulanan" value="0">
                                        <input type="checkbox" name="is_bulanan" value="1"
                                            x-model="item.is_bulanan"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Tagihan Bulanan</span>
                                    </label>
                                </div>

                                <div>
                                    <label for="target_scope" class="block text-sm font-medium text-gray-700">Berlaku
                                        Untuk</label>
                                    <select name="target_scope" id="target_scope" x-model="item.target_scope" required
                                        @change="if (item.target_scope === 'all') item.target_value = ''"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="all">Semua Jenjang</option>
                                        <option value="tingkatan">Per Tingkatan Sekolah</option>
                                        <option value="ngaji">Per Tingkatan Ngaji</option>
                                    </select>
                                </div>

                                <div x-show="item.target_scope === 'tingkatan'" x-cloak>
                                    <label for="target_tingkatan" class="block text-sm font-medium text-gray-700">Pilih
                                        Tingkatan</label>
                                    <select id="target_tingkatan" name="target_value" x-model="item.target_value"
                                        :disabled="item.target_scope !== 'tingkatan'"
                                        :required="item.target_scope === 'tingkatan'"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Pilih Tingkatan</option>
                                        @foreach ($tingkatanOptions as $tingkatan)
                                            <option value="{{ $tingkatan }}">{{ $tingkatan }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div x-show="item.target_scope === 'ngaji'" x-cloak>
                                    <label for="target_ngaji" class="block text-sm font-medium text-gray-700">Pilih
                                        Tingkatan Ngaji</label>
                                    <select id="target_ngaji" name="target_value" x-model="item.target_value"
                                        :disabled="item.target_scope !== 'ngaji'"
                                        :required="item.target_scope === 'ngaji'"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">Pilih Tingkatan Ngaji</option>
                                        @foreach ($tingkatanNgajiOptions as $ngaji)
                                            <option value="{{ $ngaji }}">{{ $ngaji }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button @click="openModal = false" type="button"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
