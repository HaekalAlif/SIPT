{{-- filepath: resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SiPayPesantren</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @stack('styles')
</head>

<body class="font-sans bg-gray-100 h-screen overflow-hidden">
    <div class="h-screen flex flex-col">
        {{-- Header Green Full Width --}}
        <header class="bg-green-700 flex items-center justify-between px-4 md:px-6 py-3 w-full flex-shrink-0 z-[60]">
            <div class="flex items-center gap-3">
                {{-- Tombol Menu (Muncul di HP & Desktop untuk Toggle) --}}
                <button id="btn-toggle-sidebar" class="text-white block focus:outline-none hover:bg-green-800 p-2 rounded-lg transition-colors">
                    <i class="fa fa-bars text-xl"></i>
                </button>

                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fa fa-user-shield text-green-700 text-sm"></i>
                </div>
                <span class="text-white font-bold text-lg hidden md:inline">SiPayPesantren - Admin</span>
                <span class="text-white font-bold text-lg inline md:hidden">SiPay</span>
            </div>
            
            <div class="flex items-center gap-2 md:gap-4">
                {{-- Ikon Lonceng telah dihapus di sini --}}
                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer">
                    <i class="fa fa-user text-green-700"></i>
                </div>
                <span class="text-white font-semibold hidden sm:inline">
                    {{ Auth::check() ? Auth::user()->nama_santri : 'ADMIN' }}
                </span>
            </div>
        </header>

        {{-- Container untuk Sidebar + Content --}}
        <div class="flex flex-1 overflow-hidden relative">
            
            {{-- SIDEBAR --}}
            <aside id="sidebar-asli" 
                class="bg-white border-r flex-shrink-0 transition-all duration-300 z-50 
                        w-64 absolute md:relative h-full overflow-y-auto">
                @include('components.sidebar-admin')
            </aside>

            {{-- Overlay untuk HP saat sidebar terbuka --}}
            <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

            {{-- Main Content Area --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                {{-- BAGIAN BREADCRUMB (HOME > ADMIN DASHBOARD) SUDAH DIHAPUS --}}

                {{-- Content --}}
                <div class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript Logic --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('btn-toggle-sidebar');
            const sidebar = document.getElementById('sidebar-asli');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
            }

            btn.addEventListener('click', function() {
                if (window.innerWidth >= 768) {
                    if (sidebar.classList.contains('w-64')) {
                        sidebar.classList.remove('w-64');
                        sidebar.classList.add('w-0', 'opacity-0', 'invisible');
                    } else {
                        sidebar.classList.remove('w-0', 'opacity-0', 'invisible');
                        sidebar.classList.add('w-64');
                    }
                } else {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.remove('-translate-x-full');
                        overlay.classList.remove('hidden');
                    } else {
                        sidebar.classList.add('-translate-x-full');
                        overlay.classList.add('hidden');
                    }
                }
            });

            overlay.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('w-64');
                    overlay.classList.add('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    sidebar.classList.remove('w-0', 'opacity-0', 'invisible');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>