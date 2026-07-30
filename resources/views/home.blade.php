@extends('layouts.app')

@section('title', 'Мотоциклы в аренде')

@section('content')
    <div x-data="{ modal: { open: false, id: null, name: '', bookings: [] } }" @keydown.escape.window="modal.open = false">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold text-moto-orange">Парк мотоциклов</h1>
            @hasanyrole(['admin', 'manager'])
                <div class="flex gap-3">
                    <a href="{{ route('renters.index') }}"
                        class="bg-neutral-700 hover:bg-neutral-600 px-4 py-2 rounded text-sm">Арендаторы</a>
                    <a href="{{ route('motorcycles.index') }}"
                        class="bg-moto-orange hover:bg-moto-orange-dark px-4 py-2 rounded text-sm text-white">Мотоциклы</a>
                </div>
            @endhasanyrole
        </div>

        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded bg-green-900/40 text-green-400 border border-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded bg-red-900/40 text-red-400 border border-red-800 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($motorcycles as $motorcycle)
                <div class="bg-moto-card rounded-xl border border-neutral-700 p-5 shadow-md flex flex-col justify-between"
                    :class="{ 'opacity-60': modal.open }">
                    <div>
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h2 class="text-lg font-semibold text-moto-text">{{ $motorcycle->name }}</h2>
                                <p class="text-sm text-moto-muted">{{ $motorcycle->year }} г.</p>
                            </div>
                            @php($status = $motorcycle->currentStatus())
                            <span @class([
                                'px-2 py-1 rounded text-xs font-medium',
                                'bg-green-900/40 text-green-400 border border-green-800' =>
                                    $status->value === 'free',
                                'bg-red-900/40 text-red-400 border border-red-800' =>
                                    $status->value === 'rented',
                            ])>
                                {{ $status->value === 'free' ? 'Свободен' : 'В аренде' }}
                            </span>
                        </div>

                        @if ($motorcycle->activeRental)
                            <div class="text-sm text-moto-muted mb-3">
                                <p>Арендатор: <span
                                        class="text-moto-text">{{ $motorcycle->activeRental->renter->name }}</span></p>
                                <p>С: {{ $motorcycle->activeRental->started_at->format('d.m.Y H:i') }}</p>
                                @if ($motorcycle->activeRental->ended_at)
                                    <p>По: {{ $motorcycle->activeRental->ended_at->format('d.m.Y H:i') }}</p>
                                @endif
                            </div>
                        @elseif ($motorcycle->upcomingRental)
                            <div class="text-sm text-moto-muted mb-3">
                                <p>Следующая бронь: <span
                                        class="text-moto-text">{{ $motorcycle->upcomingRental->renter->name }}</span></p>
                                <p>С: {{ $motorcycle->upcomingRental->started_at->format('d.m.Y H:i') }}</p>
                                @if ($motorcycle->upcomingRental->ended_at)
                                    <p>По: {{ $motorcycle->upcomingRental->ended_at->format('d.m.Y H:i') }}</p>
                                @endif
                            </div>
                        @endif

                        @if ($motorcycle->comment)
                            <p class="text-sm text-moto-muted mb-3 line-clamp-2">{{ $motorcycle->comment }}</p>
                        @endif
                    </div>

                    @hasanyrole(['admin', 'manager'])
                        <div class="flex gap-2 mt-4">
                            @php(
    $bookings = $motorcycle->rentals->map(
            fn($r) => [
                'renter' => $r->renter?->name ?? '—',
                'started_at' => $r->started_at->format('d.m.Y H:i'),
                'ended_at' => $r->ended_at?->format('d.m.Y H:i') ?? '—'
            ]
        )->toArray()
)
                            <button data-bookings='@json($bookings)'
                                @click="modal.open = true; modal.id = {{ $motorcycle->id }}; modal.name = '{{ $motorcycle->name }}'; modal.bookings = JSON.parse($event.currentTarget.dataset.bookings)"
                                class="flex-1 bg-moto-orange hover:bg-moto-orange-dark text-white py-2 rounded text-sm font-medium transition">
                                Зарезервировать
                            </button>

                            @if ($motorcycle->activeRental)
                                <form method="POST" :action="'/rentals/' + {{ $motorcycle->activeRental->id }}"
                                    class="contents" onsubmit="return confirm('Завершить аренду?')">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="free">
                                    <input type="hidden" name="started_at"
                                        value="{{ $motorcycle->activeRental->started_at?->format('Y-m-d\TH:i') }}">
                                    <button type="submit"
                                        class="flex-1 bg-neutral-700 hover:bg-neutral-600 py-2 rounded text-sm font-medium transition">
                                        Завершить
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('motorcycles.edit', $motorcycle) }}"
                                class="px-3 py-2 bg-neutral-700 hover:bg-neutral-600 rounded text-sm">Изм.</a>
                        </div>
                    @endhasanyrole
                </div>
            @empty
                <p class="text-moto-muted col-span-full">Мотоциклов пока нет.</p>
            @endforelse
        </div>

        <!-- Modal -->
        <div x-show="modal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
            <div class="absolute inset-0 bg-black/70" @click="modal.open = false"></div>
            <div class="relative bg-moto-card w-full max-w-lg rounded-xl border border-neutral-700 p-6 shadow-2xl">
                <h2 class="text-xl font-semibold text-moto-orange mb-1" x-text="modal.name"></h2>
                <p class="text-sm text-moto-muted mb-5">Новая аренда</p>

                <form method="POST" :action="'/motorcycles/' + modal.id + '/rentals'" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm text-moto-muted mb-1">Арендатор</label>
                        <select name="renter_id" required
                            class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                            <option value="">Выберите арендатора</option>
                            @foreach ($renters as $renter)
                                <option value="{{ $renter->id }}">{{ $renter->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-moto-muted mb-1">Дата с</label>
                            <input type="datetime-local" name="started_at" required
                                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm text-moto-muted mb-1">Дата по</label>
                            <input type="datetime-local" name="ended_at" required
                                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-moto-muted mb-1">Сумма аренды, BYN</label>
                        <input type="number" step="0.01" min="0" name="total_amount_byn"
                            class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm text-moto-muted mb-1">Комментарий</label>
                        <textarea name="comment" rows="3"
                            class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none"></textarea>
                    </div>

                    <div x-show="modal.bookings.length" class="space-y-1">
                        <p class="text-sm text-moto-muted">Существующие аренды:</p>
                        <ul class="text-sm">
                            <template x-for="booking in modal.bookings" :key="booking.started_at">
                                <li class="text-moto-text mb-1"
                                    x-text="booking.renter + ' — ' + booking.started_at + ' / ' + booking.ended_at"></li>
                            </template>
                        </ul>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="modal.open = false"
                            class="px-4 py-2 rounded bg-neutral-700 hover:bg-neutral-600 text-sm">Отмена</button>
                        <button type="submit"
                            class="px-4 py-2 rounded bg-moto-orange hover:bg-moto-orange-dark text-white text-sm font-medium">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
