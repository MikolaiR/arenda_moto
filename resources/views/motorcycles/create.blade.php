@extends('layouts.app')

@section('title', 'Добавить мотоцикл')

@section('content')
    <h1 class="text-2xl font-semibold text-moto-orange mb-6">Добавить мотоцикл</h1>

    <form method="POST" action="{{ route('motorcycles.store') }}"
        class="max-w-xl bg-moto-card p-6 rounded-xl border border-neutral-700 space-y-4">
        @csrf

        <div>
            <label class="block text-sm text-moto-muted mb-1">Название</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
            @error('name')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm text-moto-muted mb-1">Год</label>
            <input type="number" name="year" value="{{ old('year') }}" required
                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
            @error('year')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm text-moto-muted mb-1">Госномер</label>
            <input type="text" name="state_number" value="{{ old('state_number') }}"
                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
            @error('state_number')
                <p class="text-red-400 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm text-moto-muted mb-1">Комментарий</label>
            <textarea name="comment" rows="3"
                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">{{ old('comment') }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('motorcycles.index') }}"
                class="px-4 py-2 rounded bg-neutral-700 hover:bg-neutral-600 text-sm">Отмена</a>
            <button type="submit"
                class="px-4 py-2 rounded bg-moto-orange hover:bg-moto-orange-dark text-white text-sm font-medium">Сохранить</button>
        </div>
    </form>
@endsection
