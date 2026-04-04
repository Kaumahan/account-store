<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
     * Fetch latest messages with optimized eager loading.
     */
    public function index(): JsonResponse
    {
        $messages = Message::with('user:id,name,avatar') // Only select needed columns
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return response()->json($messages);
    }

    /**
     * Persist and broadcast a new message.
     */
    public function store(Request $request): JsonResponse
    {
        if (!Auth::check()) {
        return response()->json(['debug_error' => 'Auth check failed at controller'], 401);
    }
        $validated = $request->validate([
            'body' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'user_id' => Auth::id(),
            'body'    => $validated['body'],
        ]);

        // Load specific user data for the frontend payload
        $message->load('user:id,name,avatar');

        // Broadcast to others via Reverb
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }
}