<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Location;
use App\Models\Forklift;

class StarterSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        if (!User::where('email','admin@forklift.local')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@forklift.local',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]);
        }

        // Default location & forklifts
        $loc = Location::firstOrCreate(['name' => 'Main Warehouse'], ['address' => '123 Logistics Ave']);
        Forklift::firstOrCreate(
            ['name' => 'Yale 2T'],
            ['capacity_kg' => 2000, 'location_id' => $loc->id, 'status'=>'available']
        );
        Forklift::firstOrCreate(
            ['name' => 'Toyota 3T'],
            ['capacity_kg' => 3000, 'location_id' => $loc->id, 'status'=>'available']
        );
    }
}
