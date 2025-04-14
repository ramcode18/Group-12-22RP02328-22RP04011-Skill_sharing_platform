<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Skills -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-gray-500 text-sm mb-1">Total Skills</div>
                        <div class="text-3xl font-bold text-gray-800">{{ $totalSkills }}</div>
                    </div>
                </div>

                <!-- Shared Skills -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-gray-500 text-sm mb-1">Shared Skills</div>
                        <div class="text-3xl font-bold text-gray-800">{{ $sharedSkills }}</div>
                    </div>
                </div>

                <!-- Requested Skills -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-gray-500 text-sm mb-1">Requested Skills</div>
                        <div class="text-3xl font-bold text-gray-800">{{ $requestedSkills }}</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                    
                    @if($recentSkills->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentSkills as $skill)
                                <div class="border-b border-gray-200 pb-4 last:border-b-0 last:pb-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-md font-medium text-gray-800">{{ $skill->name }}</h4>
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($skill->description, 100) }}</p>
                                            <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500">
                                                <span>
                                                    <i class="fas fa-thumbs-up"></i> {{ $skill->likes_count }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-thumbs-down"></i> {{ $skill->dislikes_count }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-comment"></i> {{ $skill->comments_count }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $skill->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No recent activity to show.</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('skills.create') }}" class="block px-4 py-2 bg-indigo-600 text-white text-center rounded-md hover:bg-indigo-700 transition-colors">
                                Share New Skill
                            </a>
                            <a href="{{ route('skills.explore') }}" class="block px-4 py-2 bg-gray-100 text-gray-700 text-center rounded-md hover:bg-gray-200 transition-colors">
                                Explore Skills
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Your Profile</h3>
                        <div class="space-y-2">
                            <p class="text-gray-600">
                                <span class="font-medium">Name:</span> {{ auth()->user()->name }}
                            </p>
                            <p class="text-gray-600">
                                <span class="font-medium">Email:</span> {{ auth()->user()->email }}
                            </p>
                            <p class="text-gray-600">
                                <span class="font-medium">Member since:</span> {{ auth()->user()->created_at->format('M d, Y') }}
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('profile.edit') }}" class="text-indigo-600 hover:text-indigo-800">
                                    Edit Profile <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
