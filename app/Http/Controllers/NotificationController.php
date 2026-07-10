<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);

        $notification->markAsRead();

        if (isset($notification->data['order_id'])) {
            return redirect()->route('orders.show', $notification->data['order_id']);
        }

        if (isset($notification->data['ticket_id'])) {
            return redirect()->route('tickets.show', $notification->data['ticket_id']);
        }

        return redirect()->route('dashboard');
    }
}