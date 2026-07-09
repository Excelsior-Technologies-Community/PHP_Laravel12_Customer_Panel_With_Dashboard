@extends('layouts.app')

@section('content')

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-shopping-cart me-2"></i>
            My Orders
        </h1>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <form method="GET" action="{{ route('orders.index') }}">
                <div class="row g-3">

                    <!-- Search -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">
                            Search
                        </label>

                        <input type="text" name="search" class="form-control" placeholder="Order Number, Status or Amount"
                            value="{{ request('search') }}">
                    </div>

                    <!-- Status -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="">All Status</option>

                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>

                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>
                    </div>

                    <!-- Sort -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">
                            Sort
                        </label>

                        <select name="sort" class="form-select">

                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                Latest First
                            </option>

                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                Oldest First
                            </option>

                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-2 d-flex align-items-end">

                        <button type="submit" class="btn btn-primary me-2 w-100">

                            <i class="fas fa-search"></i>
                            Search
                        </button>

                        <a href="{{ route('orders.index') }}" class="btn btn-secondary w-100">

                            Reset

                        </a>

                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="card shadow-sm">

        <div class="card-header bg-white">
            <h5 class="mb-0">
                Order List
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">

                        <tr>

                            <th width="5%">#</th>

                            <th>Order Number</th>

                            <th>Total Amount</th>

                            <th>Status</th>

                            <th>Created Date</th>

                            <th width="15%">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($orders as $key => $order)

                            <tr>

                                <td>
                                    {{ $orders->firstItem() + $key }}
                                </td>

                                <td>

                                    <strong>

                                        {{ $order->order_number }}

                                    </strong>

                                </td>

                                <td>

                                    ${{ number_format($order->total_amount, 2) }}

                                </td>

                                <td>

                                    @if($order->status == 'completed')

                                        <span class="badge bg-success">
                                            Completed
                                        </span>

                                    @elseif($order->status == 'pending')

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    @elseif($order->status == 'cancelled')

                                        <span class="badge bg-danger">
                                            Cancelled
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">

                                            {{ ucfirst($order->status) }}

                                        </span>

                                    @endif

                                </td>

                                <td>

                                    {{ $order->created_at->format('d M Y') }}

                                </td>

                                <td>

                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">

                                        <i class="fas fa-eye"></i>

                                        View

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center text-muted">

                                    No orders found.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center flex-wrap mt-4">

                <div class="text-muted mb-2">

                    Showing

                    <strong>{{ $orders->firstItem() ?? 0 }}</strong>

                    to

                    <strong>{{ $orders->lastItem() ?? 0 }}</strong>

                    of

                    <strong>{{ $orders->total() }}</strong>

                    orders

                </div>

                <div>

                    {{ $orders->links() }}

                </div>

            </div>

        </div>

    </div>

@endsection