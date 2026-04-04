<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; 

use App\Events\MessageSent;
use App\Models\Message;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayMongoWebhookController;

use App\Http\Controllers\Auth\GoogleController;

// --- Public Routes ---
Route::get('/', [ProductController::class, 'index']);
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
Route::post('/paymongo/webhook', [PayMongoWebhookController::class, 'handle']);
Route::post('/paymongo/callback', [PayMongoWebhookController::class, 'handle']);

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    
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
    
    Route::post('/logout', function() {
        Auth::logout();
        return redirect('/');
    })->name('logout');
});