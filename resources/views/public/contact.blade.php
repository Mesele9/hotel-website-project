@extends('layouts.public')
@section('title', 'Contact Us')

@section('content')
<div class="bg-white">
    <div class="container mx-auto px-6 py-16">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-800">Get in Touch</h1>
            <p class="text-gray-600 text-lg mt-2">We’d love to hear from you. Let us know how we can help.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mt-12">
            <!-- Contact Info -->
            <div class="md:col-span-4 bg-gray-50 p-8 rounded-lg">
                <h2 class="text-2xl font-bold mb-6">Contact Information</h2>
                <div class="space-y-6">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-primary mt-1 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <div>
                            <h3 class="font-semibold">Address</h3>
                            <p class="text-gray-600">{{ $settings['hotel_address'] ?? '123 Paradise Lane, Ocean View City' }}</p>
                        </div>
                    </div>
                     <div class="flex items-start">
                        <svg class="w-6 h-6 text-primary mt-1 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <div>
                            <h3 class="font-semibold">Phone</h3>
                            <p class="text-gray-600">{{ $settings['hotel_phone'] ?? '+1 (555) 123-4567' }}</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-primary mt-1 mr-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <div>
                            <h3 class="font-semibold">Email</h3>
                            <p class="text-gray-600">{{ $settings['hotel_email'] ?? 'reservations@yegezulejoch.com' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="md:col-span-8 bg-white p-8 rounded-lg shadow-lg">
                 @if(session('success'))
                    <div class="text-green-800 bg-green-100 border-l-4 border-green-500 p-4 mb-6" role="alert">
                        <p class="font-bold">Message Sent</p>
                        <p>{{ session('success') }}</p>
                    </div>
                @endif
                <form action="{{ route('page.contact.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <div class="mt-6">
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input type="text" name="subject" id="subject" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="mt-6">
                        <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea name="message" id="message" rows="5" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    </div>
                    <div class="mt-6 text-right">
                        <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-lg hover:bg-primary-dark">
                            Send Message
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="mt-16">
        <iframe
            class="w-full h-96"
            loading="lazy"
            allowfullscreen
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1966.9706851462904!2d41.84541590238858!3d9.600317069720475!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1631011ddf7da6f3%3A0x763a12b1def18155!2zWWVnZXp1IExlam9jaCBIb3RlbCB8IOGLqOGLreGMiOGLmSDhiI3hjIbhib0g4YiG4Ym04YiN!5e0!3m2!1sen!2set!4v1754566866221!5m2!1sen!2set"> {{-- <-- REPLACE THIS SRC URL --}}
        </iframe>
    </div>
</div>
@endsection
