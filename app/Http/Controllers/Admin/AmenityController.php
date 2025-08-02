<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;

class AmenityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $amenities = Amenity::latest()->get();
        return view('admin.amenities.index', compact('amenities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.amenities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name',
            'icon' => 'nullable|string|max:255',
        ]);

        Amenity::create($validated);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Amenity $amenity)
    {
        // Not used for this resource, redirect to edit
        return redirect()->route('admin.amenities.edit', $amenity);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Amenity $amenity)
    {
        return view('admin.amenities.edit', compact('amenity'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:amenities,name,' . $amenity->id,
            'icon' => 'nullable|string|max:255',
        ]);

        $amenity->update($validated);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Amenity $amenity)
    {
        // The pivot table records are deleted automatically because of the 'onDelete('cascade')' constraint in the migration.
        $amenity->delete();

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity deleted successfully.');
    }
}