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

<body class="font-sans bg-gray-100">
    <div class="min-h-screen w-screen overflow-x-hidden">
        {{-- Header Hijau Full Width --}}
        <header class="bg-green-700 flex items-center justify-between px-6 py-3 w-full">
            <div class="flex items-center gap-3">
                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fa fa-mosque text-green-700 text-sm"></i>
                </div>
                <span class="text-white font-bold text-lg">SiPayPesantren</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fa fa-bell text-green-700"></i>
                </div>
                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fa fa-user text-green-700"></i>
                </div>
                <span class="text-white font-semibold">{{ Auth::check() ? Auth::user()->name : 'USER' }}</span>
            </div>
        </header>

        {{-- Container untuk Sidebar + Content --}}
        <div class="flex">
            {{-- Sidebar --}}
            @include('components.sidebar-santri')

            {{-- Main Content Area --}}
            <div class="flex-1 flex flex-col">
                {{-- Breadcrumb Bar --}}
                <div class="bg-white border-b px-6 py-3 flex items-center gap-2">
                    <i class="fa fa-bars text-gray-600"></i>
                    <i class="fa fa-home text-gray-600"></i>
                    <span class="text-gray-600">Home</span>
                    <i class="fa fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="font-semibold text-gray-800">Dashboard</span>
                </div>

                {{-- Content --}}
                <div class="p-6 bg-gray-100 flex-1">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript untuk Toggle Submenu --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const submenuToggle = document.getElementById('submenu-toggle');
            const submenuContent = document.getElementById('submenu-content');
            const chevronIcon = document.getElementById('chevron-icon');

            submenuToggle.addEventListener('click', function() {
                submenuContent.classList.toggle('hidden');
                chevronIcon.classList.toggle('fa-chevron-down');
                chevronIcon.classList.toggle('fa-chevron-up');
            });
        });
    </script>
</body>

</html>
