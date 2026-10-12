@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="text-muted">Welcome back, {{ Auth::user()->name }}!</span>
    </div>
</div>

<!-- Stats Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card bg-purple">
            <h5>Total Orders</h5>
            <h2>{{ $stats['total_orders'] }}</h2>
            <i class="fas fa-shopping-cart fa-2x float-end"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-blue">
            <h5>Pending Orders</h5>
            <h2>{{ $stats['pending_orders'] }}</h2>
            <i class="fas fa-clock fa-2x float-end"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-green">
            <h5>Total Spent</h5>
            <h2>${{ number_format($stats['total_spent'], 2) }}</h2>
            <i class="fas fa-dollar-sign fa-2x float-end"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card bg-orange">
            <h5>Open Tickets</h5>
            <h2>{{ $stats['open_tickets'] }}</h2>
            <i class="fas fa-ticket-alt fa-2x float-end"></i>
        </div>
    </div>
</div>

<!-- 💎 VIP CUSTOMER LOYALTY & REWARD TIER RADAR -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #fff;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center mb-2">
                            <span class="{{ $loyaltyBadge }} fs-6 px-3 py-2 me-3">
                                <i class="{{ $loyaltyIcon }} me-2"></i> {{ $loyaltyTier }}
                            </span>
                            <span class="text-warning fw-bold">
                                <i class="fas fa-coins me-1"></i> {{ number_format($rewardPoints) }} Reward Points
                            </span>
                        </div>
                        <h4 class="fw-bold mb-1">Welcome to Exclusive Customer Rewards</h4>
                        <p class="text-white-50 mb-3 small">
                            Earn 10 points for every $1 spent. Unlock free priority shipping & VIP support at Gold & Platinum levels!
                        </p>
                        <div class="mb-1 d-flex justify-content-between small text-white-50">
                            <span>Current Progress to Next Tier (${{ number_format($nextTierTarget) }})</span>
                            <span class="text-warning fw-bold">{{ $tierProgress }}%</span>
                        </div>
                        <div class="progress bg-secondary" style="height: 8px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $tierProgress }}%"></div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-3 mt-md-0 border-start border-secondary ps-md-4">
                        <div class="d-flex justify-content-around text-center">
                            <div>
                                <small class="text-white-50 d-block">Tier Status</small>
                                <span class="fs-5 fw-bold text-light">{{ explode(' ', $loyaltyTier)[0] }}</span>
                            </div>
                            <div>
                                <small class="text-white-50 d-block">Lifetime Orders</small>
                                <span class="fs-5 fw-bold text-light">{{ $stats['total_orders'] }}</span>
                            </div>
                            <div>
                                <small class="text-white-50 d-block">Points Value</small>
                                <span class="fs-5 fw-bold text-success">${{ number_format($rewardPoints * 0.01, 2) }}</span>
                            </div>
                        </div>
                        <div class="mt-3 text-center text-md-end">
                            <a href="{{ route('vault.index') }}" class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-wallet me-1"></i> Manage Saved Addresses & Cards
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 📊 CUSTOMER SPENDING ANALYTICS STUDIO (CHART.JS) -->
<div class="row mt-4">
    <!-- Monthly Spending Trend (Line/Bar Chart) -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-bold">
                    <i class="fas fa-chart-line text-primary me-2"></i> Monthly Spending History
                </h5>
                <span class="badge bg-light text-muted">Last 6 Months</span>
            </div>
            <div class="card-body">
                <div style="height: 260px;">
                    <canvas id="monthlySpendingChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Breakdown (Donut Chart) -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-dark fw-bold">
                    <i class="fas fa-chart-pie text-success me-2"></i> Order Status Mix
                </h5>
                <span class="badge bg-light text-muted">Donut</span>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <div style="height: 240px; width: 100%;">
                    <canvas id="statusDonutChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <!-- Recent Orders -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a>
                                    </td>
                                    <td>${{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No orders yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Support Tickets</h5>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentTickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a>
                                    </td>
                                    <td>{{ Str::limit($ticket->subject, 30) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ticket->status == 'open' ? 'success' : ($ticket->status == 'resolved' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No support tickets yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('tickets.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus-circle me-2"></i>New Support Ticket
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-user-edit me-2"></i>Update Profile
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-history me-2"></i>View Order History
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('vault.index') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-wallet me-2"></i>Address & Payments Vault
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Monthly Spending Line Chart
        const spendingCtx = document.getElementById('monthlySpendingChart');
        if (spendingCtx) {
            new Chart(spendingCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($spendingMonths) !!},
                    datasets: [{
                        label: 'Total Spent ($)',
                        data: {!! json_encode($spendingAmounts) !!},
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#1d4ed8',
                        pointRadius: 5,
                        borderWidth: 2.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) { return '$' + context.parsed.y.toFixed(2); }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { callback: function(val) { return '$' + val; } }
                        }
                    }
                }
            });
        }

        // 2. Order Status Mix Donut Chart
        const donutCtx = document.getElementById('statusDonutChart');
        if (donutCtx) {
            const statusLabels = {!! json_encode(array_keys($statusCounts)) !!};
            const statusData = {!! json_encode(array_values($statusCounts)) !!};

            new Chart(donutCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: ['#16a34a', '#eab308', '#dc2626', '#3b82f6'],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endsection