<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PublicRoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;

use App\Models\EventSpace; 
use Illuminate\Support\Facades\Http;


// **REPLACE THE DEFAULT WELCOME ROUTE WITH THIS**
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/test-chapa-connection', function () {
    try {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('CHAPA_SECRET_KEY')
        ])->get('https://api.chapa.co/v1/banks');

        if ($response->successful()) {
            return 'SUCCESS: Connected to Chapa API and fetched banks.';
        } else {
            return 'FAILED: Could not connect. Response: ' . $response->body();
        }
    } catch (\Exception $e) {
        return 'ERROR: Exception occurred. Message: ' . $e->getMessage();
    }
});

// **ADD THE NEW PUBLIC ROUTES**
Route::get('/meetings-events', [PageController::class, 'meetings'])->name('page.meetings');
Route::get('/meetings-events/{eventSpace:slug}', [PageController::class, 'showEventSpace'])->name('event_space.show');
Route::post('/meetings-events/inquiry', [PageController::class, 'storeEventInquiry'])->name('event_space.inquiry.store');
Route::get('/local-guide', [PageController::class, 'localGuide'])->name('page.local_guide');
Route::get('/local-guide/{post:slug}', [PageController::class, 'showPost'])->name('post.show');
Route::get('/contact', [PageController::class, 'contact'])->name('page.contact');
Route::post('/contact', [PageController::class, 'storeContactForm'])->name('page.contact.store'); 

Route::get('/booking/search', [BookingController::class, 'search'])->name('booking.search');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/success', [BookingController::class, 'success'])->name('booking.success');

Route::post('/booking/cart/add', [BookingController::class, 'addToCart'])->name('booking.cart.add');
Route::get('/booking/cart/remove/{rowId}', [BookingController::class, 'removeFromCart'])->name('booking.cart.remove');
Route::get('/booking/cart/clear', [BookingController::class, 'clearCart'])->name('booking.cart.clear');
Route::get('/booking/cart', [BookingController::class, 'viewCart'])->name('booking.cart.view'); 

Route::get('/rooms/{roomType:slug}', [PublicRoomController::class, 'show'])->name('rooms.show');

// **ADD THE NEW CHAPA BOOKING ROUTES**
Route::post('/booking/initialize', [BookingController::class, 'initializeChapa'])->name('booking.initialize');
Route::get('/booking/chapa/callback', [BookingController::class, 'chapaCallback'])->name('booking.callback');
Route::get('/booking/confirmation', [BookingController::class, 'confirmation'])->name('booking.confirmation');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Room Type CRUD
    Route::resource('room-types', \App\Http\Controllers\Admin\RoomTypeController::class)->scoped(['room_type' => 'id',]);

    // Amenity CRUD
    Route::resource('amenities', \App\Http\Controllers\Admin\AmenityController::class); // <-- ADD THIS LINE

    // Room CRUD
    Route::resource('rooms', \App\Http\Controllers\Admin\RoomController::class); // <-- ADD THIS LINE

    // Site Customization
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update'); // <-- ADD THESE TWO LINES

    // Availability Calendar
    Route::get('availability', [\App\Http\Controllers\Admin\AvailabilityController::class, 'index'])->name('availability.index');
    Route::post('availability', [\App\Http\Controllers\Admin\AvailabilityController::class, 'store'])->name('availability.store');

    Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class); // Only create index/show for now

    // Local Guide / Blog Management
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except(['show']);
    Route::resource('posts', \App\Http\Controllers\Admin\PostController::class);

    // Meetings & Events Management
    Route::resource('event-spaces', \App\Http\Controllers\Admin\EventSpaceController::class)->except(['show']);

    // Contact Message Viewer
    Route::resource('messages', \App\Http\Controllers\Admin\ContactMessageController::class)->only(['index', 'show']);

    // Add other admin routes here in the future

});
