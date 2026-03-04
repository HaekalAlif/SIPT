{{-- filepath: resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('content')
    <style>
        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        body {
            position: fixed;
            width: 100vw;
            height: 100vh;
        }
    </style>
    <div class="w-screen h-screen flex flex-col bg-white">
        <!-- Header -->
        <div class="bg-green-700 py-3 px-6 flex justify-between items-center">
            <div>
                <p class="text-white font-bold text-sm leading-tight">
                    Ponpes Darul Ulum Sumber Gede 56A<br>
                    Kec. Sekampung, Kabupaten Lampung Timur, Lampung 34382
                </p>
            </div>
            <div class="flex gap-7">
                <!-- Social Icons -->
                <a href="#" class="text-white text-2xl"><i class="fab fa-tiktok"></i></a>
                <a href="#" class="text-white text-2xl"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-white text-2xl"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <!-- Main Content -->
        <div class="flex flex-1 h-full">
            <!-- Left Image 40% -->
            <div class="w-[40%] h-full hidden md:block">
                <img src="/images/image.png" alt="Pondok Pesantren" class="object-cover w-full h-full" />
            </div>
            <!-- Right Form 60% -->
            <div class="w-full md:w-[60%] flex items-center justify-center h-full">
                <div class="max-w-xl w-full px-8 py-6">
                    <div class="flex gap-x-4 mb-6">
                        <span>
                            <img src="/logo/logo.png" alt="Logo" class="w-20 h-20 mb-2" />
                        </span>
                        <div class="flex flex-col justify-end">
                            <h1 class="text-4xl font-bold text-[#846D6D] text-left">SISTEM PEMBAYARAN</h1>
                            <h2 class="text-xl font-bold text-[#846D6D] text-left">Pondok Pesantren Darul ‘Ulum</h2>
                        </div>
                    </div>
                    <p class="font-semibold text-[#846D6D] mb-4">
                        Login Menggunakan Email dan Password
                    </p>
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-[#846D6D]">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="block font-semibold text-[#846D6D] mb-1">Email</label>
                            <input id="email" name="email" type="email" required autofocus
                                class="w-full border border-[#846D6D] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#846D6D]"
                                placeholder="Email" value="{{ old('email') }}">
                            @error('email')
                                <span class="text-red-500 text-xs mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Password -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center">
                                <label for="password" class="block font-semibold text-[#846D6D] mb-1">Password</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                        class="text-[#846D6D] font-semibold underline text-sm">Lupa Password?</a>
                                @endif
                            </div>
                            <input id="password" name="password" type="password" required
                                class="w-full border border-[#846D6D] rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#846D6D]"
                                placeholder="Password">
                            @error('password')
                                <span class="text-red-500 text-xs mt-2">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Remember Me -->
                        <div class="flex items-center mb-6">
                            <input id="remember_me" type="checkbox" name="remember"
                                class="rounded border-gray-300 text-[#846D6D] shadow-sm focus:ring-[#846D6D]">
                            <label for="remember_me" class="ml-2 text-sm text-[#846D6D] font-semibold">Remember Me</label>
                        </div>
                        <!-- Sign In Button -->
                        <button type="submit"
                            class="w-full bg-green-700 text-white font-bold py-2 rounded hover:bg-green-800 transition">
                            Sign in
                        </button>
                        <div class="mt-4 text-center font-semibold text-[#846D6D]">
                            Belum Memiliki Akun Login?
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
