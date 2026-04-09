<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Events\MessageSent;
use App\Models\Message;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayMongoWebhookController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleController;

// --- Public Routes ---
Route::view('/', 'welcome');
Route::view('/payment/success', 'success');
Route::view('/payment/failed', 'failed');
Route::get('/', [ProductController::class, 'index']);
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::post('/paymongo/callback', [PayMongoWebhookController::class, 'handle']);

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    Route::post('/redeem', [RedemptionController::class, 'store'])->name('redeem.store')->middleware('throttle:2,1');
    // --- Admin Only Section ---
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/payouts', [AdminController::class, 'payouts'])->name('admin.payouts');
        Route::post('/payouts/{redemption}/approve', [AdminController::class, 'approve'])->name('admin.approve');
    });

    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('/inventory', [StockController::class, 'index'])->name('stocks.index');
    Route::post('/inventory/store', [StockController::class, 'store'])->name('stocks.store');
    Route::get('/inventory/data', [ProductController::class, 'index']);
    // web.php
    Route::get('/inventory', [ProductController::class, 'ownProducts'])->name('stocks.index');


    Route::post('/inventory/store', [ProductController::class, 'store'])->name('products.store');
    Route::delete('/inventory/{product}', [ProductController::class, 'destroy']);


    // Product CRUD (For the Table)
    Route::post('/inventory/products', [ProductController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/products/{product}/edit', [ProductController::class, 'edit'])->name('inventory.edit');
    Route::put('/inventory/products/{product}', [ProductController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/products/{product}', [ProductController::class, 'destroy'])->name('inventory.destroy');
    // Get Chat History
    Route::get('/chat/messages', function () {
        return Message::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();
    });

    // Send Message (CLEANED UP - Only one route now)
    Route::post('/chat', function (Request $request) {

        $request->validate(['body' => 'required|string']);

        $message = Message::create([
            'user_id' => Auth::id(),
            'body' => $request->body
        ]);

        // Load user so Vue knows WHO sent it
        $message->load('user');

        // Broadcast to everyone else
        // NOTE: Use broadcast(new MessageSent($message)) without ->toOthers() 
        // if you want to see your own message appear via Echo.
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    });

    Route::post('/checkout/{product}', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/my-purchases', [PaymentController::class, 'purchases'])->name('purchases.index');

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});
