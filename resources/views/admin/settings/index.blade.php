@extends('layouts.admin')

@section('title', 'Site Customization')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 my-4">Site Customization & Settings</h1>

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Oops!</strong>
        <span class="block sm:inline">There were some problems with your input.</span>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="space-y-8">
        <!-- Identity -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Identity</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="hotel_logo" class="block text-gray-700 text-sm font-bold mb-2">Hotel Logo (PNG, JPG, WEBP)</label>
                    <input type="file" name="hotel_logo" id="hotel_logo" class="shadow border rounded w-full py-2 px-3">
                    @if ($settings['hotel_logo'] ?? null)
                        <img src="{{ asset('storage/' . $settings['hotel_logo']) }}" alt="Hotel Logo" class="mt-4 h-16">
                    @endif
                </div>
                <div>
                    <label for="hotel_favicon" class="block text-gray-700 text-sm font-bold mb-2">Favicon (ICO, PNG)</label>
                    <input type="file" name="hotel_favicon" id="hotel_favicon" class="shadow border rounded w-full py-2 px-3">
                     @if ($settings['hotel_favicon'] ?? null)
                        <img src="{{ asset('storage/' . $settings['hotel_favicon']) }}" alt="Favicon" class="mt-4 h-8 w-8">
                    @endif
                </div>
            </div>
        </div>

        <!-- Branding Colors -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Branding Colors</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
                <div>
                    <label for="color_primary" class="block text-gray-700 text-sm font-bold mb-2">Primary</label>
                    <input type="color" name="color_primary" id="color_primary" value="{{ old('color_primary', $settings['color_primary'] ?? '#3B82F6') }}" class="w-full h-10 p-1 border">
                </div>
                <div>
                    <label for="color_secondary" class="block text-gray-700 text-sm font-bold mb-2">Secondary</label>
                    <input type="color" name="color_secondary" id="color_secondary" value="{{ old('color_secondary', $settings['color_secondary'] ?? '#6366F1') }}" class="w-full h-10 p-1 border">
                </div>
                <div>
                    <label for="color_header_bg" class="block text-gray-700 text-sm font-bold mb-2">Header BG</label>
                    <input type="color" name="color_header_bg" id="color_header_bg" value="{{ old('color_header_bg', $settings['color_header_bg'] ?? '#FFFFFF') }}" class="w-full h-10 p-1 border">
                </div>
                 <div>
                    <label for="color_header_text" class="block text-gray-700 text-sm font-bold mb-2">Header Text</label>
                    <input type="color" name="color_header_text" id="color_header_text" value="{{ old('color_header_text', $settings['color_header_text'] ?? '#1F2937') }}" class="w-full h-10 p-1 border">
                </div>
                <div>
                    <label for="color_footer_bg" class="block text-gray-700 text-sm font-bold mb-2">Footer BG</label>
                    <input type="color" name="color_footer_bg" id="color_footer_bg" value="{{ old('color_footer_bg', $settings['color_footer_bg'] ?? '#111827') }}" class="w-full h-10 p-1 border">
                </div>
                <div>
                    <label for="color_footer_text" class="block text-gray-700 text-sm font-bold mb-2">Footer Text</label>
                    <input type="color" name="color_footer_text" id="color_footer_text" value="{{ old('color_footer_text', $settings['color_footer_text'] ?? '#FFFFFF') }}" class="w-full h-10 p-1 border">
                </div>
            </div>
        </div>
        
        <!-- Company Information -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Company Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input_text name="hotel_name" label="Hotel Name" :value="$settings['hotel_name'] ?? ''" />
                <x-input_text name="hotel_phone" label="Phone" :value="$settings['hotel_phone'] ?? ''" />
                <x-input_text name="hotel_email" label="Email" :value="$settings['hotel_email'] ?? ''" type="email" />
                <div class="md:col-span-2">
                    <label for="hotel_address" class="block text-gray-700 text-sm font-bold mb-2">Address</label>
                    <textarea name="hotel_address" id="hotel_address" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700">{{ old('hotel_address', $settings['hotel_address'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <h2 class="text-2xl font-semibold mb-4">Social Media</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-input_text name="social_facebook" label="Facebook URL" :value="$settings['social_facebook'] ?? ''" />
                <x-input_text name="social_twitter" label="Twitter URL" :value="$settings['social_twitter'] ?? ''" />
                <x-input_text name="social_instagram" label="Instagram URL" :value="$settings['social_instagram'] ?? ''" />
                <x-input_text name="social_telegram" label="Telegram URL" :value="$settings['social_telegram'] ?? ''" />
                <x-input_text name="social_whatsapp" label="WhatsApp URL" :value="$settings['social_whatsapp'] ?? ''" />
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Save All Settings
        </button>
    </div>
</form>
@endsection