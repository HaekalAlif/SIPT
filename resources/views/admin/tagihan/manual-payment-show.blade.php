@extends('layouts.admin')

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Bayar Manual Santri</h1>
            <p class="text-sm text-gray-500 mt-1">Pilih jenis tagihan yang belum dibayar, sistem akan membuat tagihan (jika
                belum ada) dan langsung melunasi otomatis.</p>
        </div>
        <a href="{{ route('admin.manual-payment.index') }}" class="text-gray-600 hover:text-gray-800 text-sm">Kembali</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-sm space-y-2">
                <div><span class="text-gray-500">Santri:</span> <span
                        class="font-semibold">{{ $user->nama_santri ?? '-' }}</span>
                </div>
                </div>
                <div><span class="text-gray-500">Tingkatan / Kelas:</span> <span
                        class="font-semibold">{{ ucfirst($user->tingkatan ?? '-') }} / {{ $user->kelas ?? '-' }}</span>
                </div>
                <div><span class="text-gray-500">Tingkatan Ngaji:</span> <span
                        class="font-semibold">{{ $user->tingkatan_ngaji ?? '-' }}</span></div>
                <div><span class="text-gray-500">Tahun Ajaran Aktif:</span> <span
                        class="font-semibold">{{ $tahunAjaranAktif->nama ?? '-' }}</span></div>
                <div><span class="text-gray-500">Total Tagihan:</span> <span class="font-semibold">Rp
                        {{ number_format($summary['total_tagihan_nominal'] ?? 0, 0, ',', '.') }}</span></div>
                <div><span class="text-gray-500">Sudah Dibayar:</span> <span class="font-semibold">Rp
                        {{ number_format($summary['total_sudah_dibayar'] ?? 0, 0, ',', '.') }}</span></div>
                <div><span class="text-gray-500">Sisa Tagihan:</span> <span class="font-semibold text-red-600">Rp
                        {{ number_format($summary['total_sisa'] ?? 0, 0, ',', '.') }}</span></div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mt-4">
                <h3 class="font-semibold text-gray-800 mb-3">Riwayat Pembayaran</h3>
                <div class="space-y-2 text-sm max-h-64 overflow-y-auto pr-1">
                    @forelse ($pembayarans as $p)
                        <div class="border border-gray-100 rounded p-2">
                            <div class="text-gray-700">{{ \Carbon\Carbon::parse($p->tanggal_bayar)->format('d M Y') }}
                            </div>
                            <div class="font-semibold">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">{{ $p->catatan ?: '-' }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Belum ada pembayaran.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <form action="{{ route('admin.manual-payment.store-bulk', $user->id) }}" method="POST"
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Tanggal Bayar</label>
                        <input type="date" name="tanggal_bayar" required value="{{ now()->format('Y-m-d') }}"
                            class="w-full rounded border-gray-300 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs text-gray-500 mb-1">Catatan</label>
                        <input type="text" name="catatan" class="w-full rounded border-gray-300 text-sm"
                            placeholder="Contoh: Bayar manual di kantor">
                    </div>
                </div>

                <div class="mb-3 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Checklist Jenis Tagihan</h3>
                    <button type="button" id="check-all-btn" class="text-xs text-blue-600 hover:underline">Centang Semua
                        Yang Belum Dibayar</button>
                </div>

                <div class="mb-3 flex flex-wrap items-center gap-2" id="kategori-tabs">
                    <button type="button" data-tab="all"
                        class="tab-btn bg-blue-600 text-white px-3 py-1.5 rounded-full text-xs font-semibold">Semua</button>
                    @foreach ($kategoriTagihan as $kategori)
                        @php $tabId = \Illuminate\Support\Str::slug($kategori->nama, '-'); @endphp
                        <button type="button" data-tab="{{ $tabId }}"
                            class="tab-btn bg-gray-100 text-gray-700 hover:bg-gray-200 px-3 py-1.5 rounded-full text-xs font-semibold">
                            {{ $kategori->nama }}
                        </button>
                    @endforeach
                </div>

                <div class="overflow-x-auto border border-gray-100 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left text-xs text-gray-500">Pilih</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500">Jenis</th>
                                <th class="px-3 py-2 text-left text-xs text-gray-500">Bulan</th>
                                <th class="px-3 py-2 text-right text-xs text-gray-500">Nominal</th>
                                <th class="px-3 py-2 text-center text-xs text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @php
                                $hasRows = false;
                                $bulanList = [
                                    'Juli',
                                    'Agustus',
                                    'September',
                                    'Oktober',
                                    'November',
                                    'Desember',
                                    'Januari',
                                    'Februari',
                                    'Maret',
                                    'April',
                                    'Mei',
                                    'Juni',
                                ];
                            @endphp

                            @foreach ($kategoriTagihan as $kategori)
                                @php $tabId = \Illuminate\Support\Str::slug($kategori->nama, '-'); @endphp
                                @foreach ($kategori->jenisTagihan as $jenis)
                                    @if ($jenis->is_bulanan)
                                        @foreach ($bulanList as $bulan)
                                            @php
                                                $hasRows = true;
                                                $sudahBayar =
                                                    isset($paidBulanMap[$jenis->id]) &&
                                                    in_array($bulan, $paidBulanMap[$jenis->id], true);
                                            @endphp
                                            <tr class="payment-row {{ $sudahBayar ? 'bg-green-50' : '' }}"
                                                data-category="{{ $tabId }}">
                                                <td class="px-3 py-2 text-sm">
                                                    @if ($sudahBayar)
                                                        <input type="checkbox" checked disabled
                                                            class="rounded border-gray-300">
                                                    @else
                                                        <input type="checkbox" name="selected_items[]"
                                                            value="{{ $jenis->id }}|{{ $bulan }}"
                                                            data-nominal="{{ (float) $jenis->nominal }}"
                                                            class="manual-item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                    @endif
                                                </td>
                                                <td class="px-3 py-2 text-sm text-gray-800">{{ $jenis->nama_tagihan }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-600">{{ $bulan }}</td>
                                                <td class="px-3 py-2 text-sm text-right font-medium">Rp
                                                    {{ number_format($jenis->nominal, 0, ',', '.') }}</td>
                                                <td class="px-3 py-2 text-center text-xs">
                                                    <span
                                                        class="px-2 py-1 rounded {{ $sudahBayar ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                        {{ $sudahBayar ? 'Sudah Terbayar' : 'Belum Bayar' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        @php
                                            $hasRows = true;
                                            $sudahBayar = in_array($jenis->id, $paidJenisIds ?? [], true);
                                        @endphp
                                        <tr class="payment-row {{ $sudahBayar ? 'bg-green-50' : '' }}"
                                            data-category="{{ $tabId }}">
                                            <td class="px-3 py-2 text-sm">
                                                @if ($sudahBayar)
                                                    <input type="checkbox" checked disabled class="rounded border-gray-300">
                                                @else
                                                    <input type="checkbox" name="selected_items[]"
                                                        value="{{ $jenis->id }}|-"
                                                        data-nominal="{{ (float) $jenis->nominal }}"
                                                        class="manual-item-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                                @endif
                                            </td>
                                            <td class="px-3 py-2 text-sm text-gray-800">{{ $jenis->nama_tagihan }}</td>
                                            <td class="px-3 py-2 text-sm text-gray-600">-</td>
                                            <td class="px-3 py-2 text-sm text-right font-medium">Rp
                                                {{ number_format($jenis->nominal, 0, ',', '.') }}</td>
                                            <td class="px-3 py-2 text-center text-xs">
                                                <span
                                                    class="px-2 py-1 rounded {{ $sudahBayar ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                    {{ $sudahBayar ? 'Sudah Terbayar' : 'Belum Bayar' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach

                            @if (!$hasRows)
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada jenis
                                        tagihan yang tersedia untuk santri ini.</td>
                                </tr>
                            @endif

                            <tr id="empty-tab-row" class="hidden">
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada data pada
                                    kategori ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex items-center justify-between bg-blue-50 border border-blue-100 rounded-lg px-4 py-3">
                    <div class="text-sm text-blue-700">Total pembayaran dipilih:</div>
                    <div id="selected-total" class="text-lg font-bold text-blue-800">Rp 0</div>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg">
                        Buat Tagihan & Lunasi Manual
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = Array.from(document.querySelectorAll('.manual-item-checkbox'));
            const rows = Array.from(document.querySelectorAll('.payment-row'));
            const tabButtons = Array.from(document.querySelectorAll('.tab-btn'));
            const emptyTabRow = document.getElementById('empty-tab-row');
            const totalNode = document.getElementById('selected-total');
            const checkAllBtn = document.getElementById('check-all-btn');
            let activeTab = 'all';

            function formatRupiah(value) {
                return 'Rp ' + Number(value).toLocaleString('id-ID');
            }

            function refreshRowsByTab() {
                let visibleCount = 0;

                rows.forEach(row => {
                    const rowTab = row.dataset.category;
                    const show = activeTab === 'all' || rowTab === activeTab;

                    row.classList.toggle('hidden', !show);
                    if (show) {
                        visibleCount++;
                    }
                });

                if (emptyTabRow) {
                    emptyTabRow.classList.toggle('hidden', visibleCount > 0);
                }
            }

            function activateTab(tab) {
                activeTab = tab;

                tabButtons.forEach(btn => {
                    const isActive = btn.dataset.tab === tab;
                    btn.classList.toggle('bg-blue-600', isActive);
                    btn.classList.toggle('text-white', isActive);
                    btn.classList.toggle('bg-gray-100', !isActive);
                    btn.classList.toggle('text-gray-700', !isActive);
                });

                refreshRowsByTab();
            }

            function recalc() {
                const total = checkboxes
                    .filter(cb => cb.checked)
                    .reduce((sum, cb) => sum + Number(cb.dataset.nominal || 0), 0);
                totalNode.textContent = formatRupiah(total);
            }

            checkboxes.forEach(cb => cb.addEventListener('change', recalc));
            checkAllBtn.addEventListener('click', function() {
                checkboxes.forEach(cb => {
                    const row = cb.closest('tr');
                    if (!row) return;

                    const rowTab = row.dataset.category;
                    const inActiveTab = activeTab === 'all' || rowTab === activeTab;
                    if (!row.classList.contains('hidden') && inActiveTab) {
                        cb.checked = true;
                    }
                });
                recalc();
            });

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    activateTab(btn.dataset.tab || 'all');
                });
            });

            activateTab('all');
            recalc();
        });
    </script>
@endsection
