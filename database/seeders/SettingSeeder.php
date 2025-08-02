<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Identity
            ['key' => 'hotel_logo', 'value' => null],
            ['key' => 'hotel_favicon', 'value' => null],

            // Branding Colors
            ['key' => 'color_primary', 'value' => '#3B82F6'],
            ['key' => 'color_secondary', 'value' => '#6366F1'],
            ['key' => 'color_bg_main', 'value' => '#F3F4F6'],

            // Header
            ['key' => 'color_header_bg', 'value' => '#FFFFFF'],
            ['key' => 'color_header_text', 'value' => '#1F2937'],

            // Footer
            ['key' => 'color_footer_bg', 'value' => '#111827'],
            ['key' => 'color_footer_text', 'value' => '#FFFFFF'],

            // Company Info
            ['key' => 'hotel_name', 'value' => 'YegeZulejoch Hotel'],
            ['key' => 'hotel_address', 'value' => '123 Paradise Lane, Ocean View City'],
            ['key' => 'hotel_phone', 'value' => '+1 (555) 123-4567'],
            ['key' => 'hotel_email', 'value' => 'reservations@yegezulejoch.com'],

            // Social Media
            ['key' => 'social_facebook', 'value' => 'https://facebook.com'],
            ['key' => 'social_twitter', 'value' => 'https://twitter.com'],
            ['key' => 'social_instagram', 'value' => 'https://instagram.com'],
            ['key' => 'social_telegram', 'value' => ''], 
            ['key' => 'social_whatsapp', 'value' => ''], 
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}