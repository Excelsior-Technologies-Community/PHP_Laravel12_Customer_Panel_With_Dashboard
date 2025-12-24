@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Support Ticket Details</h1>
    <div>
        <a href="{{ route('tickets.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-2"></i>Back to Tickets
        </a>
        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>New Ticket
        </a>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Ticket #{{ $ticket->ticket_number }}</h5>
        <div>
            <span class="badge bg-{{ $ticket->priority == 'high' ? 'danger' : ($ticket->priority == 'medium' ? 'warning' : 'info') }} me-2">
                {{ ucfirst($ticket->priority) }} Priority
            </span>
            <span class="badge bg-{{ $ticket->status == 'open' ? 'success' : ($ticket->status == 'resolved' ? 'primary' : 'secondary') }}">
                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h6>Ticket Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Subject:</strong></td>
                        <td>{{ $ticket->subject }}</td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $ticket->created_at->format('F d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>{{ $ticket->updated_at->format('F d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Your Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $ticket->user->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $ticket->user->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Phone:</strong></td>
                        <td>{{ $ticket->user->phone ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="mb-4">
            <h6>Message:</h6>
            <div class="alert alert-light border">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-user-circle fa-2x text-primary me-3"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1">{{ $ticket->user->name }} <small class="text-muted">(You)</small></h6>
                        <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                        <p class="mt-2 mb-0">{{ $ticket->message }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- This section can be expanded for replies -->
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Our support team is reviewing your ticket. You'll receive updates via email.
        </div>
    </div>
</div>

<!-- You can add reply functionality here in the future -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add Reply</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Reply functionality will be implemented in the next version.
        </div>
        <form>
            <div class="mb-3">
                <textarea class="form-control" rows="4" placeholder="Add a reply to this ticket..." disabled></textarea>
            </div>
            <button type="submit" class="btn btn-primary" disabled>Submit Reply</button>
        </form>
    </div>
</div>
@endsection