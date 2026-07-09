<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SupportTicket;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $stats = [
            'total_orders' => Order::where('user_id', $userId)->count(),

            'pending_orders' => Order::where('user_id', $userId)
                ->where('status', 'pending')
                ->count(),

            'completed_orders' => Order::where('user_id', $userId)
                ->where('status', 'completed')
                ->count(),

            'cancelled_orders' => Order::where('user_id', $userId)
                ->where('status', 'cancelled')
                ->count(),

            'today_orders' => Order::where('user_id', $userId)
                ->whereDate('created_at', Carbon::today())
                ->count(),

            'open_tickets' => SupportTicket::where('user_id', $userId)
                ->where('status', 'open')
                ->count(),

            'resolved_tickets' => SupportTicket::where('user_id', $userId)
                ->where('status', 'resolved')
                ->count(),

            'total_spent' => Order::where('user_id', $userId)
                ->where('status', 'completed')
                ->sum('total_amount'),
        ];

        $recentOrders = Order::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        $recentTickets = SupportTicket::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'recentOrders',
            'recentTickets'
        ));
    }
}