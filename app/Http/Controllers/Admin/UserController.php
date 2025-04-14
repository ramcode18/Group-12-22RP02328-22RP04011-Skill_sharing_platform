<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'learner')->get();
        return view('admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete admin users.');
        }

        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot modify admin user status.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully.");
    }
}
