<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Amenity;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all amenities
        $amenities = Amenity::all();

        // Create Standard Queen
        $standard = RoomType::create([
            'name' => 'Standard Queen',
            'description' => 'A comfortable room with a queen-sized bed, perfect for solo travelers or couples.',
            'capacity' => 2,
            'base_price' => 120.00
        ]);
        // Attach basic amenities
        $standard->amenities()->attach($amenities->whereIn('name', ['Free Wi-Fi', 'Air Conditioning', 'Flatscreen TV'])->pluck('id'));

        // Create Deluxe King
        $deluxe = RoomType::create([
            'name' => 'Deluxe King',
            'description' => 'A spacious room with a luxurious king-sized bed and a stunning mountain view.',
            'capacity' => 2,
            'base_price' => 180.00
        ]);
        // Attach more amenities
        $deluxe->amenities()->attach($amenities->whereIn('name', ['Free Wi-Fi', 'Air Conditioning', 'Flatscreen TV', 'Mini-bar', 'Mountain View'])->pluck('id'));

        // Create Executive Suite
        $suite = RoomType::create([
            'name' => 'Executive Suite',
            'description' => 'Our premium suite with a separate living area, king-sized bed, and panoramic ocean views.',
            'capacity' => 4,
            'base_price' => 250.00
        ]);
        // Attach all amenities
        $suite->amenities()->attach($amenities->pluck('id'));
    }
}