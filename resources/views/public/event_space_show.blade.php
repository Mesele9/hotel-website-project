@extends('layouts.public')
@section('title', $eventSpace->name)

@section('content')
<div class="bg-white">
    <div class="container mx-auto px-6 py-12">
        <!-- Main Content: Space Details -->
        <div>
            <a href="{{ route('page.meetings') }}" class="text-primary font-semibold hover:underline">&larr; Back to All Venues</a>
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-800 mt-4">{{ $eventSpace->name }}</h1>
            <p class="text-lg text-gray-600 mt-2">Maximum Capacity: {{ $eventSpace->capacity }} Guests</p>
        </div>

        <!-- **START: UPDATED IMAGE GALLERY** -->
        <div class="mt-8">
            {{-- Main Featured Image --}}
            <img id="main-event-image" src="{{ $eventSpace->image ? asset('storage/' . $eventSpace->image) : 'https://via.placeholder.com/1200x600.png?text=Venue' }}" alt="{{ $eventSpace->name }}" class="w-full h-96 object-cover rounded-lg shadow-lg">
            
            {{-- Thumbnails for Gallery --}}
            @if($eventSpace->images->count() > 0)
            <div class="grid grid-cols-3 md:grid-cols-6 gap-4 mt-4">
                {{-- Add the featured image as the first thumbnail --}}
                @if($eventSpace->image)
                <div>
                    <img src="{{ asset('storage/' . $eventSpace->image) }}" alt="Thumbnail of {{ $eventSpace->name }}" class="w-full h-28 object-cover rounded-md cursor-pointer thumbnail-event-image border-4 border-primary">
                </div>
                @endif
                {{-- Loop through the rest of the gallery images --}}
                @foreach($eventSpace->images as $image)
                <div>
                    <img src="{{ asset('storage/' . $image->path) }}" alt="Gallery thumbnail" class="w-full h-28 object-cover rounded-md cursor-pointer thumbnail-event-image border-4 border-transparent hover:border-primary">
                </div>
                @endforeach
            </div>
            @endif
        </div>
        <!-- **END: UPDATED IMAGE GALLERY** -->

        <!-- Description -->
        <div class="mt-12 max-w-4xl">
            <h2 class="text-3xl font-bold text-gray-800 border-b pb-2">Venue Details</h2>
            <div class="prose max-w-none text-lg text-gray-700 mt-4">
                {!! nl2br(e($eventSpace->description)) !!}
            </div>
        </div>

    
    <!-- Inquiry Form Section -->
    <div class="bg-gray-100 mt-16 -mx-6 px-6 py-16">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">Request a Proposal for the {{ $eventSpace->name }}</h2>
            @if(session('success'))
                <div class="text-center text-green-700 bg-green-100 border border-green-400 p-4 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('event_space.inquiry.store') }}" method="POST" class="bg-white p-8 rounded-lg shadow-lg">
                @csrf
                <input type="hidden" name="space_name" value="{{ $eventSpace->name }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <input type="text" name="name" id="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                        <input type="email" name="email" id="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="md:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name (Optional)</label>
                        <input type="text" name="company_name" id="company_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="event_type" class="block text-sm font-medium text-gray-700">Type of Event</label>
                        <input type="text" name="event_type" id="event_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="e.g., Wedding, Meeting">
                    </div>
                    <div>
                        <label for="number_of_guests" class="block text-sm font-medium text-gray-700">Number of Guests</label>
                        <input type="number" name="number_of_guests" id="number_of_guests" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Services Requested</label>
                    <div class="mt-2 grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="flex items-center"><input type="checkbox" name="services[]" value="Coffee Break" class="h-4 w-4 rounded border-gray-300"><label for="coffee_break" class="ml-2 text-sm text-gray-600">Coffee Break</label></div>
                        <div class="flex items-center"><input type="checkbox" name="services[]" value="Lunch" class="h-4 w-4 rounded border-gray-300"><label for="lunch" class="ml-2 text-sm text-gray-600">Lunch</label></div>
                        <div class="flex items-center"><input type="checkbox" name="services[]" value="Dinner" class="h-4 w-4 rounded border-gray-300"><label for="dinner" class="ml-2 text-sm text-gray-600">Dinner</label></div>
                        <div class="flex items-center"><input type="checkbox" name="services[]" value="Projector & Screen" class="h-4 w-4 rounded border-gray-300"><label for="projector" class="ml-2 text-sm text-gray-600">Projector & Screen</label></div>
                        <div class="flex items-center"><input type="checkbox" name="services[]" value="Stationery" class="h-4 w-4 rounded border-gray-300"><label for="stationery" class="ml-2 text-sm text-gray-600">Stationery</label></div>
                        <div class="flex items-center"><input type="checkbox" name="services[]" value="Sound System" class="h-4 w-4 rounded border-gray-300"><label for="sound_system" class="ml-2 text-sm text-gray-600">Sound System</label></div>
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="details" class="block text-sm font-medium text-gray-700">Additional Details or Questions</label>
                    <textarea name="details" id="details" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>

                <div class="mt-6 text-center">
                    <button type="submit" class="bg-primary text-white font-bold py-3 px-8 rounded-lg hover:bg-primary-dark">Submit Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mainImage = document.getElementById('main-event-image');
        const thumbnails = document.querySelectorAll('.thumbnail-event-image');

        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Change the main image source to the clicked thumbnail's source
                mainImage.src = this.src;

                // Update the active border styling
                thumbnails.forEach(t => t.classList.remove('border-primary'));
                thumbnails.forEach(t => t.classList.add('border-transparent'));
                this.classList.remove('border-transparent');
                this.classList.add('border-primary');
            });
        });
    });
</script>
@endpush
@endsection
