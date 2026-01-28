<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RideSeeder extends Seeder
{
    public function run()
    {
        $rides = [
            // Car ID 1 rides
            [
                'pickup_point' => 'DHA Phase 1, Karachi',
                'drop_point' => 'Gulshan-e-Iqbal, Karachi',
                'date_time' => Carbon::now()->addDays(1)->setTime(8, 0),
                'total_seats' => 4,
                'price_per_seat' => 500.00,
                'car_id' => 1,
                'luggage_allowed' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'pickup_point' => 'Saddar, Karachi',
                'drop_point' => 'Airport, Karachi',
                'date_time' => Carbon::now()->addDays(2)->setTime(14, 30),
                'total_seats' => 3,
                'price_per_seat' => 300.00,
                'car_id' => 1,
                'luggage_allowed' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // Car ID 2 rides
            [
                'pickup_point' => 'Model Town, Lahore',
                'drop_point' => 'Liberty Market, Lahore',
                'date_time' => Carbon::now()->addDays(1)->setTime(9, 0),
                'total_seats' => 3,
                'price_per_seat' => 400.00,
                'car_id' => 2,
                'luggage_allowed' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'pickup_point' => 'Gulberg, Lahore',
                'drop_point' => 'DHA Phase 5, Lahore',
                'date_time' => Carbon::now()->addDays(3)->setTime(18, 0),
                'total_seats' => 2,
                'price_per_seat' => 350.00,
                'car_id' => 2,
                'luggage_allowed' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // Car ID 3 rides
            [
                'pickup_point' => 'F-7, Islamabad',
                'drop_point' => 'Blue Area, Islamabad',
                'date_time' => Carbon::now()->addDays(1)->setTime(10, 0),
                'total_seats' => 4,
                'price_per_seat' => 250.00,
                'car_id' => 3,
                'luggage_allowed' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'pickup_point' => 'G-10, Islamabad',
                'drop_point' => 'Rawalpindi Saddar',
                'date_time' => Carbon::now()->addDays(4)->setTime(16, 45),
                'total_seats' => 3,
                'price_per_seat' => 450.00,
                'car_id' => 3,
                'luggage_allowed' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // Car ID 4 rides
            [
                'pickup_point' => 'University Road, Karachi',
                'drop_point' => 'Clifton, Karachi',
                'date_time' => Carbon::now()->addDays(2)->setTime(11, 30),
                'total_seats' => 4,
                'price_per_seat' => 200.00,
                'car_id' => 4,
                'luggage_allowed' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // Car ID 5 rides
            [
                'pickup_point' => 'Bahadurabad, Karachi',
                'drop_point' => 'Tariq Road, Karachi',
                'date_time' => Carbon::now()->addDays(5)->setTime(13, 15),
                'total_seats' => 3,
                'price_per_seat' => 150.00,
                'car_id' => 5,
                'luggage_allowed' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'pickup_point' => 'Karachi Airport',
                'drop_point' => 'DHA Phase 8, Karachi',
                'date_time' => Carbon::now()->addDays(6)->setTime(20, 0),
                'total_seats' => 4,
                'price_per_seat' => 600.00,
                'car_id' => 5,
                'luggage_allowed' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            // More rides for variety
            [
                'pickup_point' => 'Johar Town, Lahore',
                'drop_point' => 'Allama Iqbal Airport, Lahore',
                'date_time' => Carbon::now()->addDays(2)->setTime(6, 30),
                'total_seats' => 2,
                'price_per_seat' => 700.00,
                'car_id' => 2,
                'luggage_allowed' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('rides')->insert($rides);
        
        $this->command->info('✅ 10 dummy rides inserted successfully!');
    }
}