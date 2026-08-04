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
            ['name' => 'BMW F650 CS', 'year' => 2005, 'state_number' => '6439 ВМ77'],
            ['name' => 'HARLEY-DAVIDSON DYNA FXDL', 'year' => 2005, 'state_number' => '4898 AE-5'],
            ['name' => 'HARLEY-DAVIDSON ELECTRA GLIDE FLHTCUI', 'year' => 2005, 'state_number' => '6832 AI-5'],
            ['name' => 'HARLEY-DAVIDSON FLHX', 'year' => 2008, 'state_number' => '5661 AK-7'],
            ['name' => 'HARLEY-DAVIDSON SOFTAIL DELUXE', 'year' => 2011, 'state_number' => '4627 AK-7'],
            ['name' => 'HARLEY-DAVIDSON SPORTSTER 1200', 'year' => 1994, 'state_number' => '3935 AK-7'],
            ['name' => 'HARLEY-DAVIDSON STREET GLIDE', 'year' => 2019, 'state_number' => '7560 AK-5'],
            ['name' => 'HARLEY-DAVIDSON XL 883', 'year' => 2013, 'state_number' => '4861 AK-7'],
            ['name' => 'HONDA GL1800', 'year' => 2001, 'state_number' => '5571 AK-7'],
            ['name' => 'HONDA VT1100 SHADOW S', 'year' => 2001, 'state_number' => null],
            ['name' => 'HONDA VT 750', 'year' => 2006, 'state_number' => '4450 AI-5'],
            ['name' => 'KAWASAKI ELIMINATOR 125', 'year' => 2010, 'state_number' => '3072 AI-7'],
            ['name' => 'KAWASAKI VULCAN 900', 'year' => 2011, 'state_number' => '7004 AE-5'],
            ['name' => 'ROYAL ENFIELD METEOR', 'year' => 2021, 'state_number' => '9677 AE-7'],
            ['name' => 'TRIUMPH SPEEDMASTER 900', 'year' => 2018, 'state_number' => '2262 AI-7'],
            ['name' => 'VICTORY KINGPIN', 'year' => 2005, 'state_number' => null],
            ['name' => 'YAMAHA XVS650 DRAGSTAR', 'year' => 2005, 'state_number' => '7884 AE-5'],
            ['name' => 'МИНСК D4 125', 'year' => 2025, 'state_number' => '8588 AI-7'],
            ['name' => 'МИНСК D4 125', 'year' => 2025, 'state_number' => '8587 AI-7'],
        ];

        foreach ($motorcycles as $motorcycleData) {
            $motorcycle = Motorcycle::create([
                'name' => $motorcycleData['name'],
                'slug' => (string) Str::ulid(),
                'year' => $motorcycleData['year'],
                'state_number' => $motorcycleData['state_number'],
                'comment' => null,
            ]);

            $motorcycle->slug = $motorcycle->id . '-' . Str::slug($motorcycle->name) . '-' . $motorcycle->year;
            $motorcycle->saveQuietly();
        }
    }
}
