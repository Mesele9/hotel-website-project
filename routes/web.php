<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;


Route::get('/', function () {
    return view('welcome');
});

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
    Route::resource('room-types', \App\Http\Controllers\Admin\RoomTypeController::class); // <-- ADD THIS LINE

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

    // Add other admin routes here in the future

});
