{{-- filepath: resources/views/layouts/admin.blade.php --}}
<!DOCTYPE html>
<html lang="en">

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
        <header class="bg-green-700 flex items-center justify-between px-6 py-3 w-full flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fa fa-user-shield text-green-700 text-sm"></i>
                </div>
                <span class="text-white font-bold text-lg">SiPayPesantren - Admin</span>
            </div>
            <div class="flex items-center gap-4">
                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fa fa-bell text-green-700"></i>
                </div>
                <div class="bg-white rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="fa fa-user text-green-700"></i>
                </div>
                <span class="text-white font-semibold">{{ Auth::check() ? Auth::user()->nama_santri : 'ADMIN' }}</span>
            </div>
        </header>

        {{-- Container untuk Sidebar + Content --}}
        <div class="flex flex-1 overflow-hidden">
            {{-- Sidebar --}}
            @include('components.sidebar-admin')

            {{-- Main Content Area --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                {{-- Breadcrumb Bar --}}
                <div class="bg-white border-b px-6 py-3 flex items-center gap-2 flex-shrink-0">
                    <i class="fa fa-bars text-gray-600"></i>
                    <i class="fa fa-home text-gray-600"></i>
                    <span class="text-gray-600">Home</span>
                    <i class="fa fa-chevron-right text-gray-400 text-xs"></i>
                    <span class="font-semibold text-gray-800">Admin Dashboard</span>
                </div>

                {{-- Content --}}
                <div class="flex-1 overflow-y-auto p-6">
                    @yield('content')
                </div>
            </div>
        </div>

</body>

</html>
