<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SupportTicket;
use App\Models\Faq;
use App\Models\QuickReplyTemplate;
use App\Models\User;

class SupportSeeder extends Seeder
{
    public function run()
    {
        // Create sample support agents
        $agents = User::factory()->count(2)->create([
            'user_type' => 'support_agent',
            'phone' => '4589652369'
        ]);

        // Create sample tickets
        SupportTicket::factory()->count(10)->create();

        // Create FAQs
        $faqs = [
            [
                'question' => 'How long does refund processing take?',
                'answer' => 'Refunds typically take 2-3 business days to process after approval.',
                'category' => 'Payments',
                'order' => 1
            ],
            [
                'question' => 'What should I do if I lost an item?',
                'answer' => 'Contact our support team immediately with details about the lost item and we will assist you in locating it.',
                'category' => 'Lost Items',
                'order' => 2
            ],
            // Add more FAQs as needed
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }

        // Create quick reply templates
        $templates = [
            [
                'name' => 'Refund Processing',
                'content' => 'Refund takes 2-3 days to process and will be credited to your original payment method.'
            ],
            [
                'name' => 'Issue Investigation',
                'content' => 'We are currently investigating your issue and will provide an update within 24 hours.'
            ],
            [
                'name' => 'Account Verification',
                'content' => 'We need to verify your account information for security purposes. Please provide the requested details.'
            ]
        ];

        foreach ($templates as $template) {
            QuickReplyTemplate::create($template);
        }
    }
}