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

    // 1-Click Cancel Order (if pending/processing)
    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id != auth()->id()) abort(403);

        if (!in_array($order->status, ['pending', 'processing'])) {
            return back()->with('error', 'Only pending or processing orders can be cancelled.');
        }

        $order->status = 'cancelled';
        $order->notes = ($order->notes ? $order->notes . ' | ' : '') . 'Cancelled by Customer on ' . now()->format('d M Y, h:i A');
        $order->save();

        if ($order->user) {
            $order->user->notify(new OrderStatusNotification($order));
        }

        return back()->with('success', 'Order #' . $order->order_number . ' has been cancelled successfully.');
    }

    // 1-Click Re-Order (Creates duplicate active order)
    public function reorder(Order $order)
    {
        if ($order->user_id != auth()->id()) abort(403);

        $newOrder = Order::create([
            'user_id' => auth()->id(),
            'order_number' => 'ORD-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'total_amount' => $order->total_amount,
            'status' => 'pending',
            'carrier' => 'BlueDart Express',
            'tracking_number' => 'TRK-' . rand(10000000, 99999999),
            'estimated_delivery' => now()->addDays(rand(3, 5)),
            'current_stage' => 'placed',
            'notes' => 'Re-ordered duplicate of ' . $order->order_number,
        ]);

        return redirect()->route('orders.show', $newOrder->id)->with('success', 'Order #' . $newOrder->order_number . ' placed successfully via 1-Click Re-Order!');
    }
}