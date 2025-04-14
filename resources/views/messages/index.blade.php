<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Users List -->
                        <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Recent Chats</h3>
                            <div class="space-y-2">
                                @foreach($users as $user)
                                    <a href="{{ route('messages.show', $user) }}" 
                                       class="block p-3 rounded-lg hover:bg-gray-100 transition @if(request()->route('user')?->id === $user->id) bg-gray-200 @endif">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <div class="text-sm">
                                                    <p class="font-medium text-gray-900">{{ $user->name }}</p>
                                                    <p class="text-gray-500">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                            @if($user->sent_messages_count > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ $user->sent_messages_count }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Groups List -->
                        <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold">Groups</h3>
                                <button type="button" onclick="document.getElementById('createGroupModal').classList.remove('hidden')"
                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    New Group
                                </button>
                            </div>
                            <div class="space-y-2">
                                @foreach($groups as $group)
                                    <a href="{{ route('messages.group', $group) }}" 
                                       class="block p-3 rounded-lg hover:bg-gray-100 transition @if(request()->route('group')?->id === $group->id) bg-gray-200 @endif">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-medium text-gray-900">{{ $group->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $group->users->count() }} members</p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Message Preview -->
                        <div class="md:col-span-1 bg-gray-50 p-4 rounded-lg">
                            <div class="flex items-center justify-center h-full">
                                <p class="text-gray-500">Select a chat to start messaging</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('groups.store') }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Create New Group
                                </h3>
                                <div class="mt-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Group Name</label>
                                    <input type="text" name="name" id="name" required
                                           class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div class="mt-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea name="description" id="description" rows="3"
                                              class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Create Group
                        </button>
                        <button type="button"
                                onclick="document.getElementById('createGroupModal').classList.add('hidden')"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
