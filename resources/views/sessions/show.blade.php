<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $session->title }}
            </h2>
            @if($session->host_id === auth()->id())
                <form method="POST" action="{{ route('sessions.destroy', $session) }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-danger-button type="submit" onclick="return confirm('Are you sure you want to cancel this session?')">
                        {{ __('Cancel Session') }}
                    </x-danger-button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Session Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-4">{{ __('Session Details') }}</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Description') }}</p>
                                    <p class="mt-1">{{ $session->description }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Host') }}</p>
                                    <p class="mt-1">{{ $session->host->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Date & Time') }}</p>
                                    <p class="mt-1">{{ $session->start_time->format('D, M j, Y g:i A') }} - {{ $session->end_time->format('g:i A') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Mode') }}</p>
                                    <p class="mt-1">
                                        @if($session->mode === 'online')
                                            <span class="inline-flex items-center">
                                                <i class="fas fa-video mr-2"></i> Online
                                                @if($session->meeting_link)
                                                    <x-primary-button onclick="window.location='{{ $session->meeting_link }}'" class="ml-3">
                                                        {{ __('Join Meeting') }}
                                                    </x-primary-button>
                                                @endif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center">
                                                <i class="fas fa-location-dot mr-2"></i> {{ $session->location }}
                                            </span>
                                        @endif
                                    </p>
                                </div>
                                @if($session->tools_needed)
                                    <div>
                                        <p class="text-sm text-gray-500">{{ __('Tools Needed') }}</p>
                                        <ul class="mt-1 list-disc list-inside">
                                            @foreach(explode("\n", $session->tools_needed) as $tool)
                                                <li>{{ $tool }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold mb-4">{{ __('Participants') }} ({{ $session->participants->count() }}/{{ $session->max_participants }})</h3>
                            @if($session->participants->count() > 0)
                                <div class="space-y-2">
                                    @foreach($session->participants as $participant)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <span>{{ $participant->name }}</span>
                                            @if($session->host_id === auth()->id() && $participant->id !== auth()->id())
                                                <form method="POST" action="{{ route('sessions.leave', ['session' => $session, 'user' => $participant]) }}" class="inline">
                                                    @csrf
                                                    <x-danger-button type="submit" class="px-3 py-1">
                                                        {{ __('Remove') }}
                                                    </x-danger-button>
                                                </form>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600">{{ __('No participants yet.') }}</p>
                            @endif

                            @if(!$session->participants->contains(auth()->user()) && $session->participants->count() < $session->max_participants)
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('sessions.join', $session) }}">
                                        @csrf
                                        <x-primary-button type="submit">
                                            {{ __('Join Session') }}
                                        </x-primary-button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($session->participants->contains(auth()->user()))
                        <!-- Reminder Settings -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">{{ __('Reminder Settings') }}</h3>
                            <form method="POST" action="{{ route('sessions.updateReminders', $session) }}" class="space-y-4">
                                @csrf
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <x-input-label for="reminder_minutes" :value="__('Remind me')" />
                                        <select name="reminder_minutes" id="reminder_minutes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <option value="5">5 minutes before</option>
                                            <option value="10">10 minutes before</option>
                                            <option value="15">15 minutes before</option>
                                            <option value="30">30 minutes before</option>
                                            <option value="60">1 hour before</option>
                                        </select>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="email_notification" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2">{{ __('Email notification') }}</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="browser_notification" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2">{{ __('Browser notification') }}</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" name="calendar_sync" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                            <span class="ml-2">{{ __('Add to my calendar') }}</span>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <x-primary-button type="submit">
                                        {{ __('Save Preferences') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
