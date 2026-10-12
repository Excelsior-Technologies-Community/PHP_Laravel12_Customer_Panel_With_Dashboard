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

        // Calculate VIP Loyalty & Reward Tier based on total spent
        $totalSpent = $stats['total_spent'];
        $rewardPoints = floor($totalSpent * 10); // 10 points per dollar
        $loyaltyTier = 'Bronze';
        $loyaltyBadge = 'badge bg-secondary';
        $loyaltyIcon = 'fas fa-medal';
        $tierProgress = 25;
        $nextTierTarget = 500;

        if ($totalSpent >= 2500) {
            $loyaltyTier = 'Platinum VIP';
            $loyaltyBadge = 'badge bg-dark text-warning border border-warning';
            $loyaltyIcon = 'fas fa-gem';
            $tierProgress = 100;
            $nextTierTarget = 2500;
        } elseif ($totalSpent >= 1000) {
            $loyaltyTier = 'Gold Elite';
            $loyaltyBadge = 'badge bg-warning text-dark';
            $loyaltyIcon = 'fas fa-crown';
            $tierProgress = min(100, round(($totalSpent / 2500) * 100));
            $nextTierTarget = 2500;
        } elseif ($totalSpent >= 500) {
            $loyaltyTier = 'Silver Member';
            $loyaltyBadge = 'badge bg-info text-white';
            $loyaltyIcon = 'fas fa-award';
            $tierProgress = min(100, round(($totalSpent / 1000) * 100));
            $nextTierTarget = 1000;
        } else {
            $tierProgress = min(100, round(($totalSpent / 500) * 100));
        }

        // Monthly Spending Analytics (Last 6 Months)
        $spendingMonths = [];
        $spendingAmounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $spendingMonths[] = $monthDate->format('M Y');
            $spendingAmounts[] = (float) Order::where('user_id', $userId)
                ->where('status', 'completed')
                ->whereYear('created_at', $monthDate->year)
                ->whereMonth('created_at', $monthDate->month)
                ->sum('total_amount');
        }

        // Order Status Breakdown for Donut Chart
        $statusCounts = [
            'Completed' => $stats['completed_orders'],
            'Pending' => $stats['pending_orders'],
            'Cancelled' => $stats['cancelled_orders'],
            'Processing' => Order::where('user_id', $userId)->where('status', 'processing')->count(),
        ];

        return view('dashboard', compact(
            'stats',
            'recentOrders',
            'recentTickets',
            'loyaltyTier',
            'loyaltyBadge',
            'loyaltyIcon',
            'rewardPoints',
            'tierProgress',
            'nextTierTarget',
            'spendingMonths',
            'spendingAmounts',
            'statusCounts'
        ));
    }
}