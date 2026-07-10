<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });

    // Orders Routes
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show');
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])
            ->name('updateStatus');
    });

    // Support Tickets Routes
    Route::resource('tickets', SupportTicketController::class)->except(['edit', 'update', 'destroy']);

    Route::put('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('tickets.updateStatus');

    // Notification Routes
    Route::get('/notifications/read/{id}', [NotificationController::class, 'read'])
        ->name('notification.read');
});

Route::middleware(['auth','verified'])->group(function () {

    Route::get('/orders/{order}/invoice',
        [InvoiceController::class,'download'])
        ->name('orders.invoice');

});

Route::get('/test-order-notification/{order}', function ($id) {

    $order = \App\Models\Order::findOrFail($id);

    $order->status = 'completed';
    $order->save();

    $order->user->notify(
        new \App\Notifications\OrderStatusNotification($order)
    );

    return "Notification Sent";
});

require __DIR__ . '/auth.php';
