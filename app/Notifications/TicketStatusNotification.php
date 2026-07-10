<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketStatusNotification extends Notification
{
    use Queueable;


    public $ticket;


    public function __construct($ticket)
    {
        $this->ticket = $ticket;
    }


    public function via($notifiable)
    {
        return ['database'];
    }


    public function toDatabase($notifiable)
    {
        return [

            'title' => 'Support Ticket Updated',

            'message' =>
                'Your ticket '
                .$this->ticket->ticket_number.
                ' status changed to '
                .ucfirst($this->ticket->status),

            'ticket_id' => $this->ticket->id

        ];
    }
}