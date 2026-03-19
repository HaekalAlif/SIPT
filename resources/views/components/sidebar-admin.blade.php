{{-- filepath: resources/views/components/sidebar-admin.blade.php --}}
<aside class="w-64 bg-green-800 text-white flex flex-col h-full overflow-hidden">
    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-3">
        <!-- Dashboard -->
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-green-700 text-green-200' : 'hover:bg-green-700' }} font-semibold transition">
            <i class="fa fa-tachometer-alt w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- Master Data Dropdown -->
        <div class="space-y-2">
            <div class="flex items-center gap-3 px-3 py-2 rounded hover:bg-green-700 transition cursor-pointer"
                onclick="toggleSubmenu('master-data')">
                <i class="fa fa-database w-5"></i>
                <span class="flex-1">Master Data</span>
                <i id="chevron-master-data" class="fa fa-chevron-down text-xs transition-transform"></i>
            </div>
            <div id="submenu-master-data" class="ml-8 space-y-1 hidden">
                <a href="{{ route('admin.users') }}"
                    class="flex items-center gap-2 px-3 py-1 text-sm {{ request()->routeIs('admin.users*') ? 'text-white bg-green-600 rounded' : 'text-green-200 hover:text-white' }} transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Kelola User</span>
                </a>
                <a href="{{ route('admin.tahun-ajaran') }}"
                    class="flex items-center gap-2 px-3 py-1 text-sm {{ request()->routeIs('admin.tahun-ajaran*') ? 'text-white bg-green-600 rounded' : 'text-green-200 hover:text-white' }} transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Tahun Ajaran</span>
                </a>
                <a href="{{ route('admin.kategori-tagihan') }}"
                    class="flex items-center gap-2 px-3 py-1 text-sm {{ request()->routeIs('admin.kategori-tagihan*') ? 'text-white bg-green-600 rounded' : 'text-green-200 hover:text-white' }} transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Kategori Tagihan</span>
                </a>
                <a href="{{ route('admin.jenis-tagihan') }}"
                    class="flex items-center gap-2 px-3 py-1 text-sm {{ request()->routeIs('admin.jenis-tagihan*') ? 'text-white bg-green-600 rounded' : 'text-green-200 hover:text-white' }} transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Jenis Tagihan</span>
                </a>
            </div>
        </div>

        <!-- Kartu & Tagihan -->
        <a href="{{ route('admin.kartu-pembayaran.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.kartu-pembayaran*') || request()->routeIs('admin.tagihan*') ? 'bg-green-700 text-green-200' : 'hover:bg-green-700' }} transition">
            <i class="fa fa-id-card w-5"></i>
            <span>Kartu &amp; Tagihan</span>
        </a>

        <!-- Master Tagihan -->
        <a href="{{ route('admin.master-tagihan') }}"
            class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.master-tagihan*') ? 'bg-green-700 text-green-200' : 'hover:bg-green-700' }} transition">
            <i class="fa fa-sliders-h w-5"></i>
            <span>Master Tagihan</span>
        </a>

        <!-- Bayar Manual -->
        <a href="{{ route('admin.manual-payment.index') }}"
            class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.manual-payment*') ? 'bg-green-700 text-green-200' : 'hover:bg-green-700' }} transition">
            <i class="fa fa-money-bill-wave w-5"></i>
            <span>Bayar Manual</span>
        </a>

        <!-- Rekap Per Kelas -->
        <a href="{{ route('admin.laporan-rekap-kelas') }}"
            class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.laporan-rekap-kelas*') ? 'bg-green-700 text-green-200' : 'hover:bg-green-700' }} transition">
            <i class="fa fa-table w-5"></i>
            <span>Rekap Per Kelas</span>
        </a>

        <!-- Verifikasi Pembayaran -->
        <a href="{{ route('admin.verifikasi-pembayaran') }}"
            class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.verifikasi-pembayaran*') ? 'bg-green-700 text-green-200' : 'hover:bg-green-700' }} transition">
            <i class="fa fa-check-circle w-5"></i>
            <span>Verifikasi Pembayaran</span>
            @if ($pendingCount ?? 0)
                <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
            @endif
        </a>

        <!-- Data Santri -->
        <a href="{{ route('admin.data-santri') }}"
            class="flex items-center gap-3 px-3 py-2 rounded {{ request()->routeIs('admin.data-santri*') ? 'bg-green-700 text-green-200' : 'hover:bg-green-700' }} transition">
            <i class="fa fa-users w-5"></i>
            <span>Data Santri</span>
        </a>

        <!-- Laporan Dropdown -->
        <div class="space-y-2">
            <div class="flex items-center gap-3 px-3 py-2 rounded hover:bg-green-700 transition cursor-pointer"
                onclick="toggleSubmenu('laporan')">
                <i class="fa fa-chart-bar w-5"></i>
                <span class="flex-1">Laporan</span>
                <i id="chevron-laporan" class="fa fa-chevron-down text-xs transition-transform"></i>
            </div>
            <div id="submenu-laporan" class="ml-8 space-y-1 hidden">
                <a href="{{ route('admin.laporan-pemasukan') }}"
                    class="flex items-center gap-2 px-3 py-1 text-sm {{ request()->routeIs('admin.laporan-pemasukan*') ? 'text-white bg-green-600 rounded' : 'text-green-200 hover:text-white' }} transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Laporan Pemasukan</span>
                </a>
                <a href="{{ route('admin.laporan-rekap') }}"
                    class="flex items-center gap-2 px-3 py-1 text-sm {{ request()->routeIs('admin.laporan-rekap*') ? 'text-white bg-green-600 rounded' : 'text-green-200 hover:text-white' }} transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Rekap Keuangan</span>
                </a>
            </div>
        </div>

        <!-- Setting Dropdown -->
        <div class="space-y-2">
            <div class="flex items-center gap-3 px-3 py-2 rounded hover:bg-green-700 transition cursor-pointer"
                onclick="toggleSubmenu('setting')">
                <i class="fa fa-cog w-5"></i>
                <span class="flex-1">Setting</span>
                <i id="chevron-setting" class="fa fa-chevron-down text-xs transition-transform"></i>
            </div>
            <div id="submenu-setting" class="ml-8 space-y-1 hidden">
                <a href="{{ route('admin.settings.metode-pembayaran') }}"
                    class="flex items-center gap-2 px-3 py-1 text-sm {{ request()->routeIs('admin.settings.metode-pembayaran*') ? 'text-white bg-green-600 rounded' : 'text-green-200 hover:text-white' }} transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Metode Pembayaran</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Logout at Bottom -->
    <div class="px-4 pb-6">
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="flex items-center gap-3 px-3 py-2 rounded text-red-300 hover:bg-red-600 hover:text-white transition">
            <i class="fa fa-sign-out-alt w-5"></i>
            <span>LogOut</span>
        </a>
    </div>
</aside>

<script>
    function toggleSubmenu(menuId) {
        const submenu = document.getElementById('submenu-' + menuId);
        const chevron = document.getElementById('chevron-' + menuId);

        if (submenu.classList.contains('hidden')) {
            submenu.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            submenu.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }
</script>
