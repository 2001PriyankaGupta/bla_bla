<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    public function run()
    {
        $reviews = [
            [
                'booking_id' => 1,
                'type' => 'driver',
                'rating' => 5,
                'comment' => 'Great ride!',
                'driver_id' => 2,
                'user_id' => 3,
                'reviewed_by' => 3,
            ],
            [
                'booking_id' => 2,
                'type' => 'passenger',
                'rating' => 4,
                'comment' => 'Good passenger, polite.',
                'driver_id' => 5,
                'user_id' => 6,
                'reviewed_by' => 5,
            ],
            [
                'booking_id' => 3,
                'type' => 'driver',
                'rating' => 3,
                'comment' => 'Ride was okay.',
                'driver_id' => 7,
                'user_id' => 8,
                'reviewed_by' => 8,
            ],
            [
                'booking_id' => 4,
                'type' => 'passenger',
                'rating' => 5,
                'comment' => 'Very friendly passenger!',
                'driver_id' => 9,
                'user_id' => 10,
                'reviewed_by' => 9,
            ],
            [
                'booking_id' => 5,
                'type' => 'driver',
                'rating' => 2,
                'comment' => 'Driver came late.',
                'driver_id' => 11,
                'user_id' => 12,
                'reviewed_by' => 12,
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }
    }
}
