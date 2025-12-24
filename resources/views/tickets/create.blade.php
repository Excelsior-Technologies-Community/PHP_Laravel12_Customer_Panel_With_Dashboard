@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Create New Support Ticket</h1>
    <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Tickets
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf
            
            <div class="mb-3">
                <label for="subject" class="form-label">Subject *</label>
                <input type="text" class="form-control @error('subject') is-invalid @enderror" 
                       id="subject" name="subject" value="{{ old('subject') }}" required>
                @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="priority" class="form-label">Priority *</label>
                <select class="form-select @error('priority') is-invalid @enderror" 
                        id="priority" name="priority" required>
                    <option value="">Select Priority</option>
                    <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                    <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                    <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                </select>
                @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="message" class="form-label">Message *</label>
                <textarea class="form-control @error('message') is-invalid @enderror" 
                          id="message" name="message" rows="8" required>{{ old('message') }}</textarea>
                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Our support team will respond to your ticket within 24 hours.
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">What to include in your ticket:</h5>
    </div>
    <div class="card-body">
        <ul class="mb-0">
            <li>Clear description of the issue</li>
            <li>Steps to reproduce the problem</li>
            <li>Error messages (if any)</li>
            <li>Account/Order number (if applicable)</li>
            <li>Screenshots (you can mention you'll attach them in follow-up)</li>
        </ul>
    </div>
</div>
@endsection