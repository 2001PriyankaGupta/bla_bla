<?php
// database/seeders/FareConfigurationSeeder.php

namespace Database\Seeders;

use App\Models\FareConfiguration;
use Illuminate\Database\Seeder;

class FareConfigurationSeeder extends Seeder
{
    public function run()
    {
        FareConfiguration::create([
            'base_fare' => 2.50,
            'per_km_charge' => 1.20,
            'waiting_fee' => 0.30,
            'home_pickup_fee' => 1.50,
            'night_holiday_surcharge' => 2.00,
            'is_active' => true,
        ]);
    }
}