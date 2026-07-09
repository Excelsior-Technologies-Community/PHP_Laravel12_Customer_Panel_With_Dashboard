@extends('layouts.app')

@section('content')

    <div class="container-fluid">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">

            <div>
                <h2 class="fw-bold mb-1">
                    <i class="fas fa-ticket-alt text-primary me-2"></i>
                    Support Tickets
                </h2>

                <p class="text-muted mb-0">
                    Manage all your support requests in one place.
                </p>
            </div>

            <div>
                <a href="{{ route('tickets.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle me-2"></i>
                    New Ticket
                </a>
            </div>

        </div>

        @if(session('success'))

            <div class="alert alert-success alert-dismissible fade show shadow-sm">

                <i class="fas fa-check-circle me-2"></i>

                {{ session('success') }}

                <button class="btn-close" data-bs-dismiss="alert">
                </button>

            </div>

        @endif

        <!-- Search Card -->

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white">

                <h5 class="mb-0">

                    <i class="fas fa-search me-2 text-primary"></i>

                    Search & Filter

                </h5>

            </div>

            <div class="card-body">

                <form method="GET" action="{{ route('tickets.index') }}">

                    <div class="row g-3">

                        <!-- Search -->

                        <div class="col-lg-4">

                            <label class="form-label fw-semibold">

                                Search

                            </label>

                            <input type="text" name="search" class="form-control" placeholder="Ticket Number or Subject"
                                value="{{ request('search') }}">

                        </div>

                        <!-- Priority -->

                        <div class="col-lg-2">

                            <label class="form-label fw-semibold">

                                Priority

                            </label>

                            <select name="priority" class="form-select">

                                <option value="">All</option>

                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>
                                    Low
                                </option>

                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>
                                    Medium
                                </option>

                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>
                                    High
                                </option>

                            </select>

                        </div>

                        <!-- Status -->

                        <div class="col-lg-2">

                            <label class="form-label fw-semibold">

                                Status

                            </label>

                            <select name="status" class="form-select">

                                <option value="">All</option>

                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>
                                    Open
                                </option>

                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>
                                    Resolved
                                </option>

                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>
                                    Closed
                                </option>

                            </select>

                        </div>

                        <!-- Sort -->

                        <div class="col-lg-2">

                            <label class="form-label fw-semibold">

                                Sort

                            </label>

                            <select name="sort" class="form-select">

                                <option value="latest" {{ request('sort') != 'oldest' ? 'selected' : '' }}>
                                    Latest

                                </option>

                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>
                                    Oldest

                                </option>

                            </select>

                        </div>

                        <!-- Buttons -->

                        <div class="col-lg-2 d-flex align-items-end">

                            <button class="btn btn-primary w-100 me-2">

                                <i class="fas fa-search"></i>

                            </button>

                            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary w-100">

                                Reset

                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <!-- Ticket Table -->

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0">

                    <i class="fas fa-list me-2"></i>

                    Ticket List

                </h5>

                <span class="badge bg-light text-dark">

                    Total :

                    {{ $tickets->total() }}

                </span>

            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>

                            <th>Ticket Number</th>

                            <th>Subject</th>

                            <th>Priority</th>

                            <th>Status</th>

                            <th>Date</th>

                            <th width="120">Action</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($tickets as $key => $ticket)

                            <tr>

                                <td>
                                    {{ $tickets->firstItem() + $key }}
                                </td>

                                <td>
                                    <span class="fw-bold text-primary">
                                        {{ $ticket->ticket_number }}
                                    </span>
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ \Illuminate\Support\Str::limit($ticket->subject, 35) }}
                                    </div>

                                    <small class="text-muted">
                                        {{ \Illuminate\Support\Str::limit($ticket->message, 50) }}
                                    </small>
                                </td>

                                <td>

                                    @if($ticket->priority == 'high')

                                        <span class="badge bg-danger px-3 py-2">
                                            High
                                        </span>

                                    @elseif($ticket->priority == 'medium')

                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            Medium
                                        </span>

                                    @else

                                        <span class="badge bg-info text-dark px-3 py-2">
                                            Low
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    @if($ticket->status == 'open')

                                        <span class="badge bg-success px-3 py-2">
                                            Open
                                        </span>

                                    @elseif($ticket->status == 'resolved')

                                        <span class="badge bg-primary px-3 py-2">
                                            Resolved
                                        </span>

                                    @else

                                        <span class="badge bg-secondary px-3 py-2">
                                            Closed
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    {{ $ticket->created_at->format('d M, Y') }}

                                    <br>

                                    <small class="text-muted">

                                        {{ $ticket->created_at->format('h:i A') }}

                                    </small>

                                </td>

                                <td>

                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">

                                        <i class="fas fa-eye"></i>

                                        View

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7">

                                    <div class="text-center py-5">

                                        <i class="fas fa-ticket-alt fa-4x text-secondary mb-3"></i>

                                        <h5 class="text-muted">

                                            No Support Tickets Found

                                        </h5>

                                        <p class="text-muted">

                                            Create your first support ticket.

                                        </p>

                                        <a href="{{ route('tickets.create') }}" class="btn btn-primary">

                                            <i class="fas fa-plus-circle me-2"></i>

                                            Create Ticket

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="card-footer bg-white">

                <div class="row align-items-center">

                    <div class="col-md-6">

                        <small class="text-muted">

                            Showing

                            <strong>{{ $tickets->firstItem() ?? 0 }}</strong>

                            to

                            <strong>{{ $tickets->lastItem() ?? 0 }}</strong>

                            of

                            <strong>{{ $tickets->total() }}</strong>

                            tickets

                        </small>

                    </div>

                    <div class="col-md-6 d-flex justify-content-md-end mt-3 mt-md-0">

                        {{ $tickets->links() }}

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection