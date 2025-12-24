@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Order Details</h1>
    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>Order Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Order Date:</strong></td>
                        <td>{{ $order->created_at->format('F d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Total Amount:</strong></td>
                        <td>${{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Customer Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $order->user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $order->user->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $order->user->phone ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        @if($order->notes)
        <div class="mt-4">
            <h6>Order Notes</h6>
            <div class="alert alert-info">
                {{ $order->notes }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection