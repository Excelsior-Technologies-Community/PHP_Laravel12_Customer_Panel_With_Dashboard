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
        Route::put('/{order}/status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
        Route::post('/{order}/reorder', [OrderController::class, 'reorder'])->name('reorder');
    });

    // Support Tickets Routes
    Route::resource('tickets', SupportTicketController::class)->except(['edit', 'update', 'destroy']);

    Route::put('/tickets/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('tickets.updateStatus');

    // Multi-Address Book & Saved Payment Methods Vault
    Route::prefix('vault')->name('vault.')->group(function () {
        Route::get('/', [\App\Http\Controllers\CustomerVaultController::class, 'index'])->name('index');
        Route::post('/addresses', [\App\Http\Controllers\CustomerVaultController::class, 'storeAddress'])->name('addresses.store');
        Route::patch('/addresses/{address}/default', [\App\Http\Controllers\CustomerVaultController::class, 'setDefaultAddress'])->name('addresses.default');
        Route::delete('/addresses/{address}', [\App\Http\Controllers\CustomerVaultController::class, 'destroyAddress'])->name('addresses.destroy');

        Route::post('/payments', [\App\Http\Controllers\CustomerVaultController::class, 'storePaymentMethod'])->name('payments.store');
        Route::patch('/payments/{method}/default', [\App\Http\Controllers\CustomerVaultController::class, 'setDefaultPaymentMethod'])->name('payments.default');
        Route::delete('/payments/{method}', [\App\Http\Controllers\CustomerVaultController::class, 'destroyPaymentMethod'])->name('payments.destroy');
    });

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
