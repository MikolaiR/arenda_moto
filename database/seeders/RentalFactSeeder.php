<?php

namespace Database\Seeders;

use App\Models\Motorcycle;
use App\Models\Rental;
use App\Models\Renter;
use App\Models\User;
use Illuminate\Database\Seeder;

class RentalFactSeeder extends Seeder
{
    public function run(): void
    {
        $renters = [
            'Тишкевич Сергей',
            'Дорошенко Дмитрий',
            'Шматько Дмитрий',
            'Лакиник Игорь',
            'Бакланов Алексей',
            'Филицкий Антон',
        ];

        foreach ($renters as $name) {
            Renter::firstOrCreate(['name' => $name, 'comment' => null]);
        }

        $admin = User::where('email', 'admin@moto.local')->firstOrFail();

        $facts = [
            [
                'motorcycle' => 'YAMAHA XVS650 DRAGSTAR',
                'renter' => 'Тишкевич Сергей',
                'started_at' => '2026-07-18 20:00',
                'ended_at' => '2026-07-19 20:00',
                'total_amount_byn' => 180,
                'status' => 'rented',
                'comment' => '2 шлема',
            ],
            [
                'motorcycle' => 'HARLEY-DAVIDSON SOFTAIL DELUXE',
                'renter' => 'Дорошенко Дмитрий',
                'started_at' => '2026-07-19 10:00',
                'ended_at' => '2026-07-20 10:00',
                'total_amount_byn' => 300,
                'status' => 'rented',
                'comment' => null,
            ],
            [
                'motorcycle' => 'YAMAHA XVS650 DRAGSTAR',
                'renter' => 'Шматько Дмитрий',
                'started_at' => '2026-07-19 20:00',
                'ended_at' => '2026-07-20 20:00',
                'total_amount_byn' => 160,
                'status' => 'rented',
                'comment' => null,
            ],
            [
                'motorcycle' => 'YAMAHA XVS650 DRAGSTAR',
                'renter' => 'Лакиник Игорь',
                'started_at' => '2026-07-22 13:00',
                'ended_at' => '2026-07-22 21:00',
                'total_amount_byn' => 220,
                'status' => 'rented',
                'comment' => null,
            ],
            [
                'motorcycle' => 'HARLEY-DAVIDSON ELECTRA GLIDE FLHTCUI',
                'renter' => 'Бакланов Алексей',
                'started_at' => '2026-08-02 11:00',
                'ended_at' => '2026-08-04 21:00',
                'total_amount_byn' => 1500,
                'status' => 'rented',
                'comment' => '58000 РФ, аванс 11000',
            ],
            [
                'motorcycle' => 'HARLEY-DAVIDSON SOFTAIL DELUXE',
                'renter' => 'Бакланов Алексей',
                'started_at' => '2026-08-02 11:00',
                'ended_at' => '2026-08-04 21:00',
                'total_amount_byn' => 1500,
                'status' => 'rented',
                'comment' => '58000 РФ, аванс 11000',
            ],
            [
                'motorcycle' => 'HARLEY-DAVIDSON ELECTRA GLIDE FLHTCUI',
                'renter' => 'Филицкий Антон',
                'started_at' => '2026-08-05 12:00',
                'ended_at' => '2026-08-08 12:00',
                'total_amount_byn' => 900,
                'status' => 'rented',
                'comment' => '69000 РФ, аванс 10.000',
            ],
        ];

        foreach ($facts as $fact) {
            $motorcycle = Motorcycle::where('name', $fact['motorcycle'])->firstOrFail();
            $renter = Renter::where('name', $fact['renter'])->firstOrFail();

            Rental::create([
                'motorcycle_id' => $motorcycle->id,
                'renter_id' => $renter->id,
                'user_id' => $admin->id,
                'started_at' => $fact['started_at'],
                'ended_at' => $fact['ended_at'],
                'total_amount_byn' => $fact['total_amount_byn'],
                'status' => $fact['status'],
                'comment' => $fact['comment'],
            ]);
        }
    }
}
