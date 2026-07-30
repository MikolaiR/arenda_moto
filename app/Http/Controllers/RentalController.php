<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use App\Models\Rental;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RentalController extends Controller
{
    public function store(Request $request, Motorcycle $motorcycle)
    {
        $this->authorize('create', Rental::class);

        $data = $request->validate([
            'renter_id' => ['required', 'exists:renters,id'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
            'total_amount_byn' => ['nullable', 'numeric', 'min:0'],
            'comment' => ['nullable', 'string'],
        ]);

        $startedAt = Carbon::parse($data['started_at']);
        $endedAt = Carbon::parse($data['ended_at']);

        if (! $motorcycle->isAvailableBetween($startedAt, $endedAt)) {
            throw ValidationException::withMessages([
                'started_at' => 'Мотоцикл занят на выбранный период.',
            ]);
        }

        $data['motorcycle_id'] = $motorcycle->id;
        $data['user_id'] = $request->user()->id;
        $data['status'] = 'rented';

        Rental::create($data);

        return redirect()->route('home')->with('success', 'Аренда сохранена.');
    }

    public function update(Request $request, Rental $rental)
    {
        $this->authorize('update', $rental);

        $data = $request->validate([
            'status' => ['required', 'in:rented,free'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['nullable', 'date', 'after_or_equal:started_at'],
            'total_amount_byn' => ['nullable', 'numeric', 'min:0'],
            'comment' => ['nullable', 'string'],
        ]);

        if ($data['status'] === 'free') {
            $data['ended_at'] = $data['ended_at'] ?? now()->format('Y-m-d H:i:s');
        }

        if ($data['status'] === 'rented') {
            $startedAt = Carbon::parse($data['started_at']);
            $endedAt = isset($data['ended_at']) ? Carbon::parse($data['ended_at']) : null;

            if ($endedAt && ! $rental->motorcycle->isAvailableBetween($startedAt, $endedAt, $rental->id)) {
                throw ValidationException::withMessages([
                    'started_at' => 'Мотоцикл занят на выбранный период.',
                ]);
            }
        }

        $rental->update($data);

        return redirect()->route('home')->with('success', 'Статус аренды обновлён.');
    }

    public function destroy(Rental $rental)
    {
        $this->authorize('delete', $rental);

        $rental->delete();

        return redirect()->route('home')->with('success', 'Запись аренды удалена.');
    }
}
