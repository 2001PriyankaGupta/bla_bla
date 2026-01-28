<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CarSeeder extends Seeder
{
    public function run()
    {
        $cars = [
            [
                'user_id' => 1,
                'car_make' => 'Toyota',
                'car_model' => 'Camry',
                'car_year' => 2022,
                'car_color' => 'Silver',
                'licence_plate' => 'ABC-1234',
                'car_photo' => 'toyota_camry.jpg',
                'driver_license_front' => 'license_front_1.jpg',
                'driver_license_back' => 'license_back_1.jpg',
                'license_verified' => 'verified',
                'verification_notes' => 'Documents verified successfully',
                'verified_by' => 'Admin User',
                'verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'user_id' => 2,
                'car_make' => 'Honda',
                'car_model' => 'Civic',
                'car_year' => 2021,
                'car_color' => 'Red',
                'licence_plate' => 'XYZ-5678',
                'car_photo' => 'honda_civic.jpg',
                'driver_license_front' => 'license_front_2.jpg',
                'driver_license_back' => 'license_back_2.jpg',
                'license_verified' => 'verified',
                'verification_notes' => 'All documents in order',
                'verified_by' => 'Admin User',
                'verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'user_id' => 3,
                'car_make' => 'Suzuki',
                'car_model' => 'Mehran',
                'car_year' => 2020,
                'car_color' => 'White',
                'licence_plate' => 'KHI-9012',
                'car_photo' => 'suzuki_mehran.jpg',
                'driver_license_front' => 'license_front_3.jpg',
                'driver_license_back' => 'license_back_3.jpg',
                'license_verified' => 'verified',
                'verification_notes' => 'License verified',
                'verified_by' => 'Admin User',
                'verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'user_id' => 4,
                'car_make' => 'Toyota',
                'car_model' => 'Corolla',
                'car_year' => 2023,
                'car_color' => 'Black',
                'licence_plate' => 'LHR-3456',
                'car_photo' => 'toyota_corolla.jpg',
                'driver_license_front' => 'license_front_4.jpg',
                'driver_license_back' => 'license_back_4.jpg',
                'license_verified' => 'pending',
                'verification_notes' => null,
                'verified_by' => null,
                'verified_at' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
            [
                'user_id' => 5,
                'car_make' => 'Honda',
                'car_model' => 'City',
                'car_year' => 2022,
                'car_color' => 'Blue',
                'licence_plate' => 'ISB-7890',
                'car_photo' => 'honda_city.jpg',
                'driver_license_front' => 'license_front_5.jpg',
                'driver_license_back' => 'license_back_5.jpg',
                'license_verified' => 'verified',
                'verification_notes' => 'Vehicle registration verified',
                'verified_by' => 'Admin User',
                'verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ];

        DB::table('cars')->insert($cars);
        
        $this->command->info('✅ 5 dummy cars inserted successfully!');
    }
}