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

<div class="row mt-4">
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
                        <a href="#" class="btn btn-outline-warning w-100">
                            <i class="fas fa-download me-2"></i>Download Invoice
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection