<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $currentUser = Auth::user();

        // Get users who have message history with the current user
        $users = User::where('id', '!=', $currentUser->id)
            ->whereHas('sentMessages', function($query) use ($currentUser) {
                $query->where('receiver_id', $currentUser->id);
            })
            ->orWhereHas('receivedMessages', function($query) use ($currentUser) {
                $query->where('sender_id', $currentUser->id);
            })
            ->withCount(['sentMessages' => function($query) use ($currentUser) {
                $query->where('receiver_id', $currentUser->id)
                      ->where('is_read_by_receiver', false);
            }])
            ->get();

        // Get groups the user is a member of
        $groups = $currentUser->groups()
            ->withCount(['messages' => function($query) {
                $query->whereNull('parent_id');
            }])
            ->get();

        // Get recent messages
        $messages = Message::forUser($currentUser->id)
            ->whereNull('parent_id')
            ->with(['sender', 'receiver', 'replies', 'comments'])
            ->latest()
            ->paginate(20);

        return view('messages.index', compact('users', 'groups', 'messages'));
    }

    public function show(User $user)
    {
        $messages = Message::where(function($query) use ($user) {
            $query->where(function($q) use ($user) {
                $q->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
            })->orWhere(function($q) use ($user) {
                $q->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
            });
        })
        ->whereNull('parent_id')
        ->with(['sender', 'receiver', 'replies.sender', 'comments.sender'])
        ->latest()
        ->get();

        // Mark messages as read
        $messages->each(function($message) {
            if ($message->receiver_id === Auth::id()) {
                $message->markAsRead();
            }
        });

        return view('messages.show', compact('user', 'messages'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'receiver_id' => 'required|exists:users,id',
            'message_type' => 'required|in:text,emoji,gif,sticker',
            'is_private' => 'boolean',
            'priority' => 'required|in:normal,important,urgent'
        ]);

        $message = new Message([
            'content' => $validated['content'],
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'message_type' => $validated['message_type'],
            'is_private' => $validated['is_private'] ?? false,
            'priority' => $validated['priority'],
            'type' => 'message'
        ]);

        $message->save();

        // Notify the recipient
        $recipient = User::find($validated['receiver_id']);
        $recipient->notify(new NewMessage($message));

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => $message->load('sender'),
            'status' => 'success'
        ]);
    }

    public function reply(Request $request, Message $message)
    {
        if (!$message->canBeAccessedBy(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $reply = new Message([
            'content' => $validated['content'],
            'sender_id' => Auth::id(),
            'receiver_id' => $message->sender_id === Auth::id() ? $message->receiver_id : $message->sender_id,
            'parent_id' => $message->id,
            'type' => 'reply',
            'message_type' => 'text',
            'priority' => 'normal'
        ]);

        $reply->save();

        // Notify the recipient
        $recipient = User::find($reply->receiver_id);
        $recipient->notify(new NewMessage($reply));

        // Broadcast the reply
        broadcast(new MessageSent($reply))->toOthers();

        return response()->json([
            'message' => $reply->load('sender'),
            'parent_message' => $message
        ]);
    }

    public function comment(Request $request, Message $message)
    {
        if (!$message->canBeAccessedBy(Auth::id())) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $comment = new Message([
            'content' => $validated['content'],
            'sender_id' => Auth::id(),
            'receiver_id' => $message->sender_id === Auth::id() ? $message->receiver_id : $message->sender_id,
            'parent_id' => $message->id,
            'type' => 'comment',
            'message_type' => 'text',
            'priority' => 'normal'
        ]);

        $comment->save();

        // Notify the recipient
        $recipient = User::find($comment->receiver_id);
        $recipient->notify(new NewMessage($comment));

        // Broadcast the comment
        broadcast(new MessageSent($comment))->toOthers();

        return response()->json([
            'message' => $comment->load('sender'),
            'parent_message' => $message
        ]);
    }

    public function markAsRead(Message $message)
    {
        if ($message->receiver_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'Message marked as read'
        ]);
    }

    public function searchMessages(Request $request)
    {
        $query = $request->get('query');
        
        $messages = Message::forUser(Auth::id())
            ->search($query)
            ->with(['sender', 'receiver'])
            ->latest()
            ->get();

        return response()->json($messages);
    }
}
