@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div>
        <h1 class="h2 mb-1">
            <i class="fas fa-truck text-primary me-2"></i>Order Tracking & Details
        </h1>
        <span class="text-muted small">Tracking Order <strong>#{{ $order->order_number }}</strong></span>
    </div>

    <div class="d-flex gap-2">
        <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-success">
            <i class="fas fa-file-pdf me-1"></i> Download Invoice
        </a>

        @if(in_array($order->status, ['pending', 'processing']))
        <form method="POST" action="{{ route('orders.cancel', $order->id) }}" onsubmit="return confirm('Are you sure you want to cancel this order?');">
            @csrf
            <button type="submit" class="btn btn-outline-danger">
                <i class="fas fa-times-circle me-1"></i> Cancel Order
            </button>
        </form>
        @else
        <form method="POST" action="{{ route('orders.reorder', $order->id) }}">
            @csrf
            <button type="submit" class="btn btn-warning text-dark fw-bold">
                <i class="fas fa-redo me-1"></i> 1-Click Re-Order
            </button>
        </form>
        @endif

        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- 🚚 LIVE INTERACTIVE SHIPMENT TRACKER & VISUAL STEPPER RADAR -->
<div class="card shadow-sm border-0 mb-4 overflow-hidden">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <div class="d-flex align-items-center">
            <i class="fas fa-shipping-fast fa-lg text-info me-2"></i>
            <div>
                <h6 class="mb-0 fw-bold">Live Shipment Tracker</h6>
                <small class="text-white-50">Carrier: <strong>{{ $order->carrier ?? 'BlueDart Express' }}</strong> | Tracking: <code class="text-warning">{{ $order->tracking_number ?? ('TRK-' . substr(md5($order->id), 0, 8)) }}</code></small>
            </div>
        </div>
        <div>
            @if($order->status === 'cancelled')
                <span class="badge bg-danger fs-6 px-3 py-2"><i class="fas fa-ban me-1"></i> Shipment Cancelled</span>
            @elseif($order->status === 'completed')
                <span class="badge bg-success fs-6 px-3 py-2"><i class="fas fa-check-circle me-1"></i> Delivered</span>
            @else
                <span class="badge bg-primary fs-6 px-3 py-2 animate-pulse">
                    <i class="fas fa-hourglass-half me-1"></i> Arriving in {{ $order->estimated_delivery ? $order->estimated_delivery->diffForHumans() : '3-4 business days' }}
                </span>
            @endif
        </div>
    </div>

    <div class="card-body p-4 bg-light">
        <!-- Visual Stepper Progress Bar -->
        <style>
            .stepper-wrapper {
                display: flex;
                justify-content: space-between;
                position: relative;
                margin-bottom: 25px;
            }
            .stepper-item {
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                flex: 1;
            }
            .stepper-item::before {
                position: absolute;
                content: "";
                border-bottom: 3px solid #cbd5e1;
                width: 100%;
                top: 20px;
                left: -50%;
                z-index: 1;
            }
            .stepper-item:first-child::before {
                content: none;
            }
            .stepper-item .step-counter {
                position: relative;
                z-index: 2;
                display: flex;
                justify-content: center;
                align-items: center;
                width: 42px;
                height: 42px;
                border-radius: 50%;
                background: #e2e8f0;
                color: #64748b;
                font-weight: bold;
                font-size: 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            }
            .stepper-item.completed .step-counter {
                background: #16a34a;
                color: #ffffff;
            }
            .stepper-item.completed::before {
                border-color: #16a34a;
            }
            .stepper-item.active .step-counter {
                background: #2563eb;
                color: #ffffff;
                box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.2);
            }
            .stepper-item.cancelled .step-counter {
                background: #dc2626;
                color: #ffffff;
            }
        </style>

        @php
            $isCancelled = $order->status === 'cancelled';
            $isPending = in_array($order->status, ['pending']);
            $isProcessing = in_array($order->status, ['processing']);
            $isCompleted = in_array($order->status, ['completed']);
        @endphp

        <div class="stepper-wrapper mt-3">
            <!-- Step 1: Placed -->
            <div class="stepper-item {{ $isCancelled ? 'cancelled' : 'completed' }}">
                <div class="step-counter"><i class="fas fa-file-invoice"></i></div>
                <div class="step-name mt-2 fw-bold small text-dark">Order Placed</div>
                <div class="text-muted small" style="font-size: 0.75rem;">{{ $order->created_at->format('d M, h:i A') }}</div>
            </div>

            <!-- Step 2: Processing -->
            <div class="stepper-item {{ $isCancelled ? 'cancelled' : ($isProcessing || $isCompleted ? 'completed' : ($isPending ? 'active' : '')) }}">
                <div class="step-counter"><i class="fas fa-boxes"></i></div>
                <div class="step-name mt-2 fw-bold small text-dark">Processing</div>
                <div class="text-muted small" style="font-size: 0.75rem;">Fulfillment Hub</div>
            </div>

            <!-- Step 3: Shipped -->
            <div class="stepper-item {{ $isCancelled ? 'cancelled' : ($isCompleted ? 'completed' : ($isProcessing ? 'active' : '')) }}">
                <div class="step-counter"><i class="fas fa-shipping-fast"></i></div>
                <div class="step-name mt-2 fw-bold small text-dark">Shipped</div>
                <div class="text-muted small" style="font-size: 0.75rem;">In Transit</div>
            </div>

            <!-- Step 4: Out for Delivery -->
            <div class="stepper-item {{ $isCancelled ? 'cancelled' : ($isCompleted ? 'completed' : '') }}">
                <div class="step-counter"><i class="fas fa-truck-moving"></i></div>
                <div class="step-name mt-2 fw-bold small text-dark">Out for Delivery</div>
                <div class="text-muted small" style="font-size: 0.75rem;">Local Courier</div>
            </div>

            <!-- Step 5: Delivered -->
            <div class="stepper-item {{ $isCancelled ? 'cancelled' : ($isCompleted ? 'completed' : '') }}">
                <div class="step-counter"><i class="fas fa-check"></i></div>
                <div class="step-name mt-2 fw-bold small text-dark">Delivered</div>
                <div class="text-muted small" style="font-size: 0.75rem;">{{ $isCompleted ? $order->updated_at->format('d M, h:i A') : 'Pending' }}</div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded border">
            <div>
                <span class="text-muted small d-block">Dispatch & Carrier Information:</span>
                <strong>{{ $order->carrier ?? 'BlueDart Express Air Courier' }}</strong>
                <span class="badge bg-light text-dark ms-2 font-monospace">{{ $order->tracking_number ?? 'BLUEDART-849204' }}</span>
            </div>
            <a href="{{ $order->tracking_url }}" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-external-link-alt me-1"></i> Live Trace with Carrier
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-bold">Order Summary & Payment Information</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 border-end">
                <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-info-circle me-1"></i> Order Information</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">Order Number:</td>
                        <td><strong>{{ $order->order_number }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Order Date:</td>
                        <td>{{ $order->created_at->format('F d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status:</td>
                        <td>
                            <span class="badge bg-{{ $order->status == 'completed' ? 'success' : ($order->status == 'pending' ? 'warning text-dark' : ($order->status == 'cancelled' ? 'danger' : 'info')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Total Amount:</td>
                        <td><h4 class="text-primary fw-bold mb-0">${{ number_format($order->total_amount, 2) }}</h4></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6 ps-md-4">
                <h6 class="fw-bold text-secondary mb-3"><i class="fas fa-map-marker-alt me-1"></i> Delivery & Customer</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="text-muted" width="40%">Customer:</td>
                        <td><strong>{{ $order->user->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email:</td>
                        <td>{{ $order->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Phone:</td>
                        <td>{{ $order->user->phone ?? '987-654-3210' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Delivery Address:</td>
                        <td>{{ $order->user->address ?? 'Default Delivery Address on File' }}, {{ $order->user->city ?? '' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        @if($order->notes)
        <div class="mt-4 pt-3 border-top">
            <h6 class="fw-bold text-secondary"><i class="fas fa-sticky-note me-1"></i> Notes & Audit Trail</h6>
            <div class="alert alert-secondary mb-0">
                {{ $order->notes }}
            </div>
        </div>
        @endif

        @if(auth()->id() == $order->user_id)
        <div class="mt-4 pt-3 border-top">
            <h6 class="fw-bold text-secondary mb-3">Simulation Controls (Update Status)</h6>
            <form method="POST" action="{{ route('orders.updateStatus', $order->id) }}">
                @csrf
                @method('PUT')
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <select name="status" class="form-select">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed / Delivered</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Status
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection