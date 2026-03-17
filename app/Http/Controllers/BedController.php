<?php

namespace App\Http\Controllers;

use App\Models\Bed;
use Illuminate\Http\Request;

class BedController extends Controller
{
    public function index()
    {
        $beds = Bed::all();
        
        $stats = [
            'total' => $beds->count(),
            'occupied' => $beds->where('is_occupied', true)->count(),
            'available' => $beds->where('is_occupied', false)->count(),
        ];

        return view('beds.index', compact('beds', 'stats'));
    }

    public function create()
    {
        return view('beds.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'ward_type' => 'required|in:General,Private,ICU',
            'bed_number' => 'required|string|unique:beds,bed_number',
            'floor' => 'required|string',
        ]);

        Bed::create($request->all());

        return redirect()->route('beds.index')->with('success', 'Bed created successfully.');
    }

    public function edit(Bed $bed)
    {
        return view('beds.edit', compact('bed'));
    }

    public function update(Request $request, Bed $bed)
    {
        $request->validate([
            'ward_type' => 'required|in:General,Private,ICU',
            'bed_number' => 'required|string|unique:beds,bed_number,' . $bed->id,
            'floor' => 'required|string',
            'is_occupied' => 'boolean'
        ]);

        $bed->update($request->all());

        return redirect()->route('beds.index')->with('success', 'Bed updated successfully.');
    }

    public function destroy(Bed $bed)
    {
        $bed->delete();
        return redirect()->route('beds.index')->with('success', 'Bed deleted successfully.');
    }
}
