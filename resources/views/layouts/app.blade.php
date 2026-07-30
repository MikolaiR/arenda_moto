<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Учёт аренды мотоциклов')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-moto-bg text-moto-text min-h-screen font-sans antialiased" x-data="{ mobileMenu: false }">
    <nav class="bg-moto-card border-b border-neutral-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-moto-orange font-semibold text-xl">
                    <span>🏍️</span>
                    <span>MotoRent</span>
                </a>

                <div class="hidden sm:flex items-center gap-6">
                    @auth
                        <a href="{{ route('home') }}" class="hover:text-moto-orange transition">Главная</a>
                        @hasanyrole(['admin', 'manager'])
                            <a href="{{ route('motorcycles.index') }}" class="hover:text-moto-orange transition">Мотоциклы</a>
                            <a href="{{ route('renters.index') }}" class="hover:text-moto-orange transition">Арендаторы</a>
                        @endhasanyrole
                        <span class="text-moto-muted text-sm">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-moto-orange hover:text-moto-orange-dark text-sm">Выйти</button>
                        </form>
                    @endauth
                    @guest
                        <a href="{{ route('login') }}" class="hover:text-moto-orange transition">Вход</a>
                        <a href="{{ route('register') }}" class="hover:text-moto-orange transition">Регистрация</a>
                    @endguest
                </div>

                <button @click="mobileMenu = !mobileMenu" class="sm:hidden text-moto-orange">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
        </div>

        <div x-show="mobileMenu" class="sm:hidden border-t border-neutral-700" style="display: none;">
            <div class="px-4 pt-2 pb-4 space-y-2">
                @auth
                    <a href="{{ route('home') }}" class="block py-2 hover:text-moto-orange">Главная</a>
                    @hasanyrole(['admin', 'manager'])
                        <a href="{{ route('motorcycles.index') }}" class="block py-2 hover:text-moto-orange">Мотоциклы</a>
                        <a href="{{ route('renters.index') }}" class="block py-2 hover:text-moto-orange">Арендаторы</a>
                    @endhasanyrole
                    <form method="POST" action="{{ route('logout') }}" class="py-2">
                        @csrf
                        <button type="submit" class="text-moto-orange">Выйти</button>
                    </form>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="block py-2 hover:text-moto-orange">Вход</a>
                    <a href="{{ route('register') }}" class="block py-2 hover:text-moto-orange">Регистрация</a>
                @endguest
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="mb-6 p-4 rounded bg-green-900/30 text-green-400 border border-green-800">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
