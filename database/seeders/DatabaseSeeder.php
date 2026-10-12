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
        // Check tables
        \App\Models\Order::checkAndAddShipmentColumns();
        \App\Models\CustomerAddress::checkAndCreateTable();
        \App\Models\CustomerPaymentMethod::checkAndCreateTable();

        // Create or get test user
        $user = User::firstOrCreate(
            ['email' => 'john@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password'),
                'phone' => '987-654-3210',
                'address' => '704 Palm Grove Heights, SG Highway',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'zip_code' => '380015',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Carriers pool
        $carriers = ['BlueDart Express', 'FedEx', 'DHL Express', 'Delhivery'];

        // Sample rich orders spanning last 6 months for Chart.js analytics
        $sampleOrders = [
            ['amount' => 349.99, 'status' => 'completed', 'carrier' => 'BlueDart Express', 'months_ago' => 5],
            ['amount' => 189.50, 'status' => 'completed', 'carrier' => 'FedEx', 'months_ago' => 4],
            ['amount' => 520.00, 'status' => 'completed', 'carrier' => 'DHL Express', 'months_ago' => 3],
            ['amount' => 275.20, 'status' => 'completed', 'carrier' => 'BlueDart Express', 'months_ago' => 2],
            ['amount' => 640.80, 'status' => 'completed', 'carrier' => 'FedEx', 'months_ago' => 1],
            ['amount' => 149.00, 'status' => 'processing', 'carrier' => 'BlueDart Express', 'months_ago' => 0],
            ['amount' => 89.99, 'status' => 'pending', 'carrier' => 'Delhivery', 'months_ago' => 0],
        ];

        foreach ($sampleOrders as $idx => $ord) {
            $createdDate = now()->subMonths($ord['months_ago'])->subDays(rand(2, 20));
            Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => $ord['amount'],
                'status' => $ord['status'],
                'carrier' => $ord['carrier'],
                'tracking_number' => 'TRK-' . rand(10000000, 99999999),
                'estimated_delivery' => now()->addDays(rand(2, 5)),
                'current_stage' => $ord['status'] === 'completed' ? 'delivered' : ($ord['status'] === 'processing' ? 'shipped' : 'placed'),
                'notes' => 'Expedited priority delivery order #' . ($idx + 1),
                'created_at' => $createdDate,
                'updated_at' => $createdDate,
            ]);
        }

        // Seed Default Addresses
        \App\Models\CustomerAddress::firstOrCreate(
            ['user_id' => $user->id, 'title' => 'Home'],
            [
                'recipient_name' => 'John Doe',
                'phone' => '+91 98765 43210',
                'address_line1' => '704 Palm Grove Heights, SG Highway',
                'address_line2' => 'Opp. Grand Bhagwati',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'zip_code' => '380015',
                'country' => 'India',
                'is_default' => true,
            ]
        );

        \App\Models\CustomerAddress::firstOrCreate(
            ['user_id' => $user->id, 'title' => 'Office'],
            [
                'recipient_name' => 'John Doe (Tech Lead)',
                'phone' => '+91 98765 43211',
                'address_line1' => 'Tower B, 5th Floor, Mindspace IT Park',
                'address_line2' => 'Satellite Road',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'zip_code' => '380054',
                'country' => 'India',
                'is_default' => false,
            ]
        );

        // Seed Payment Shortcuts
        \App\Models\CustomerPaymentMethod::firstOrCreate(
            ['user_id' => $user->id, 'last_four' => '4242'],
            [
                'method_type' => 'card',
                'card_brand' => 'Visa',
                'holder_name' => 'John Doe',
                'expiry_month' => '08',
                'expiry_year' => '28',
                'is_default' => true,
            ]
        );

        \App\Models\CustomerPaymentMethod::firstOrCreate(
            ['user_id' => $user->id, 'upi_id' => 'johndoe@okaxis'],
            [
                'method_type' => 'upi',
                'is_default' => false,
            ]
        );

        // Seed Sample support ticket
        SupportTicket::firstOrCreate(
            ['user_id' => $user->id, 'subject' => 'Delivery query regarding BlueDart tracking'],
            [
                'ticket_number' => 'TICKET-' . strtoupper(Str::random(8)),
                'message' => 'Please confirm if my latest order requires signature upon delivery.',
                'priority' => 'medium',
                'status' => 'open',
            ]
        );
    }
}