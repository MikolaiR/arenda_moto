<?php

namespace App\Http\Controllers;

use App\Models\Renter;
use Illuminate\Http\Request;

class RenterController extends Controller
{
    public function index()
    {
        $renters = Renter::withTrashed()->latest()->get();

        return view('renters.index', compact('renters'));
    }

    public function create()
    {
        $this->authorize('create', Renter::class);

        return view('renters.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Renter::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
        ]);

        Renter::create($data);

        return redirect()->route('renters.index')->with('success', 'Арендатор добавлен.');
    }

    public function show(Renter $renter)
    {
        return view('renters.show', compact('renter'));
    }

    public function edit(Renter $renter)
    {
        $this->authorize('update', $renter);

        return view('renters.edit', compact('renter'));
    }

    public function update(Request $request, Renter $renter)
    {
        $this->authorize('update', $renter);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'comment' => ['nullable', 'string'],
        ]);

        $renter->update($data);

        return redirect()->route('renters.index')->with('success', 'Арендатор обновлён.');
    }

    public function destroy(Renter $renter)
    {
        $this->authorize('delete', $renter);

        $renter->delete();

        return redirect()->route('renters.index')->with('success', 'Арендатор удалён.');
    }

    public function restore($id)
    {
        $renter = Renter::withTrashed()->findOrFail($id);
        $this->authorize('restore', $renter);

        $renter->restore();

        return redirect()->route('renters.index')->with('success', 'Арендатор восстановлен.');
    }
}
