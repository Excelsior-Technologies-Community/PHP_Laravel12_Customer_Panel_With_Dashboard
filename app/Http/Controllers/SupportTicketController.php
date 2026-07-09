<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of tickets.
     */
    public function index(Request $request)
    {
        $query = SupportTicket::where('user_id', auth()->id());

        // Search by ticket number or subject
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        // Priority Filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        if ($request->sort == 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate(4)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }

    /**
     * Show create ticket form.
     */
    public function create()
    {
        return view('tickets.create');
    }

    /**
     * Store new ticket.
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'ticket_number' => 'TICKET-' . strtoupper(Str::random(8)),
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => 'open',
        ]);

        return redirect()
            ->route('tickets.show', $ticket)
            ->with('success', 'Support ticket created successfully.');
    }

    /**
     * Display ticket details.
     */
    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id != auth()->id()) {
            abort(403);
        }

        return view('tickets.show', compact('ticket'));
    }
}