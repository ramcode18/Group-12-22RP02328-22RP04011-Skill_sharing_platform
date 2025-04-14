<?php

namespace App\Http\Controllers;

use App\Models\LearningSession;
use App\Models\User;
use App\Notifications\SessionReminder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $hostedSessions = LearningSession::where('host_id', $user->id)
            ->with(['host', 'participants'])
            ->latest()
            ->get();

        $joinedSessions = $user->participatingSessions()
            ->with(['host', 'participants'])
            ->latest()
            ->get();

        $upcomingSessions = LearningSession::where(function($query) use ($user) {
            $query->where('host_id', '!=', $user->id)
                  ->whereDoesntHave('participants', function($q) use ($user) {
                      $q->where('user_id', $user->id);
                  });
        })
        ->where('start_time', '>', now())
        ->where('mode', 'online')
        ->orderBy('start_time')
        ->get();

        return view('sessions.index', compact('hostedSessions', 'joinedSessions', 'upcomingSessions'));
    }

    public function create()
    {
        return view('sessions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'mode' => 'required|in:online,offline',
            'location' => 'required_if:mode,offline',
            'tools_needed' => 'nullable|array',
            'max_participants' => 'required|integer|min:1',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'required_if:is_recurring,true|in:daily,weekly,monthly',
            'recurrence_end_date' => 'required_if:is_recurring,true|date|after:start_time',
        ]);

        $session = new LearningSession($validated);
        $session->host_id = Auth::id();
        
        if ($validated['mode'] === 'online') {
            $session->save();
            $session->generateMeetingLink();
        } else {
            $session->save();
        }

        return redirect()->route('sessions.show', $session)
            ->with('success', 'Session created successfully.');
    }

    public function show(LearningSession $session)
    {
        $session->load(['host', 'participants']);
        $isParticipant = $session->participants()->where('user_id', Auth::id())->exists();
        $canJoin = $session->canJoin(Auth::user());

        return view('sessions.show', compact('session', 'isParticipant', 'canJoin'));
    }

    public function join(LearningSession $session)
    {
        if (!$session->canJoin(Auth::user())) {
            return back()->with('error', 'Unable to join this session.');
        }

        $session->participants()->attach(Auth::id(), [
            'reminder_enabled' => true,
            'calendar_synced' => false
        ]);

        // Create default reminder (15 minutes before)
        $session->reminders()->create([
            'user_id' => Auth::id(),
            'minutes_before' => 15
        ]);

        if ($session->mode === 'online') {
            return redirect()->route('sessions.show', $session)
                ->with('success', 'Joined session successfully. You can access the meeting link when the session starts.');
        }

        return redirect()->route('sessions.show', $session)
            ->with('success', 'Joined session successfully.');
    }

    public function leave(LearningSession $session)
    {
        $session->participants()->detach(Auth::id());
        $session->reminders()->where('user_id', Auth::id())->delete();

        return redirect()->route('sessions.index')
            ->with('success', 'Left session successfully.');
    }

    public function updateReminders(LearningSession $session, Request $request)
    {
        $validated = $request->validate([
            'minutes_before' => 'required|integer|min:5',
            'email_notification' => 'boolean',
            'browser_notification' => 'boolean'
        ]);

        $session->reminders()
            ->where('user_id', Auth::id())
            ->update($validated);

        return back()->with('success', 'Reminder preferences updated.');
    }

    public function syncCalendar(LearningSession $session)
    {
        // Here we would integrate with Google Calendar API
        // For now, we'll just mark it as synced
        $session->participants()
            ->where('user_id', Auth::id())
            ->update(['calendar_synced' => true]);

        return back()->with('success', 'Session added to your calendar.');
    }

    public function destroy(LearningSession $session)
    {
        if (!$session->isHost(Auth::user())) {
            return back()->with('error', 'Unauthorized action.');
        }

        // Notify participants
        foreach ($session->participants as $participant) {
            $participant->notify(new SessionReminder($session, 'cancelled'));
        }

        $session->delete();

        return redirect()->route('sessions.index')
            ->with('success', 'Session cancelled successfully.');
    }
}
