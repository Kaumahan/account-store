<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Events\MessageSent;
use App\Models\Message;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayMongoWebhookController;
use App\Http\Controllers\RedemptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\GoogleController;

/*
|--------------------------------------------------------------------------
| Public Routes (Throttled to prevent scraping/flooding)
|--------------------------------------------------------------------------
*/
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::view('/payment/success', 'success');
    Route::view('/payment/failed', 'failed');
});

// Authentication Throttling (Prevent brute-force attempts)
Route::middleware('throttle:10,1')->group(function () {
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('login.google');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
});

// Webhooks: Higher limit to allow PayMongo bursts, but still protected
Route::post('/paymongo/callback', [PayMongoWebhookController::class, 'handle'])
    ->middleware('throttle:100,1');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'throttle:120,1'])->group(function () {

    // Page to write the report
    Route::get('/payments/{payment}/report', [ReportController::class, 'create'])->name('payments.report');
    // Save the report
    Route::post('/payments/{payment}/report', [ReportController::class, 'store'])->name('reports.store');

    // --- High-Value Transactions (Heavily Protected) ---
    // Prevent automated balance drain or ticket-buying bots
    Route::post('/redeem', [RedemptionController::class, 'store'])
        ->name('redeem.store')
        ->middleware('throttle:2,1');

    Route::post('/checkout/{product}', [PaymentController::class, 'checkout'])
        ->name('checkout');

    // --- Real-time Chat Logic ---
    Route::get('/chat/messages', function () {
        return Message::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->reverse()
            ->values();
    });

    Route::post('/chat', function (Request $request) {
        $request->validate(['body' => 'required|string|max:1000']);

        $message = Message::create([
            'user_id' => Auth::id(),
            'body' => $request->body
        ]);

        $message->load('user');
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    })->middleware('throttle:15,1'); // Prevents chat spamming

    // --- Inventory & Product Management ---
    Route::post('/stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('/inventory/data', [ProductController::class, 'index']);
    Route::prefix('inventory')->group(function () {
        Route::get('/', [ProductController::class, 'ownProducts'])->name('stocks.index');
        Route::post('/store', [ProductController::class, 'store'])->name('products.store');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('inventory.destroy');

        // Product CRUD
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('inventory.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('inventory.update');
    });

    // --- User Profile/Vault ---
    Route::get('/my-purchases', [PaymentController::class, 'purchases'])->name('purchases.index');

    // --- Admin Section ---
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/payouts', [AdminController::class, 'payouts'])->name('admin.payouts');
        Route::post('/payouts/{redemption}/approve', [AdminController::class, 'approve'])->name('admin.approve');
    });

    Route::post('/logout', function () {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});