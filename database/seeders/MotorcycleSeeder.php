<?php

namespace Database\Seeders;

use App\Models\Motorcycle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MotorcycleSeeder extends Seeder
{
    public function run(): void
    {
        $motorcycles = [
            'HD Electra Glide',
            'HD ELECTRA',
            'HD Softail Deluxe',
            'Victory KP',
            'Honda GL1800',
            'Honda VT1100',
            'Kawasaki Vulcan 900',
            'HD Sportster 1200',
            'HD Sportster 883',
            'Kawasaki Eliminator 125',
            'Yamaha XVS-650',
        ];

        foreach ($motorcycles as $name) {
            $motorcycle = Motorcycle::create([
                'name' => $name,
                'slug' => (string) Str::ulid(),
                'year' => date('Y'),
                'comment' => null,
            ]);

            $motorcycle->slug = $motorcycle->id . '-' . Str::slug($motorcycle->name) . '-' . $motorcycle->year;
            $motorcycle->saveQuietly();
        }
    }
}
