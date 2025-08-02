<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index()
    {
        // Fetch all settings and key them by their 'key' for easy access in the view
        $settings = Setting::all()->keyBy('key')->map(function ($setting) {
            return $setting->value;
        });

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'hotel_logo' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:2048',
            'hotel_favicon' => 'nullable|image|mimes:png,ico|max:512',
            'hotel_name' => 'nullable|string',
            'hotel_phone' => 'nullable|string',
            'hotel_email' => 'nullable|email',
            'hotel_address' => 'nullable|string',
            'color_primary' => 'nullable|string',
            'color_secondary' => 'nullable|string',
            'color_header_bg' => 'nullable|string',
            'color_header_text' => 'nullable|string',
            'color_footer_bg' => 'nullable|string',
            'color_footer_text' => 'nullable|string',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_telegram' => 'nullable|url', 
            'social_whatsapp' => 'nullable|url',  
        ]);

        // Handle file uploads
        foreach (['hotel_logo', 'hotel_favicon'] as $key) {
            if ($request->hasFile($key)) {
                $setting = Setting::firstOrNew(['key' => $key]);
                // Delete old file if it exists
                if ($setting->value && Storage::disk('public')->exists($setting->value)) {
                    Storage::disk('public')->delete($setting->value);
                }
                $path = $request->file($key)->store('settings', 'public');
                $setting->value = $path;
                $setting->save();
            }
        }

        // Handle text-based settings
        foreach ($validated as $key => $value) {
            // Skip file inputs as they are handled above
            if ($request->hasFile($key)) {
                continue;
            }
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        
        // Clear cache to apply changes immediately
        Artisan::call('cache:clear');

        return redirect()->route('admin.settings.index')->with('success', 'Settings updated successfully.');
    }
}