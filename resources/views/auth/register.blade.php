@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
    <div class="max-w-md mx-auto mt-16 bg-moto-card p-8 rounded-xl border border-neutral-700 shadow-lg">
        <h1 class="text-2xl font-semibold text-moto-orange mb-6">Регистрация</h1>

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm text-moto-muted mb-1">Имя</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                    class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                @error('name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm text-moto-muted mb-1">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                @error('email')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm text-moto-muted mb-1">Пароль</label>
                <input id="password" type="password" name="password" required
                    class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm text-moto-muted mb-1">Подтвердите пароль</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required
                    class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
            </div>

            <button type="submit" class="w-full bg-moto-orange hover:bg-moto-orange-dark text-white font-medium py-2 rounded transition">
                Зарегистрироваться
            </button>
        </form>

        <p class="mt-4 text-sm text-center text-moto-muted">
            Уже есть аккаунт? <a href="{{ route('login') }}" class="text-moto-orange hover:underline">Войти</a>
        </p>
    </div>
@endsection
