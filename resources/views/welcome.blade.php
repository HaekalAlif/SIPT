{{-- filepath: resources/views/home.blade.php --}}
@extends('layouts.app')

@section('content')
    <style>
        html,
        body {
            overflow-x: hidden;
        }
    </style>
    <div class="w-screen min-h-screen flex flex-col bg-white">
        <!-- Header -->
        <div class="bg-green-700 py-3 px-6 flex justify-between items-center">
            <div>
                <p class="text-white font-bold text-sm leading-tight">
                    Ponpes Darul Ulum Sumber Gede 56A<br>
                    Kec. Sekampung, Kabupaten Lampung Timur, Lampung 34382
                </p>
            </div>
            <div class="flex gap-4">
                <a href="#" class="text-white text-2xl"><i class="fab fa-tiktok"></i></a>
                <a href="#" class="text-white text-2xl"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-white text-2xl"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <!-- Navbar -->
        <div class="bg-white py-2 px-6 flex justify-between items-center border-b border-gray-200">
            <div class="text-2xl italic font-semibold text-[#222]">SiPayPesantren</div>
            <div class="flex gap-8 items-center">
                <a href="{{ url('/') }}"
                    class="flex items-center gap-2 text-lg font-semibold text-[#222] hover:text-green-700">
                    <i class="fas fa-home"></i> Home
                </a>
                @auth
                    <a href="{{ route('logout') }}"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="flex items-center gap-2 text-lg font-semibold text-[#222] hover:text-green-700">
                        <i class="fas fa-user"></i> Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="flex items-center gap-2 text-lg font-semibold text-[#222] hover:text-green-700">
                        <i class="fas fa-user"></i> Login
                    </a>
                @endauth
            </div>
        </div>
        <!-- Hero Section -->
        <div class="relative flex-1 flex items-center justify-start overflow-hidden"
            style="background: url('/images/bg-home.jpg') center center / cover no-repeat;">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <div class="relative z-10 px-8 md:px-24 py-16">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-2 drop-shadow-lg">
                    Sistem Pembayaran Pesantren
                </h1>
                <h2 class="text-2xl md:text-2xl font-semibold text-white mb-8 drop-shadow-lg">
                    Pondok Pesantren Darul ‘Ulum
                </h2>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center gap-2 bg-green-700 hover:bg-green-800 text-white font-bold px-6 py-3 rounded text-lg shadow transition">
                    <i class="fas fa-angle-double-right"></i> Login
                </a>
            </div>
        </div>
    </div>
@endsection
