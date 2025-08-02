<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\RoomType;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $standardQueenType = RoomType::where('name', 'Standard Queen')->first();
        $deluxeKingType = RoomType::where('name', 'Deluxe King')->first();
        $executiveSuiteType = RoomType::where('name', 'Executive Suite')->first();

        // Seed Standard Queen Rooms
        for ($i = 101; $i <= 110; $i++) {
            Room::create(['room_type_id' => $standardQueenType->id, 'room_number' => (string)$i]);
        }

        // Seed Deluxe King Rooms
        for ($i = 201; $i <= 208; $i++) {
            Room::create(['room_type_id' => $deluxeKingType->id, 'room_number' => (string)$i]);
        }

        // Seed Executive Suites
        for ($i = 301; $i <= 304; $i++) {
            Room::create(['room_type_id' => $executiveSuiteType->id, 'room_number' => (string)$i]);
        }
    }
}