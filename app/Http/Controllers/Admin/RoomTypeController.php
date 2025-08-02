<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Amenity;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roomTypes = RoomType::withCount('rooms')->get();
        return view('admin.room_types.index', compact('roomTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $amenities = Amenity::all();
        return view('admin.room_types.create', compact('amenities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        $roomType = RoomType::create($validated);

        if ($request->has('amenities')) {
            $roomType->amenities()->sync($request->amenities);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('room_type_images', 'public');
                $roomType->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('admin.room-types.index')->with('success', 'Room type created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RoomType $roomType)
    {
        $amenities = Amenity::all();
        $roomType->load('images', 'amenities'); // Eager load relationships
        return view('admin.room_types.edit', compact('roomType', 'amenities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RoomType $roomType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'exists:images,id'
        ]);

        $roomType->update($validated);

        $roomType->amenities()->sync($request->amenities ?? []);

        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = Image::find($imageId);
                if ($image && $image->imageable_id === $roomType->id) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('room_type_images', 'public');
                $roomType->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('admin.room-types.index')->with('success', 'Room type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RoomType $roomType)
    {
        // Delete associated images from storage
        foreach ($roomType->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }
        
        $roomType->delete();

        return redirect()->route('admin.room-types.index')->with('success', 'Room type deleted successfully.');
    }
}