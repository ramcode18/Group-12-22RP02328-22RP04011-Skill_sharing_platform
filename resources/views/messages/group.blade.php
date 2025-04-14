<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $group->name }}
            </h2>
            <button type="button" onclick="document.getElementById('membersModal').classList.remove('hidden')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Members ({{ $group->users->count() }})
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col h-[600px]">
                        <!-- Messages Container -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
                            @foreach($messages as $message)
                                <div class="flex @if($message->sender_id === auth()->id()) justify-end @endif">
                                    <div class="max-w-[70%] @if($message->sender_id === auth()->id()) bg-indigo-100 @else bg-gray-100 @endif rounded-lg p-3">
                                        <div class="text-sm">
                                            <p class="font-medium text-gray-900">
                                                {{ $message->sender_id === auth()->id() ? 'You' : $message->sender->name }}
                                            </p>
                                            <p class="text-gray-800">{{ $message->content }}</p>
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $message->created_at->format('M j, Y g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Message Input -->
                        <div class="border-t p-4">
                            <form id="message-form" class="flex space-x-4">
                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                <input type="text" name="content" id="message-input"
                                       class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="Type your message...">
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Send
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Members Modal -->
    <div id="membersModal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Group Members
                            </h3>
                            <div class="mt-4 space-y-2">
                                @foreach($group->users as $member)
                                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $member->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $member->email }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                            onclick="document.getElementById('membersModal').classList.add('hidden')"
                            class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');

        // Scroll to bottom of messages
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        scrollToBottom();

        // Handle form submission
        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const content = messageInput.value.trim();
            if (!content) return;

            try {
                const response = await fetch('{{ route("messages.store") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        content,
                        group_id: {{ $group->id }}
                    })
                });

                if (response.ok) {
                    messageInput.value = '';
                    const message = await response.json();
                    appendMessage(message);
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });

        // Append new message to the container
        function appendMessage(message) {
            const div = document.createElement('div');
            div.className = 'flex justify-end';
            div.innerHTML = `
                <div class="max-w-[70%] bg-indigo-100 rounded-lg p-3">
                    <div class="text-sm">
                        <p class="font-medium text-gray-900">You</p>
                        <p class="text-gray-800">${message.content}</p>
                        <p class="text-xs text-gray-500 mt-1">Just now</p>
                    </div>
                </div>
            `;
            messagesContainer.appendChild(div);
            scrollToBottom();
        }

        // Listen for new messages
        window.Echo.join('group.{{ $group->id }}')
            .listen('MessageSent', (e) => {
                if (e.message.sender_id !== {{ auth()->id() }}) {
                    const div = document.createElement('div');
                    div.className = 'flex';
                    div.innerHTML = `
                        <div class="max-w-[70%] bg-gray-100 rounded-lg p-3">
                            <div class="text-sm">
                                <p class="font-medium text-gray-900">${e.message.sender.name}</p>
                                <p class="text-gray-800">${e.message.content}</p>
                                <p class="text-xs text-gray-500 mt-1">Just now</p>
                            </div>
                        </div>
                    `;
                    messagesContainer.appendChild(div);
                    scrollToBottom();
                }
            });
    </script>
    @endpush
</x-app-layout>
