<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display the chat interface using pages-user-profile layout.
     */
    public function index(Request $request)
    {
        $currentUserId = Auth::id();

        // Get all team members/users except current user
        $users = User::where('id', '!=', $currentUserId)
            ->orderBy('name')
            ->get();

        // Active contact for chat
        $receiverId = $request->query('user_id', $users->first()?->id);
        $activeUser = $receiverId ? User::find($receiverId) : null;

        $messages = collect();
        if ($activeUser) {
            // Fetch messages exchanged between current user & active contact
            $messages = Message::where(function ($q) use ($currentUserId, $activeUser) {
                $q->where('sender_id', $currentUserId)->where('receiver_id', $activeUser->id);
            })->orWhere(function ($q) use ($currentUserId, $activeUser) {
                $q->where('sender_id', $activeUser->id)->where('receiver_id', $currentUserId);
            })->orderBy('created_at', 'asc')->get();

            // Mark incoming messages from active contact as read
            Message::where('sender_id', $activeUser->id)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        // Attach unread counts to contacts
        foreach ($users as $user) {
            $user->unread_count = Message::where('sender_id', $user->id)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->count();
        }

        return view('chat.index', compact('users', 'activeUser', 'messages'));
    }

    /**
     * Store a newly sent message.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $message->load('sender');
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'html' => view('chat.partials.message_single', compact('message'))->render()
            ]);
        }

        return redirect()->route('chat.index', ['user_id' => $validated['receiver_id']]);
    }

    /**
     * Fetch updated messages via AJAX.
     */
    public function fetchMessages(User $user)
    {
        $currentUserId = Auth::id();

        $messages = Message::where(function ($q) use ($currentUserId, $user) {
            $q->where('sender_id', $currentUserId)->where('receiver_id', $user->id);
        })->orWhere(function ($q) use ($currentUserId, $user) {
            $q->where('sender_id', $user->id)->where('receiver_id', $currentUserId);
        })->orderBy('created_at', 'asc')->get();

        // Mark unread as read
        Message::where('sender_id', $user->id)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'status' => 'success',
            'html' => view('chat.partials.messages_feed', compact('messages'))->render()
        ]);
    }

    /**
     * Delete a chat message.
     */
    public function destroy(Message $message)
    {
        $currentUser = Auth::user();

        if ($message->sender_id !== $currentUser->id && !$currentUser->isAdmin()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to delete this message.'
            ], 403);
        }

        $messageId = $message->id;
        $message->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message_id' => $messageId,
            ]);
        }

        return redirect()->back()->with('success', 'Message deleted.');
    }
}
