<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $group = Group::create($validated);
        $group->addMember(auth()->user());

        return redirect()->route('messages.group', $group)
            ->with('success', 'Group created successfully.');
    }

    public function addMember(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $group->addMember($validated['user_id']);

        return back()->with('success', 'Member added successfully.');
    }

    public function removeMember(Request $request, Group $group)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $group->removeMember($validated['user_id']);

        return back()->with('success', 'Member removed successfully.');
    }
}
