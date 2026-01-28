<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $bookings = [
            [
                'ride_id' => 1,
                'user_id' => 3,
                'seats_booked' => 2,
                'total_price' => 300.00,
                'status' => 'confirmed',
                'special_requests' => 'Need window seat',
                'approved_at' => now(),
            ],
            [
                'ride_id' => 2,
                'user_id' => 4,
                'seats_booked' => 1,
                'total_price' => 150.00,
                'status' => 'pending',
                'special_requests' => null,
            ],
            [
                'ride_id' => 3,
                'user_id' => 5,
                'seats_booked' => 3,
                'total_price' => 450.00,
                'status' => 'rejected',
                'rejected_at' => now(),
                'rejection_reason' => 'Seats not available',
            ],
            [
                'ride_id' => 4,
                'user_id' => 6,
                'seats_booked' => 1,
                'total_price' => 200.00,
                'status' => 'completed',
                'approved_at' => now(),
            ],
        ];

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }
}
