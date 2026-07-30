<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::create(['name' => 'admin']);
        Role::create(['name' => 'manager']);
        Role::create(['name' => 'member']);

        $user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@moto.local',
            'password' => bcrypt('password'),
        ]);

        $user->assignRole($admin);
    }
}
