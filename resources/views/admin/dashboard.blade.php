<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- My Skills Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">My Skills</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($userSkills as $skill)
                            <div class="bg-white p-6 rounded-lg shadow">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xl font-semibold">{{ $skill->name }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $skill->status === 'shared' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($skill->status) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm mt-1">{{ $skill->category }}</p>
                                <p class="mt-2">{{ $skill->description }}</p>
                                
                                <div class="mt-4 flex space-x-2">
                                    <form action="{{ route('skills.destroy', $skill) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button onclick="return confirm('Are you sure?')">
                                            {{ __('Delete') }}
                                        </x-danger-button>
                                    </form>
                                </div>

                                <!-- Comments -->
                                <div class="mt-4 border-t pt-4">
                                    <h5 class="font-medium text-gray-900">Comments ({{ $skill->comments->count() }})</h5>
                                    @foreach($skill->comments as $comment)
                                        <div class="mt-2 bg-gray-50 p-2 rounded">
                                            <p class="text-sm">{{ $comment->content }}</p>
                                            <p class="text-xs text-gray-500">By: {{ $comment->user->name }}</p>
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="mt-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- All Skills Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">All Skills</h3>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($allSkills as $skill)
                            <div class="bg-white p-6 rounded-lg shadow">
                                <div class="flex items-center justify-between">
                                    <h4 class="text-xl font-semibold">{{ $skill->name }}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $skill->status === 'shared' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($skill->status) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm mt-1">{{ $skill->category }}</p>
                                <p class="mt-2">{{ $skill->description }}</p>
                                <p class="text-sm text-gray-500 mt-2">By: {{ $skill->user->name }}</p>

                                <div class="mt-4 flex space-x-2">
                                    <form action="{{ route('skills.destroy', $skill) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button onclick="return confirm('Are you sure?')">
                                            {{ __('Delete') }}
                                        </x-danger-button>
                                    </form>
                                </div>

                                <!-- Comments -->
                                <div class="mt-4 border-t pt-4">
                                    <h5 class="font-medium text-gray-900">Comments ({{ $skill->comments->count() }})</h5>
                                    @foreach($skill->comments as $comment)
                                        <div class="mt-2 bg-gray-50 p-2 rounded">
                                            <p class="text-sm">{{ $comment->content }}</p>
                                            <p class="text-xs text-gray-500">By: {{ $comment->user->name }}</p>
                                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="mt-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-xs text-red-600 hover:text-red-800">Delete</button>
                                            </form>
                                        </div>
                                    @endforeach

                                    <form action="{{ route('comments.store', $skill) }}" method="POST" class="mt-3">
                                        @csrf
                                        <div class="flex gap-2">
                                            <input type="text" name="content" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Add a comment..." required>
                                            <x-primary-button>Comment</x-primary-button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
