<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition()
    {
        // Get random user IDs for customer and agent
        $customerIds = User::where('user_type', 'customer')->pluck('id')->toArray();
        $agentIds = User::where('user_type', 'support_agent')->pluck('id')->toArray();
        
        // If no users exist, create some defaults
        if (empty($customerIds)) {
            $customerIds = [1, 2, 3];
        }
        if (empty($agentIds)) {
            $agentIds = [4, 5];
        }

        $priorities = ['Low', 'Medium', 'High'];
        $statuses = ['Open', 'In Progress', 'Closed'];
        
        $subjects = [
            'Issue with payment processing',
            'Lost item during ride',
            'Account access problem',
            'Ride cancellation issue',
            'Safety concern',
            'Driver behavior complaint',
            'Payment refund request',
            'App not working properly',
            'Rating system issue',
            'Promo code not applying'
        ];

        return [
            'user_id' => $this->faker->randomElement($customerIds),
            'subject' => $this->faker->randomElement($subjects),
            'description' => $this->faker->paragraph(3),
            'priority' => $this->faker->randomElement($priorities),
            'status' => $this->faker->randomElement($statuses),
            'assigned_to' => $this->faker->randomElement($agentIds),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}