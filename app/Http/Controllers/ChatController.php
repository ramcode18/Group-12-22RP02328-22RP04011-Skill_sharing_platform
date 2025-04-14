<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get recent private chats
        $recentChats = User::whereIn('id', function($query) use ($user) {
            $query->select('sender_id')
                ->from('messages')
                ->where('receiver_id', $user->id)
                ->whereNull('group_id')
                ->union(
                    Message::select('receiver_id')
                        ->where('sender_id', $user->id)
                        ->whereNull('group_id')
                );
        })->get();

        // Get user's groups
        $groups = Group::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return view('chat.index', compact('recentChats', 'groups'));
    }

    public function show($id)
    {
        $currentChat = User::findOrFail($id);
        $user = Auth::user();
        
        $messages = Message::where(function($query) use ($user, $id) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', $id);
        })->orWhere(function($query) use ($user, $id) {
            $query->where('sender_id', $id)
                  ->where('receiver_id', $user->id);
        })
        ->whereNull('group_id')
        ->orderBy('created_at')
        ->get();

        // Mark messages as read
        Message::where('sender_id', $id)
              ->where('receiver_id', $user->id)
              ->where('is_read_by_receiver', false)
              ->update(['is_read_by_receiver' => true]);

        $recentChats = User::whereIn('id', function($query) use ($user) {
            $query->select('sender_id')
                ->from('messages')
                ->where('receiver_id', $user->id)
                ->whereNull('group_id')
                ->union(
                    Message::select('receiver_id')
                        ->where('sender_id', $user->id)
                        ->whereNull('group_id')
                );
        })->get();

        $groups = Group::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return view('chat.index', compact('messages', 'currentChat', 'recentChats', 'groups'));
    }

    public function showGroup($id)
    {
        $currentChat = Group::findOrFail($id);
        $user = Auth::user();
        
        $messages = Message::where('group_id', $id)
                         ->orderBy('created_at')
                         ->get();

        $recentChats = User::whereIn('id', function($query) use ($user) {
            $query->select('sender_id')
                ->from('messages')
                ->where('receiver_id', $user->id)
                ->whereNull('group_id')
                ->union(
                    Message::select('receiver_id')
                        ->where('sender_id', $user->id)
                        ->whereNull('group_id')
                );
        })->get();

        $groups = Group::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return view('chat.index', compact('messages', 'currentChat', 'recentChats', 'groups'));
    }
}
