<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Notifications\TicketStatusNotification;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        $query = SupportTicket::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->sort == 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tickets = $query->paginate(4)->withQueryString();

        return view('tickets.index', compact('tickets'));
    }


    public function create()
    {
        return view('tickets.create');
    }


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


    public function show(SupportTicket $ticket)
    {
        if ($ticket->user_id != auth()->id()) {
            abort(403);
        }

        return view('tickets.show', compact('ticket'));
    }


    public function updateStatus(Request $request, SupportTicket $ticket)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);


        $ticket->status = $request->status;

        $ticket->save();


        if ($ticket->user) {

            $ticket->user->notify(
                new TicketStatusNotification($ticket)
            );
        }


        return back()->with(
            'success',
            'Ticket status updated successfully'
        );
    }
}
