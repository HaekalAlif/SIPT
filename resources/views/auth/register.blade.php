@extends('layouts.app')

@section('content')
    <style>
        html,
        body {
            height: 100vh;
            margin: 0;
            padding: 0;
            overflow: hidden !important;
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
            <div class="flex gap-7">
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
                <a href="{{ route('login') }}"
                    class="flex items-center gap-2 text-lg font-semibold text-[#222] hover:text-green-700">
                    <i class="fas fa-user"></i> Login
                </a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="relative flex-1 flex items-center justify-center"
            style="background: url('/images/image.png') center center / cover no-repeat;">
            <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            <div class="relative z-10 w-full flex items-center justify-center py-16">
                <div class="bg-white bg-opacity-90 rounded-lg shadow-lg max-w-md w-full px-8 py-8">
                    <h2 class="text-2xl font-bold text-center mb-6 text-[#222]">DAFTAR AKUN</h2>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <!-- Nama Santri -->
                        <div class="mb-4">
                            <label for="nama_santri" class="block font-semibold text-[#222] mb-1">Nama Santri</label>
                            <input id="nama_santri" name="nama_santri" type="text" required autofocus
                                class="w-full border border-[#846D6D] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#846D6D]"
                                placeholder="Nama Santri" value="{{ old('nama_santri') }}">
                            @error('nama_santri')
                                <span class="text-red-500 text-xs mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Nis Santri (Email) -->
                        <div class="mb-4">
                            <label for="email" class="block font-semibold text-[#222] mb-1">Email santri</label>
                            <input id="email" name="email" type="email" required autocomplete="username"
                                class="w-full border border-[#846D6D] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#846D6D]"
                                placeholder="Email santri" value="{{ old('email') }}">
                            @error('email')
                                <span class="text-red-500 text-xs mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="block font-semibold text-[#222] mb-1">Password</label>
                            <input id="password" name="password" type="password" required autocomplete="new-password"
                                class="w-full border border-[#846D6D] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#846D6D]"
                                placeholder="Password">
                            @error('password')
                                <span class="text-red-500 text-xs mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Confirm Password -->
                        <div class="mb-6">
                            <label for="password_confirmation" class="block font-semibold text-[#222] mb-1">Konfirmasi
                                Password</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                autocomplete="new-password"
                                class="w-full border border-[#846D6D] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#846D6D]"
                                placeholder="Konfirmasi Password">
                            @error('password_confirmation')
                                <span class="text-red-500 text-xs mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit"
                            class="w-full bg-green-700 text-white font-bold py-2 rounded hover:bg-green-800 transition">
                            Daftar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
