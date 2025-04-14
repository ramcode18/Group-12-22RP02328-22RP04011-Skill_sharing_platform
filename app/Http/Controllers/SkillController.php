<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SkillController extends Controller
{
    public function shared()
    {
        $sharedSkills = Skill::where('status', 'shared')
            ->with(['user', 'comments.user'])
            ->latest()
            ->get();

        return view('skill.shared', [
            'sharedSkills' => $sharedSkills,
            'isAdmin' => Auth::user()->isAdmin()
        ]);
    }

    public function requested()
    {
        $requestedSkills = Skill::where('status', 'requested')
            ->with(['user', 'comments.user'])
            ->latest()
            ->get();

        return view('skill.requested', [
            'requestedSkills' => $requestedSkills,
            'isAdmin' => Auth::user()->isAdmin()
        ]);
    }

    public function explore()
    {
        $skills = Skill::with(['user', 'comments.user'])
            ->latest()
            ->get();

        return view('skill.explore', [
            'skills' => $skills,
            'isAdmin' => Auth::user()->isAdmin()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:shared,requested',
            'category' => 'required|string|max:255',
        ]);

        $skill = Auth::user()->skills()->create($validated);

        return redirect()->back()->with('success', 'Skill created successfully!');
    }

    public function update(Request $request, Skill $skill)
    {
        if (!Auth::user()->isAdmin() && Auth::id() !== $skill->user_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:shared,requested',
            'category' => 'required|string|max:255',
        ]);

        $skill->update($validated);

        return redirect()->back()->with('success', 'Skill updated successfully!');
    }

    public function destroy(Skill $skill)
    {
        if (!Auth::user()->isAdmin() && Auth::id() !== $skill->user_id) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $skill->delete();
        return redirect()->back()->with('success', 'Skill deleted successfully!');
    }
}
