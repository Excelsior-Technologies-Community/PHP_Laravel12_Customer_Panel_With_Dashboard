<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Order;
use App\Models\SupportTicket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create test user
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password'),
            'phone' => '123-456-7890',
            'address' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'zip_code' => '10001',
            'status' => 'active',
        ]);

        // Create sample orders
        for ($i = 1; $i <= 5; $i++) {
            Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . Str::random(8),
                'total_amount' => rand(50, 500),
                'status' => ['pending', 'processing', 'completed', 'cancelled'][rand(0, 3)],
                'notes' => 'Sample order note ' . $i,
            ]);
        }

        // Create sample support tickets
        for ($i = 1; $i <= 3; $i++) {
            SupportTicket::create([
                'user_id' => $user->id,
                'ticket_number' => 'TICKET-' . Str::random(10),
                'subject' => 'Support Request ' . $i,
                'message' => 'This is a sample support ticket message for ticket number ' . $i,
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'status' => ['open', 'in_progress', 'resolved'][rand(0, 2)],
            ]);
        }

        $this->command->info('Test data seeded successfully!');
        $this->command->info('Test User: john@example.com / password');
    }
}