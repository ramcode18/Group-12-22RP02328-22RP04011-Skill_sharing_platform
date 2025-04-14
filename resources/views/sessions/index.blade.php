<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Learning Sessions') }}
            </h2>
            <x-primary-button onclick="window.location='{{ route('sessions.create') }}'">
                {{ __('Host New Session') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Hosted Sessions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Sessions You Host') }}</h3>
                    @if($hostedSessions->count() > 0)
                        <div class="space-y-4">
                            @foreach($hostedSessions as $session)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-medium">{{ $session->title }}</h4>
                                            <p class="text-gray-600 mt-1">{{ $session->description }}</p>
                                            <div class="mt-2 space-y-1">
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    {{ $session->start_time->format('D, M j, Y g:i A') }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas {{ $session->mode === 'online' ? 'fa-video' : 'fa-location-dot' }} mr-2"></i>
                                                    {{ $session->mode === 'online' ? 'Online' : $session->location }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-users mr-2"></i>
                                                    {{ $session->participants->count() }}/{{ $session->max_participants }} participants
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <x-secondary-button onclick="window.location='{{ route('sessions.show', $session) }}'">
                                                {{ __('Details') }}
                                            </x-secondary-button>
                                            @if($session->mode === 'online' && $session->meeting_link)
                                                <x-primary-button onclick="window.location='{{ $session->meeting_link }}'">
                                                    {{ __('Join Meeting') }}
                                                </x-primary-button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('You are not hosting any sessions yet.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Joined Sessions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Sessions You\'ve Joined') }}</h3>
                    @if($joinedSessions->count() > 0)
                        <div class="space-y-4">
                            @foreach($joinedSessions as $session)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-medium">{{ $session->title }}</h4>
                                            <p class="text-gray-600 mt-1">{{ $session->description }}</p>
                                            <div class="mt-2 space-y-1">
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-user mr-2"></i>
                                                    Hosted by {{ $session->host->name }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    {{ $session->start_time->format('D, M j, Y g:i A') }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas {{ $session->mode === 'online' ? 'fa-video' : 'fa-location-dot' }} mr-2"></i>
                                                    {{ $session->mode === 'online' ? 'Online' : $session->location }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <form method="POST" action="{{ route('sessions.leave', $session) }}" class="inline">
                                                @csrf
                                                <x-danger-button type="submit">
                                                    {{ __('Leave') }}
                                                </x-danger-button>
                                            </form>
                                            @if($session->mode === 'online' && $session->meeting_link)
                                                <x-primary-button onclick="window.location='{{ $session->meeting_link }}'">
                                                    {{ __('Join Meeting') }}
                                                </x-primary-button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('You haven\'t joined any sessions yet.') }}</p>
                    @endif
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Upcoming Sessions') }}</h3>
                    @if($upcomingSessions->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingSessions as $session)
                                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-medium">{{ $session->title }}</h4>
                                            <p class="text-gray-600 mt-1">{{ $session->description }}</p>
                                            <div class="mt-2 space-y-1">
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-user mr-2"></i>
                                                    Hosted by {{ $session->host->name }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-clock mr-2"></i>
                                                    {{ $session->start_time->format('D, M j, Y g:i A') }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas {{ $session->mode === 'online' ? 'fa-video' : 'fa-location-dot' }} mr-2"></i>
                                                    {{ $session->mode === 'online' ? 'Online' : $session->location }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <i class="fas fa-users mr-2"></i>
                                                    {{ $session->participants->count() }}/{{ $session->max_participants }} participants
                                                </p>
                                            </div>
                                        </div>
                                        <div>
                                            @if(!$session->participants->contains(auth()->user()))
                                                <form method="POST" action="{{ route('sessions.join', $session) }}">
                                                    @csrf
                                                    <x-primary-button type="submit">
                                                        {{ __('Join Session') }}
                                                    </x-primary-button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">{{ __('No upcoming sessions available.') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
