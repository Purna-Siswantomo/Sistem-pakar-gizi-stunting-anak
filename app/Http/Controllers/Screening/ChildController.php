<?php

namespace App\Http\Controllers\Screening;

use App\Http\Controllers\Controller;
use App\Models\Child;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildController extends Controller
{
    public function index(): View
    {
        $children = Child::query()
            ->withCount('screenings')
            ->latest()
            ->paginate(10);

        return view('children.index', compact('children'));
    }

    public function create(): View
    {
        return view('children.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'birth_date' => ['required', 'date'],
            'parent_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);

        $child = Child::create($validated);

        return redirect()
            ->route('children.show', $child)
            ->with('success', 'Data anak berhasil disimpan.');
    }

    public function show(Child $child): View
    {
        $child->load(['screenings' => fn ($query) => $query->latest('screening_date')]);

        return view('children.show', compact('child'));
    }
}
