@extends('layouts.app')

@section('title', 'Изменить арендатора')

@section('content')
    <h1 class="text-2xl font-semibold text-moto-orange mb-6">Изменить арендатора</h1>

    <form method="POST" action="{{ route('renters.update', $renter) }}" class="max-w-xl bg-moto-card p-6 rounded-xl border border-neutral-700 space-y-4">
        @csrf
        @method('PATCH')

        <div>
            <label class="block text-sm text-moto-muted mb-1">Имя / название</label>
            <input type="text" name="name" value="{{ old('name', $renter->name) }}" required
                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
            @error('name') <p class="text-red-400 text-sm">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-moto-muted mb-1">Комментарий</label>
            <textarea name="comment" rows="3"
                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">{{ old('comment', $renter->comment) }}</textarea>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('renters.index') }}" class="px-4 py-2 rounded bg-neutral-700 hover:bg-neutral-600 text-sm">Отмена</a>
            <button type="submit" class="px-4 py-2 rounded bg-moto-orange hover:bg-moto-orange-dark text-white text-sm font-medium">Сохранить</button>
        </div>
    </form>
@endsection
