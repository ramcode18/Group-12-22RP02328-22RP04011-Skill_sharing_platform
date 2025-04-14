<?php

namespace App\Http\Controllers;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userSkills = Skill::where('user_id', $user->id)->with('comments')->latest()->get();
        
        if ($user->isAdmin()) {
            $allSkills = Skill::with(['user', 'comments'])->latest()->get();
            return view('admin.dashboard', compact('allSkills', 'userSkills'));
        }

        return view('learner.dashboard', compact('userSkills'));
    }
}
