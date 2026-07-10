<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Notifications\OrderStatusNotification;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('user_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");

                if (is_numeric($search)) {
                    $q->orWhere('total_amount', $search);
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sort = $request->get('sort', 'latest');

        if ($sort == 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $orders = $query->paginate(5)->withQueryString();

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id != auth()->id()) {
            abort(403);
        }

        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required'
        ]);

        $order->status = $request->status;
        $order->save();

        if ($order->user) {
            $order->user->notify(
                new OrderStatusNotification($order)
            );
        }

        return back()->with(
            'success',
            'Order status updated successfully'
        );
    }
}