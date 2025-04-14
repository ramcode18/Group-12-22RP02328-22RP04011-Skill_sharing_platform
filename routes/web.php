<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\SkillReactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SessionController;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            $userSkills = Skill::where('user_id', $user->id)
                ->withCount('comments')
                ->with(['user', 'comments.user'])
                ->get();

            $allSkills = Skill::with(['user', 'comments.user'])
                ->withCount('comments')
                ->get();

            return view('admin.dashboard', compact('userSkills', 'allSkills'));
        }

        $totalSkills = Skill::where('user_id', $user->id)->count();
        $sharedSkills = Skill::where('user_id', $user->id)
            ->where('category', 'shared')
            ->count();
        $requestedSkills = Skill::where('user_id', $user->id)
            ->where('category', 'requested')
            ->count();
        $recentSkills = Skill::where('user_id', $user->id)
            ->withCount(['comments', 'reactions as likes_count' => function ($query) {
                $query->where('type', 'like');
            }, 'reactions as dislikes_count' => function ($query) {
                $query->where('type', 'dislike');
            }])
            ->latest()
            ->take(5)
            ->get();

        return view('learner.dashboard', compact(
            'totalSkills',
            'sharedSkills',
            'requestedSkills',
            'recentSkills'
        ));
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Skills routes
    Route::get('/skills/explore', [SkillController::class, 'explore'])->name('skills.explore');
    Route::get('/skills/shared', [SkillController::class, 'shared'])->name('skills.shared');
    Route::get('/skills/requested', [SkillController::class, 'requested'])->name('skills.requested');
    Route::resource('skills', SkillController::class);
    
    // Comment routes
    Route::post('/skills/{skill}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Skill reactions
    Route::post('/skills/{skill}/react', [SkillReactionController::class, 'react'])->name('skills.react');
    Route::delete('/skills/{skill}/react', [SkillReactionController::class, 'removeReaction'])->name('skills.remove-reaction');

    // Messaging routes
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/user/{user}', [MessageController::class, 'show'])->name('show');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::post('/{message}/reply', [MessageController::class, 'reply'])->name('reply');
        Route::post('/{message}/comment', [MessageController::class, 'comment'])->name('comment');
        Route::post('/{message}/read', [MessageController::class, 'markAsRead'])->name('read');
        Route::get('/search', [MessageController::class, 'searchMessages'])->name('search');
    });

    // Group routes
    Route::prefix('groups')->name('groups.')->group(function () {
        Route::post('/', [GroupController::class, 'store'])->name('store');
        Route::get('/{group}', [GroupController::class, 'show'])->name('show');
        Route::post('/{group}/messages', [GroupController::class, 'sendMessage'])->name('messages.store');
        Route::post('/{group}/members', [GroupController::class, 'addMember'])->name('members.add');
        Route::delete('/{group}/members/{user}', [GroupController::class, 'removeMember'])->name('members.remove');
    });

    // Chat routes
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'index'])->name('index');
        Route::get('/user/{id}', [ChatController::class, 'show'])->name('show');
        Route::get('/group/{id}', [ChatController::class, 'showGroup'])->name('group');
    });

    // Session routes
    Route::middleware(['auth', 'verified'])->prefix('sessions')->name('sessions.')->group(function () {
        Route::get('/', [SessionController::class, 'index'])->name('index');
        Route::get('/create', [SessionController::class, 'create'])->name('create');
        Route::post('/', [SessionController::class, 'store'])->name('store');
        Route::get('/{session}', [SessionController::class, 'show'])->name('show');
        Route::post('/{session}/join', [SessionController::class, 'join'])->name('join');
        Route::post('/{session}/leave', [SessionController::class, 'leave'])->name('leave');
        Route::post('/{session}/reminders', [SessionController::class, 'updateReminders'])->name('updateReminders');
        Route::post('/{session}/calendar', [SessionController::class, 'syncCalendar'])->name('syncCalendar');
        Route::delete('/{session}', [SessionController::class, 'destroy'])->name('destroy');
    });

    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/logout', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
});

require __DIR__.'/auth.php';
