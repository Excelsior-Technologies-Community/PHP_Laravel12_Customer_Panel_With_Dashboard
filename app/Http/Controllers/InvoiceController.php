<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        $pdf = Pdf::loadView('orders.invoice', compact('order'));

        return $pdf->download(
            'Invoice-'.$order->order_number.'.pdf'
        );
    }
}