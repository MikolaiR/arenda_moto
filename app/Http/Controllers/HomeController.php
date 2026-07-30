<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
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

        return view('home', compact('motorcycles', 'renters'));
    }
}
