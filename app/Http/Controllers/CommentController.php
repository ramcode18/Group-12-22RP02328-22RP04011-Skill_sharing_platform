<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $comment = $skill->comments()->create([
            'content' => $validated['content'],
            'user_id' => Auth::id()
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    public function destroy(Comment $comment)
    {
        if (Auth::user()->isAdmin() || Auth::id() === $comment->user_id) {
            $comment->delete();
            return redirect()->back()->with('success', 'Comment deleted successfully!');
        }

        return redirect()->back()->with('error', 'Unauthorized action.');
    }
}
