@extends('layouts.app')

@section('title', 'Мотоциклы в аренде')

@section('content')
    @php
        $motos = $motorcycles
            ->map(
                fn(\App\Models\Motorcycle $m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'year' => $m->year,
                    'state_number' => $m->state_number,
                    'comment' => $m->comment,
                    'status' => $m->currentStatus()->value,
                    'active' => $m->activeRental
                        ? [
                            'id' => $m->activeRental->id,
                            'renter' => $m->activeRental->renter?->name,
                            'started_at' => $m->activeRental->started_at->format('d.m.Y H:i'),
                            'started_at_input' => $m->activeRental->started_at->format('Y-m-d\TH:i'),
                            'ended_at' => $m->activeRental->ended_at?->format('d.m.Y H:i') ?? null,
                        ]
                        : null,
                    'upcoming' => $m->upcomingRental
                        ? [
                            'id' => $m->upcomingRental->id,
                            'renter' => $m->upcomingRental->renter?->name,
                            'started_at' => $m->upcomingRental->started_at->format('d.m.Y H:i'),
                            'ended_at' => $m->upcomingRental->ended_at?->format('d.m.Y H:i') ?? null,
                        ]
                        : null,
                    'rentals' => $m->rentals
                        ->map(
                            fn(\App\Models\Rental $r) => [
                                'id' => $r->id,
                                'renter_id' => $r->renter_id,
                                'renter' => $r->renter?->name,
                                'started_at' => $r->started_at->format('d.m.Y H:i'),
                                'started_at_input' => $r->started_at->format('Y-m-d\TH:i'),
                                'ended_at' => $r->ended_at?->format('d.m.Y H:i') ?? '—',
                                'ended_at_input' => $r->ended_at?->format('Y-m-d\TH:i') ?? '',
                                'total_amount_byn' => $r->total_amount_byn,
                                'comment' => $r->comment ?? '',
                            ],
                        )
                        ->values()
                        ->toArray(),
                ],
            )
            ->values()
            ->toArray();

        $rentals = $activeRentals
            ->map(
                fn(\App\Models\Rental $r) => [
                    'id' => $r->id,
                    'motorcycle' => $r->motorcycle?->name,
                    'renter' => $r->renter?->name,
                    'started_at' => $r->started_at->format('d.m.Y H:i'),
                    'ended_at' => $r->ended_at?->format('d.m.Y H:i') ?? '—',
                    'total_amount_byn' => $r->total_amount_byn,
                    'comment' => $r->comment,
                ],
            )
            ->values()
            ->toArray();
    @endphp

    <script>
        window.homeData = {!! json_encode(
            ['motorcycles' => $motos, 'activeRentals' => $rentals],
            JSON_HEX_TAG | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
        ) !!};

        window.homeApp = () => ({
            view: localStorage.getItem('homeView') || 'list',
            search: '',
            selected: null,
            modal: {
                open: false,
                id: null,
                name: '',
                bookings: []
            },
            editModal: {
                open: false,
                rental: {
                    id: null,
                    renter_id: '',
                    started_at_input: '',
                    ended_at_input: '',
                    total_amount_byn: '',
                    comment: '',
                },
            },
            motorcycles: window.homeData.motorcycles,
            activeRentals: window.homeData.activeRentals,
            get filteredMotorcycles() {
                const s = this.search.toLowerCase();
                return this.motorcycles.filter(m =>
                    m.name.toLowerCase().includes(s) ||
                    (m.state_number || '').toLowerCase().includes(s) ||
                    (m.active?.renter || '').toLowerCase().includes(s) ||
                    (m.upcoming?.renter || '').toLowerCase().includes(s)
                );
            },
            get filteredRentals() {
                const s = this.search.toLowerCase();
                return this.activeRentals.filter(r =>
                    (r.motorcycle || '').toLowerCase().includes(s) ||
                    (r.renter || '').toLowerCase().includes(s)
                );
            },
            openReserve(m) {
                this.modal = {
                    open: true,
                    id: m.id,
                    name: m.name,
                    bookings: m.rentals
                };
            },
            openEditRental(rental) {
                this.editModal = {
                    open: true,
                    rental: {
                        ...rental
                    },
                };
            },
        });
    </script>

    <div x-data="homeApp()" x-init="$watch('view', v => localStorage.setItem('homeView', v))" @keydown.escape.window="modal.open = false; editModal.open = false">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
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

        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <input type="text" x-model="search" placeholder="Поиск по названию, арендатору, госномеру..."
                class="flex-1 bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
            <div class="inline-flex bg-moto-card rounded border border-neutral-700 p-1">
                <button @click="view = 'list'"
                    :class="view === 'list' ? 'bg-moto-orange text-white' : 'text-moto-muted'"
                    class="px-4 py-2 rounded text-sm transition">Список</button>
                <button @click="view = 'grid'"
                    :class="view === 'grid' ? 'bg-moto-orange text-white' : 'text-moto-muted'"
                    class="px-4 py-2 rounded text-sm transition">Сетка</button>
            </div>
        </div>

        <!-- List -->
        <div x-show="view === 'list'" class="bg-moto-card border border-neutral-700 rounded-xl overflow-hidden"
            style="display: none;">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-neutral-800 text-moto-muted">
                    <tr>
                        <th class="px-4 py-3 font-medium">Мотоцикл</th>
                        <th class="px-4 py-3 font-medium">Статус</th>
                        <th class="px-4 py-3 font-medium">Примечание</th>
                        <th class="px-4 py-3 font-medium">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-700">
                    <template x-for="m in filteredMotorcycles" :key="m.id">
                        <tr class="hover:bg-neutral-800/50" x-data="{ open: false }">
                            <td class="px-4 py-3">
                                <div class="font-medium text-moto-text" x-text="m.name"></div>
                                <div class="text-xs text-moto-muted"
                                    x-text="m.year + ' г.' + (m.state_number ? ' · ' + m.state_number : '')"></div>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    :class="m.status === 'free' ? 'bg-green-900/40 text-green-400 border border-green-800' :
                                        'bg-red-900/40 text-red-400 border border-red-800'"
                                    class="px-2 py-1 rounded text-xs font-medium"
                                    x-text="m.status === 'free' ? 'Свободен' : 'В аренде'"></span>
                                <div class="mt-2 text-xs text-moto-muted" x-show="m.active">
                                    <span x-text="'Арендатор: ' + m.active?.renter"></span><br>
                                    <span
                                        x-text="'С: ' + m.active?.started_at + (m.active?.ended_at ? ' по ' + m.active?.ended_at : '')"></span>
                                </div>
                                <div class="mt-2 text-xs text-moto-muted" x-show="m.upcoming && !m.active">
                                    <span x-text="'След. бронь: ' + m.upcoming?.renter"></span><br>
                                    <span
                                        x-text="'С: ' + m.upcoming?.started_at + (m.upcoming?.ended_at ? ' по ' + m.upcoming?.ended_at : '')"></span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-moto-muted" x-text="m.comment || ''"></td>
                            <td class="px-4 py-3">
                                @hasanyrole(['admin', 'manager'])
                                    <div class="flex flex-wrap gap-2">
                                        <button @click="openReserve(m)"
                                            class="px-3 py-1.5 bg-moto-orange hover:bg-moto-orange-dark text-white rounded text-xs font-medium transition">Зарезервировать</button>
                                        <button @click="open = !open"
                                            class="px-3 py-1.5 bg-neutral-800 hover:bg-neutral-700 border border-neutral-600 rounded text-xs transition"
                                            x-text="open ? 'Скрыть' : 'Брони'"></button>
                                    </div>
                                    <div x-show="open" class="mt-3 text-xs" x-cloak>
                                        <p class="font-medium mb-1 text-moto-muted">Все резервы:</p>
                                        <ul class="space-y-2">
                                            <template x-for="b in m.rentals" :key="b.id">
                                                <li
                                                    class="flex flex-wrap items-center justify-between gap-2 bg-neutral-800/50 border border-neutral-700 rounded px-3 py-2">
                                                    <span class="text-moto-text"
                                                        x-text="b.renter + ' — ' + b.started_at + ' / ' + b.ended_at"></span>
                                                    <div class="flex gap-2">
                                                        <button type="button" @click="openEditRental(b)"
                                                            class="px-2 py-1 bg-neutral-700 hover:bg-neutral-600 rounded text-xs transition">Изменить</button>
                                                        <form method="POST" :action="'/rentals/' + b.id" class="contents"
                                                            onsubmit="return confirm('Отменить резерв?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                class="px-2 py-1 bg-red-900/40 hover:bg-red-900/60 text-red-400 rounded text-xs transition">Отменить</button>
                                                        </form>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                @endhasanyrole
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            <p x-show="filteredMotorcycles.length === 0" class="px-4 py-6 text-center text-moto-muted">Нет мотоциклов.</p>
        </div>

        <!-- Grid -->
        <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" style="display: none;">
            <template x-for="m in filteredMotorcycles" :key="m.id">
                <div class="bg-moto-card rounded-xl border border-neutral-700 p-5 shadow-md flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h2 class="text-lg font-semibold text-moto-text" x-text="m.name"></h2>
                                <p class="text-sm text-moto-muted"
                                    x-text="m.year + ' г.' + (m.state_number ? ' · ' + m.state_number : '')"></p>
                            </div>
                            <span
                                :class="m.status === 'free' ? 'bg-green-900/40 text-green-400 border border-green-800' :
                                    'bg-red-900/40 text-red-400 border border-red-800'"
                                class="px-2 py-1 rounded text-xs font-medium"
                                x-text="m.status === 'free' ? 'Свободен' : 'В аренде'"></span>
                        </div>
                        <div class="text-sm text-moto-muted mb-3" x-show="m.active">
                            <p>Арендатор: <span class="text-moto-text" x-text="m.active?.renter"></span></p>
                            <p x-text="'С: ' + m.active?.started_at"></p>
                            <p x-show="m.active?.ended_at" x-text="'По: ' + m.active?.ended_at"></p>
                        </div>
                        <div class="text-sm text-moto-muted mb-3" x-show="m.upcoming && !m.active">
                            <p>Следующая бронь: <span class="text-moto-text" x-text="m.upcoming?.renter"></span></p>
                            <p x-text="'С: ' + m.upcoming?.started_at"></p>
                            <p x-show="m.upcoming?.ended_at" x-text="'По: ' + m.upcoming?.ended_at"></p>
                        </div>
                        <p class="text-sm text-moto-muted mb-3 line-clamp-2" x-text="m.comment || ''" x-show="m.comment">
                        </p>
                    </div>
                    @hasanyrole(['admin', 'manager'])
                        <div x-data="{ open: false }">
                            <div class="flex gap-2 mt-4">
                                <button @click="openReserve(m)"
                                    class="flex-1 bg-moto-orange hover:bg-moto-orange-dark text-white py-2 rounded text-sm font-medium transition">Зарезервировать</button>
                                <button @click="open = !open"
                                    class="px-3 py-2 bg-neutral-800 hover:bg-neutral-700 border border-neutral-600 rounded text-sm transition"
                                    x-text="open ? 'Скрыть' : 'Брони'"></button>
                            </div>
                            <div x-show="open" class="mt-3 text-xs" x-cloak>
                                <p class="font-medium mb-1 text-moto-muted">Все резервы:</p>
                                <ul class="space-y-2">
                                    <template x-for="b in m.rentals" :key="b.id">
                                        <li
                                            class="flex flex-wrap items-center justify-between gap-2 bg-neutral-800/50 border border-neutral-700 rounded px-3 py-2">
                                            <span class="text-moto-text"
                                                x-text="b.renter + ' — ' + b.started_at + ' / ' + b.ended_at"></span>
                                            <div class="flex gap-2">
                                                <button type="button" @click="openEditRental(b)"
                                                    class="px-2 py-1 bg-neutral-700 hover:bg-neutral-600 rounded text-xs transition">Изменить</button>
                                                <form method="POST" :action="'/rentals/' + b.id" class="contents"
                                                    onsubmit="return confirm('Отменить резерв?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-2 py-1 bg-red-900/40 hover:bg-red-900/60 text-red-400 rounded text-xs transition">Отменить</button>
                                                </form>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    @endhasanyrole
                </div>
            </template>
            <p x-show="filteredMotorcycles.length === 0" class="col-span-full text-moto-muted">Нет мотоциклов.</p>
        </div>

        <!-- Active rentals -->
        <div class="mt-10">
            <h2 class="text-xl font-semibold text-moto-orange mb-4">Действующие резервы</h2>
            <div class="bg-moto-card border border-neutral-700 rounded-xl overflow-hidden">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-neutral-800 text-moto-muted">
                        <tr>
                            <th class="px-4 py-3 font-medium">Мотоцикл</th>
                            <th class="px-4 py-3 font-medium">Арендатор</th>
                            <th class="px-4 py-3 font-medium">Период</th>
                            <th class="px-4 py-3 font-medium">Сумма, BYN</th>
                            <th class="px-4 py-3 font-medium">Комментарий</th>
                            <th class="px-4 py-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-700">
                        <template x-for="r in filteredRentals" :key="r.id">
                            <tr class="hover:bg-neutral-800/50">
                                <td class="px-4 py-3" x-text="r.motorcycle"></td>
                                <td class="px-4 py-3" x-text="r.renter"></td>
                                <td class="px-4 py-3" x-text="r.started_at + ' — ' + r.ended_at"></td>
                                <td class="px-4 py-3" x-text="r.total_amount_byn ?? '—'"></td>
                                <td class="px-4 py-3 text-moto-muted" x-text="r.comment || ''"></td>
                                <td class="px-4 py-3">
                                    @hasanyrole(['admin', 'manager'])
                                        <form method="POST" :action="'/rentals/' + r.id" class="inline"
                                            onsubmit="return confirm('Отменить резерв?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-red-400 hover:text-red-300 text-xs font-medium">Отменить</button>
                                        </form>
                                    @endhasanyrole
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <p x-show="filteredRentals.length === 0" class="px-4 py-6 text-center text-moto-muted">Нет действующих
                    резервов.</p>
            </div>
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
                            <template x-for="(booking, index) in modal.bookings" :key="index">
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

        <!-- Edit rental modal -->
        <div x-show="editModal.open" class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display: none;" x-cloak>
            <div class="absolute inset-0 bg-black/70" @click="editModal.open = false"></div>
            <div class="relative bg-moto-card w-full max-w-lg rounded-xl border border-neutral-700 p-6 shadow-2xl">
                <h2 class="text-xl font-semibold text-moto-orange mb-1">Редактировать аренду</h2>
                <p class="text-sm text-moto-muted mb-5"
                    x-text="editModal.rental?.renter + ' — ' + editModal.rental?.started_at"></p>

                <form method="POST" :action="'/rentals/' + editModal.rental?.id" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-sm text-moto-muted mb-1">Арендатор</label>
                        <select name="renter_id" required x-model="editModal.rental.renter_id"
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
                                x-model="editModal.rental.started_at_input"
                                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm text-moto-muted mb-1">Дата по</label>
                            <input type="datetime-local" name="ended_at" x-model="editModal.rental.ended_at_input"
                                class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-moto-muted mb-1">Сумма аренды, BYN</label>
                        <input type="number" step="0.01" min="0" name="total_amount_byn"
                            x-model="editModal.rental.total_amount_byn"
                            class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-sm text-moto-muted mb-1">Комментарий</label>
                        <textarea name="comment" rows="3" x-model="editModal.rental.comment"
                            class="w-full bg-neutral-800 border border-neutral-600 rounded px-3 py-2 text-moto-text focus:border-moto-orange focus:outline-none"></textarea>
                    </div>

                    <input type="hidden" name="status" value="rented">

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="editModal.open = false"
                            class="px-4 py-2 rounded bg-neutral-700 hover:bg-neutral-600 text-sm">Отмена</button>
                        <button type="submit"
                            class="px-4 py-2 rounded bg-moto-orange hover:bg-moto-orange-dark text-white text-sm font-medium">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
