<?php

namespace App\Http\Controllers;

use App\Enums\RentalStatus;
use App\Models\Motorcycle;
use App\Models\Rental;
use App\Models\Renter;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $motorcycles = Motorcycle::with(['activeRental.renter', 'upcomingRental.renter', 'rentals.renter'])->get();
        $renters = $request->user()->hasAnyRole(['admin', 'manager'])
            ? Renter::all()
            : collect();

        $activeRentals = Rental::with(['motorcycle', 'renter'])
            ->where('status', RentalStatus::Rented->value)
            ->where(static function ($query) {
                $query->whereNull('ended_at')
                    ->orWhere('ended_at', '>', now());
            })
            ->orderBy('started_at')
            ->get();

        return view('home', compact('motorcycles', 'renters', 'activeRentals'));
    }
}
