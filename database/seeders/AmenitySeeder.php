<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Amenity;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            ['name' => 'Free Wi-Fi'],
            ['name' => 'Air Conditioning'],
            ['name' => 'Flatscreen TV'],
            ['name' => 'Mini-bar'],
            ['name' => 'Room Service'],
            ['name' => 'Ocean View'],
            ['name' => 'Mountain View'],
            ['name' => 'In-room Safe'],
        ];

        foreach ($amenities as $amenity) {
            Amenity::create($amenity);
        }
    }
}
