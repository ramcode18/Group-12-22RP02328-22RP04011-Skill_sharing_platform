<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\SkillReaction;
use Illuminate\Http\Request;

class SkillReactionController extends Controller
{
    public function react(Request $request, Skill $skill)
    {
        $type = $request->input('type');
        if (!in_array($type, ['like', 'dislike'])) {
            return response()->json(['error' => 'Invalid reaction type'], 400);
        }

        $reaction = SkillReaction::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'skill_id' => $skill->id,
            ],
            ['type' => $type]
        );

        return response()->json([
            'likes_count' => $skill->fresh()->likes_count,
            'dislikes_count' => $skill->fresh()->dislikes_count,
            'user_reaction' => $type,
        ]);
    }

    public function removeReaction(Skill $skill)
    {
        SkillReaction::where('user_id', auth()->id())
            ->where('skill_id', $skill->id)
            ->delete();

        return response()->json([
            'likes_count' => $skill->fresh()->likes_count,
            'dislikes_count' => $skill->fresh()->dislikes_count,
            'user_reaction' => null,
        ]);
    }
}
