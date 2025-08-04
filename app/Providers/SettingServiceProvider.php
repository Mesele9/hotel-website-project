<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Use a try-catch block to prevent errors during migrations
        try {
            // Check if the settings table exists
            if (Schema::hasTable('settings')) {
                // Use a view composer to share settings with all views
                View::composer('*', function ($view) {
                    $settings = Cache::rememberForever('settings', function () {
                        // Key by 'key' for easy access like $settings['hotel_name']
                        return Setting::all()->keyBy('key')->map->value;
                    });
                    $view->with('settings', $settings);
                });
            }
        } catch (\Exception $e) {
            // Log the error or handle it gracefully
            // This prevents issues when running `php artisan` commands before the DB is ready
        }
    }
}
