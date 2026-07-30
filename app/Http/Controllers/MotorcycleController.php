<?php

namespace App\Http\Controllers;

use App\Models\Motorcycle;
use Illuminate\Http\Request;

class MotorcycleController extends Controller
{
    public function index()
    {
        $motorcycles = Motorcycle::withTrashed()->latest()->get();

        return view('motorcycles.index', compact('motorcycles'));
    }

    public function create()
    {
        $this->authorize('create', Motorcycle::class);

        return view('motorcycles.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Motorcycle::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'comment' => ['nullable', 'string'],
        ]);

        Motorcycle::create($data);

        return redirect()->route('motorcycles.index')->with('success', 'Мотоцикл добавлен.');
    }

    public function show(Motorcycle $motorcycle)
    {
        return view('motorcycles.show', compact('motorcycle'));
    }

    public function edit(Motorcycle $motorcycle)
    {
        $this->authorize('update', $motorcycle);

        return view('motorcycles.edit', compact('motorcycle'));
    }

    public function update(Request $request, Motorcycle $motorcycle)
    {
        $this->authorize('update', $motorcycle);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'year' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 1)],
            'comment' => ['nullable', 'string'],
        ]);

        $motorcycle->update($data);

        return redirect()->route('motorcycles.index')->with('success', 'Мотоцикл обновлён.');
    }

    public function destroy(Motorcycle $motorcycle)
    {
        $this->authorize('delete', $motorcycle);

        $motorcycle->delete();

        return redirect()->route('motorcycles.index')->with('success', 'Мотоцикл удалён.');
    }

    public function restore($id)
    {
        $motorcycle = Motorcycle::withTrashed()->findOrFail($id);
        $this->authorize('restore', $motorcycle);

        $motorcycle->restore();

        return redirect()->route('motorcycles.index')->with('success', 'Мотоцикл восстановлен.');
    }
}
