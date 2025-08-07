<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventSpace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventSpaceController extends Controller
{
    public function index()
    {
        $eventSpaces = EventSpace::latest()->get();
        return view('admin.event_spaces.index', compact('eventSpaces'));
    }

    public function create()
    {
        return view('admin.event_spaces.form');
    }

    public function store(Request $request)
    {
        $validated = $this->validateSpace($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('event_space_images', 'public');
        }

        EventSpace::create($validated);

        $eventSpace = EventSpace::create($validated);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('event_space_gallery', 'public');
                $eventSpace->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('admin.event-spaces.index')->with('success', 'Event space created successfully.');
    }

    public function edit(EventSpace $eventSpace)
    {
        return view('admin.event_spaces.form', ['space' => $eventSpace]);
    }

    public function update(Request $request, EventSpace $eventSpace)
    {
        $validated = $this->validateSpace($request);

        if ($request->hasFile('image')) {
            if ($eventSpace->image) {
                Storage::disk('public')->delete($eventSpace->image);
            }
            $validated['image'] = $request->file('image')->store('event_space_images', 'public');
        }

        $eventSpace->update($validated);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('event_space_gallery', 'public');
                $eventSpace->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('admin.event-spaces.index')->with('success', 'Event space updated successfully.');
    }

    public function destroy(EventSpace $eventSpace)
    {
        if ($eventSpace->image) {
            Storage::disk('public')->delete($eventSpace->image);
        }
        $eventSpace->delete();
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = \App\Models\Image::find($imageId);
                if ($image) {
                    Storage::disk('public')->delete($image->path);
                    $image->delete();
                }
            }
        }

        return redirect()->route('admin.event-spaces.index')->with('success', 'Event space deleted successfully.');
    }

    protected function validateSpace(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
    }
}