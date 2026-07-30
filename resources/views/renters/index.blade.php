@extends('layouts.app')

@section('title', 'Арендаторы')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-moto-orange">Арендаторы</h1>
        <a href="{{ route('renters.create') }}" class="bg-moto-orange hover:bg-moto-orange-dark text-white px-4 py-2 rounded text-sm font-medium">Добавить</a>
    </div>

    <div class="overflow-x-auto bg-moto-card border border-neutral-700 rounded-xl">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-neutral-800 text-moto-muted">
                <tr>
                    <th class="px-4 py-3 font-medium">Имя</th>
                    <th class="px-4 py-3 font-medium">Комментарий</th>
                    <th class="px-4 py-3 font-medium">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-neutral-700">
                @forelse ($renters as $renter)
                    <tr class="hover:bg-neutral-800/50 @if($renter->trashed()) opacity-60 @endif">
                        <td class="px-4 py-3">{{ $renter->name }}</td>
                        <td class="px-4 py-3 text-moto-muted">{{ $renter->comment }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('renters.edit', $renter) }}" class="text-moto-orange hover:underline text-xs">Изм.</a>
                            @if ($renter->trashed())
                                <form method="POST" action="{{ route('renters.restore', $renter->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-400 hover:underline text-xs">Восст.</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('renters.destroy', $renter) }}" class="inline" onsubmit="return confirm('Удалить?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:underline text-xs">Удал.</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-moto-muted">Нет арендаторов</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
