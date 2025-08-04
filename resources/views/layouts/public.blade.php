<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Welcome') - {{ $settings['hotel_name'] ?? config('app.name', 'Laravel') }}</title>
    
    @if($settings['hotel_favicon'] ?? false)
    <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings['hotel_favicon']) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Dynamic Color Styles -->
    <style>
        :root {
            --primary-color: {{ $settings['color_primary'] ?? '#3B82F6' }};
            --secondary-color: {{ $settings['color_secondary'] ?? '#6366F1' }};
        }
        .bg-primary { background-color: var(--primary-color); }
        .text-primary { color: var(--primary-color); }
        .border-primary { border-color: var(--primary-color); }
        .hover\:bg-primary-dark:hover { filter: brightness(0.9); }
    </style>

    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <!-- Header -->
    <header style="background-color: {{ $settings['color_header_bg'] ?? '#FFFFFF' }}; color: {{ $settings['color_header_text'] ?? '#1F2937' }};">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <a href="/">
                @if($settings['hotel_logo'] ?? false)
                    <img src="{{ asset('storage/' . $settings['hotel_logo']) }}" alt="{{ $settings['hotel_name'] ?? 'Hotel Logo' }}" class="h-12">
                @else
                    <span class="text-2xl font-bold">{{ $settings['hotel_name'] ?? 'YegeZulejoch' }}</span>
                @endif
            </a>
            <div class="space-x-8 text-lg">
                <a href="#" class="hover:text-primary">Rooms</a>
                <a href="#" class="hover:text-primary">Meetings & Events</a>
                <a href="#" class="hover:text-primary">Local Guide</a>
                <a href="#" class="hover:text-primary">Contact</a>
                <a href="#" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark">Book Now</a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer style="background-color: {{ $settings['color_footer_bg'] ?? '#111827' }}; color: {{ $settings['color_footer_text'] ?? '#FFFFFF' }};">
        <div class="container mx-auto px-6 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="font-bold text-xl mb-4">{{ $settings['hotel_name'] ?? 'YegeZulejoch Hotel' }}</h3>
                    <p>{{ $settings['hotel_address'] ?? '123 Paradise Lane, Ocean View City' }}</p>
                    <p>{{ $settings['hotel_phone'] ?? '+1 (555) 123-4567' }}</p>
                    <p>{{ $settings['hotel_email'] ?? 'reservations@yegezulejoch.com' }}</p>
                </div>
                <div>
                    <h3 class="font-bold text-xl mb-4">Quick Links</h3>
                    <ul>
                        <li class="mb-2"><a href="#" class="hover:underline">About Us</a></li>
                        <li class="mb-2"><a href="#" class="hover:underline">Photo Gallery</a></li>
                        <li class="mb-2"><a href="#" class="hover:underline">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-bold text-xl mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        @if($settings['social_facebook'] ?? false)<a href="{{ $settings['social_facebook'] }}" target="_blank">Facebook</a>@endif
                        @if($settings['social_twitter'] ?? false)<a href="{{ $settings['social_twitter'] }}" target="_blank">Twitter</a>@endif
                        @if($settings['social_instagram'] ?? false)<a href="{{ $settings['social_instagram'] }}" target="_blank">Instagram</a>@endif
                    </div>
                </div>
            </div>
            <div class="text-center mt-8 border-t border-gray-700 pt-4">
                © {{ date('Y') }} {{ $settings['hotel_name'] ?? 'YegeZulejoch Hotel' }}. All Rights Reserved.
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>