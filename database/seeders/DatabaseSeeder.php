<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@moto.local',
            'password' => bcrypt('password'),
        ])->assignRole('manager');

        User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@moto.local',
            'password' => bcrypt('password'),
        ])->assignRole('member');

        $this->call(MotorcycleSeeder::class);
        $this->call(RentalFactSeeder::class);
    }
}
