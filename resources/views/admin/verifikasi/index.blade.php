@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800">Verifikasi Pembayaran</h1>
    </div>

    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.verifikasi-pembayaran') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full py-2 px-3 border border-gray-300 rounded-md bg-white placeholder-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 sm:text-sm"
                    placeholder="Cari nama santri...">
            </div>
            <div>
                <select name="tahun_ajaran_id"
                    class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach ($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}"
                            {{ (string) request('tahun_ajaran_id') === (string) $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="tingkatan" id="tingkatan-filter"
                    class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Tingkatan</option>
                    @foreach ($tingkatanOptions as $tingkatan)
                        <option value="{{ $tingkatan }}" {{ request('tingkatan') == $tingkatan ? 'selected' : '' }}>
                            {{ $tingkatan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="kelas" id="kelas-filter"
                    class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                    <option value="">Semua Kelas</option>
                </select>
            </div>
            <button type="submit"
                class="bg-gray-800 hover:bg-gray-900 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
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
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pembayarans as $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold mr-3">
                                        {{ substr($p->tagihan->kartuPembayaran->user->nama_santri ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $p->tagihan->kartuPembayaran->user->nama_santri ?? '-' }}</div>

                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $p->tagihan->jenisTagihan->nama ?? 'Tagihan #' . $p->tagihan_id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                @if ($p->bukti_pembayaran)
                                    <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" target="_blank"
                                        class="hover:underline flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-gray-400">Tidak ada bukti</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.verifikasi-pembayaran.show', $p->id) }}"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm transition-colors">
                                    Proses
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-lg font-medium text-gray-900">Tidak ada pembayaran yang perlu
                                        diverifikasi.</p>
                                    <p class="text-sm text-gray-500">Semua pembayaran masuk telah diproses.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            {{ $pembayarans->links() }}
        </div>
    </div>

    <script>
        const kelasOptionsByTingkatan = @json($kelasOptionsByTingkatan ?? []);
        const tingkatanSelect = document.getElementById('tingkatan-filter');
        const kelasSelect = document.getElementById('kelas-filter');
        const selectedKelas = @json(request('kelas'));

        function renderKelasOptions() {
            if (!tingkatanSelect || !kelasSelect) return;
            const tingkatan = tingkatanSelect.value;
            const options = kelasOptionsByTingkatan[tingkatan] || [];

            kelasSelect.innerHTML = '<option value="">Semua Kelas</option>';
            options.forEach((kelas) => {
                const option = document.createElement('option');
                option.value = kelas;
                option.textContent = kelas;
                if (selectedKelas === kelas) {
                    option.selected = true;
                }
                kelasSelect.appendChild(option);
            });
        }

        tingkatanSelect?.addEventListener('change', () => {
            kelasSelect.value = '';
            renderKelasOptions();
        });

        renderKelasOptions();
    </script>
@endsection
