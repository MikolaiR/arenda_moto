@extends('layouts.app')

@section('title', 'Вход')

@section('content')
    <div class="max-w-md mx-auto mt-16 bg-moto-card p-8 rounded-xl border border-neutral-700 shadow-lg">
        <h1 class="text-2xl font-semibold text-moto-orange mb-6">Вход в систему</h1>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm text-moto-muted mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm text-moto-muted mb-1">Пароль</label>
                <input id="password" type="password" name="password" required
                    class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
            </div>

            <div class="flex items-center gap-2">
                <input id="remember" type="checkbox" name="remember" class="rounded bg-neutral-800 border-neutral-600 text-moto-orange focus:ring-moto-orange">
                <label for="remember" class="text-sm text-moto-muted">Запомнить меня</label>
            </div>

            <button type="submit" class="w-full bg-moto-orange hover:bg-moto-orange-dark text-white font-medium py-2 rounded transition">
                Войти
            </button>
        </form>

        <p class="mt-4 text-sm text-center text-moto-muted">
            Нет аккаунта? <a href="{{ route('register') }}" class="text-moto-orange hover:underline">Зарегистрироваться</a>
        </p>
    </div>
@endsection
