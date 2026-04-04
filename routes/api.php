<?php

use App\Http\Controllers\PayMongoWebhookController;
use Illuminate\Support\Facades\Route;

// This will be accessible at: your-site.com/api/paymongo/webhook
Route::post('/paymongo/webhook', [PayMongoWebhookController::class, 'handle']);