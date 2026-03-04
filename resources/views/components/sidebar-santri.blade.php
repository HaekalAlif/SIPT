{{-- filepath: resources/views/components/sidebar-santri.blade.php --}}
<aside class="w-64 bg-green-800 text-white flex flex-col min-h-screen">
    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-3">
        <!-- Dashboard - Active State -->
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded bg-green-700 text-green-200 font-semibold">
            <i class="fa fa-tachometer-alt w-5"></i>
            <span>Dashboard</span>
        </a>

        <!-- Profil -->
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-green-700 transition">
            <i class="fa fa-user w-5"></i>
            <span>Profil</span>
        </a>

        <!-- Pembayaran with Toggle Submenu -->
        <div class="space-y-2">
            <div id="submenu-toggle"
                class="flex items-center gap-3 px-3 py-2 rounded hover:bg-green-700 transition cursor-pointer">
                <i class="fa fa-credit-card w-5"></i>
                <span class="flex-1">Pembayaran</span>
                <i id="chevron-icon" class="fa fa-chevron-down text-xs"></i>
            </div>
            <div id="submenu-content" class="ml-8 space-y-1 hidden">
                <a href="#"
                    class="flex items-center gap-2 px-3 py-1 text-sm text-green-200 hover:text-white transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Tagihan pembayaran</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2 px-3 py-1 text-sm text-green-200 hover:text-white transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Registrasi</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2 px-3 py-1 text-sm text-green-200 hover:text-white transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Syariah</span>
                </a>
                <a href="#"
                    class="flex items-center gap-2 px-3 py-1 text-sm text-green-200 hover:text-white transition">
                    <i class="fa fa-angle-right text-xs"></i>
                    <span>Tagihan lainnya</span>
                </a>
            </div>
        </div>

        <!-- Laporan -->
        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-green-700 transition">
            <i class="fa fa-file-alt w-5"></i>
            <span>Laporan</span>
        </a>
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
