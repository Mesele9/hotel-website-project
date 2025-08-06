<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PublicRoomController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;

// **REPLACE THE DEFAULT WELCOME ROUTE WITH THIS**
Route::get('/', [HomeController::class, 'index'])->name('home');

// **ADD THE NEW PUBLIC ROUTES**
Route::get('/meetings-events', [PageController::class, 'meetings'])->name('page.meetings');
Route::get('/local-guide', [PageController::class, 'localGuide'])->name('page.local_guide');
Route::get('/contact', [PageController::class, 'contact'])->name('page.contact');

Route::get('/booking/search', [BookingController::class, 'search'])->name('booking.search');
Route::post('/booking/store', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/success', [BookingController::class, 'success'])->name('booking.success');

Route::post('/booking/cart/add', [BookingController::class, 'addToCart'])->name('booking.cart.add');
Route::get('/booking/cart/remove/{rowId}', [BookingController::class, 'removeFromCart'])->name('booking.cart.remove');
Route::get('/booking/cart/clear', [BookingController::class, 'clearCart'])->name('booking.cart.clear');
Route::get('/booking/cart', [BookingController::class, 'viewCart'])->name('booking.cart.view'); // The checkout page

Route::get('/rooms/{roomType:slug}', [PublicRoomController::class, 'show'])->name('rooms.show');

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

    // Add other admin routes here in the future

});
