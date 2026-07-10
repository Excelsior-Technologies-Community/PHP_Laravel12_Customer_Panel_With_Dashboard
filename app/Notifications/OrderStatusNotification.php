<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;


    public $order;


    public function __construct($order)
    {
        $this->order = $order;
    }


    public function via($notifiable)
    {
        return ['database'];
    }


    public function toDatabase($notifiable)
    {
        return [

            'title' => 'Order Status Updated',

            'message' =>
                'Your order '
                .$this->order->order_number.
                ' status changed to '
                .ucfirst($this->order->status),

            'order_id' => $this->order->id

        ];
    }
}