@extends('layouts.app')

@section('title', 'Мотоциклы')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-moto-orange">Мотоциклы</h1>
        <a href="{{ route('motorcycles.create') }}"
            class="bg-moto-orange hover:bg-moto-orange-dark text-white px-4 py-2 rounded text-sm font-medium">Добавить</a>
    </div>

    <div class="overflow-x-auto bg-moto-card border border-neutral-700 rounded-xl">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-neutral-800 text-moto-muted">
                <tr>
                    <th class="px-4 py-3 font-medium">Название</th>
                    <th class="px-4 py-3 font-medium">Slug</th>
                    <th class="px-4 py-3 font-medium">Год</th>
                    <th class="px-4 py-3 font-medium">Госномер</th>
                    <th class="px-4 py-3 font-medium">Статус</th>
                    <th class="px-4 py-3 font-medium">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-700">
                @forelse ($motorcycles as $motorcycle)
                    <tr class="hover:bg-neutral-800/50 @if ($motorcycle->trashed()) opacity-60 @endif">
                        <td class="px-4 py-3">{{ $motorcycle->name }}</td>
                        <td class="px-4 py-3 text-moto-muted">{{ $motorcycle->slug }}</td>
                        <td class="px-4 py-3">{{ $motorcycle->year }}</td>
                        <td class="px-4 py-3">{{ $motorcycle->state_number }}</td>
                        <td class="px-4 py-3">
                            @if ($motorcycle->currentStatus()->value === 'free')
                                <span class="text-green-400">Свободен</span>
                            @else
                                <span class="text-red-400">В аренде</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('motorcycles.edit', $motorcycle) }}"
                                class="text-moto-orange hover:underline text-xs">Изм.</a>
                            @if ($motorcycle->trashed())
                                <form method="POST" action="{{ route('motorcycles.restore', $motorcycle->id) }}"
                                    class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-400 hover:underline text-xs">Восст.</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('motorcycles.destroy', $motorcycle) }}" class="inline"
                                    onsubmit="return confirm('Удалить?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:underline text-xs">Удал.</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-moto-muted">Нет мотоциклов</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
