{{-- filepath: resources/views/layouts/santri.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | SiPayPesantren</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="font-sans bg-gray-100 h-screen overflow-hidden">
    <div class="h-screen flex flex-col">
        {{-- Header Hijau Full Width --}}
        <header class="bg-green-700 flex items-center justify-between px-6 py-3 w-full flex-shrink-0">
            <div class="flex items-center gap-3">
                {{-- TOMBOL HAMBURGER --}}
                <button id="btn-hamburger-santri" class="text-white block focus:outline-none mr-2">
                    <i class="fa fa-bars text-xl"></i>
                </button>

                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fa fa-mosque text-green-700 text-sm"></i>
                </div>
                <span class="text-white font-bold text-lg hidden md:inline">SiPayPesantren</span>
                <span class="text-white font-bold text-lg inline md:hidden">SiPay</span>
            </div>
            <div class="flex items-center gap-4">
                {{-- Ikon lonceng telah dihapus dari sini --}}
                @if (Auth::check() && Auth::user()->foto_profile)
                    <img src="{{ Storage::url(Auth::user()->foto_profile) }}" alt="Foto Profil"
                        class="w-8 h-8 rounded-full object-cover">
                @else
                    <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                        <i class="fa fa-user text-green-700"></i>
                    </div>
                @endif
                <span
                    class="text-white font-semibold hidden sm:inline">{{ Auth::check() ? Auth::user()->nama_santri : 'USER' }}</span>
            </div>
        </header>

        {{-- Container untuk Sidebar + Content --}}
        <div class="flex flex-1 overflow-hidden relative">
            <aside id="sidebar-santri-asli"
                class="w-64 bg-white border-r flex-shrink-0 transition-all duration-300 z-50 absolute md:relative h-full">
                @include('components.sidebar-santri')
            </aside>

            {{-- Main Content Area --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                {{-- BAGIAN HOME DASHBOARD SUDAH DIHAPUS DARI SINI --}}

                {{-- Content --}}
                <div class="flex-1 overflow-y-auto p-6 bg-gray-100">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submenuToggle = document.getElementById('submenu-toggle');
            const submenuContent = document.getElementById('submenu-content');
            const chevronIcon = document.getElementById('chevron-icon');

            if (submenuToggle) {
                submenuToggle.addEventListener('click', function() {
                    submenuContent.classList.toggle('hidden');
                    chevronIcon.classList.toggle('fa-chevron-down');
                    chevronIcon.classList.toggle('fa-chevron-up');
                });
            }

            const btnHam = document.getElementById('btn-hamburger-santri');
            const sidebar = document.getElementById('sidebar-santri-asli');

            btnHam.addEventListener('click', function() {
                sidebar.classList.toggle('hidden');
                if (!sidebar.classList.contains('hidden') && window.innerWidth < 768) {
                    sidebar.classList.add('shadow-2xl');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>
