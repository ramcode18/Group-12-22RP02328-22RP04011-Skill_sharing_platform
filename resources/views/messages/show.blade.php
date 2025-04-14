<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat with') }} {{ $user->name }}
        </h2>
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
                            <form id="message-form" class="space-y-4">
                                <input type="hidden" name="receiver_id" value="{{ $user->id }}">
                                
                                <!-- Message Type Selector -->
                                <div class="flex space-x-4 mb-2">
                                    <button type="button" data-type="text" class="message-type-btn px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-700">Text</button>
                                    <button type="button" data-type="emoji" class="message-type-btn px-3 py-1 rounded-full text-sm">Emoji</button>
                                    <button type="button" data-type="gif" class="message-type-btn px-3 py-1 rounded-full text-sm">GIF</button>
                                    <button type="button" data-type="sticker" class="message-type-btn px-3 py-1 rounded-full text-sm">Sticker</button>
                                </div>

                                <!-- Message Options -->
                                <div class="flex items-center space-x-4 mb-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="is_private" class="form-checkbox text-indigo-600">
                                        <span class="ml-2 text-sm text-gray-600">Private</span>
                                    </label>
                                    <select name="priority" class="form-select text-sm border-gray-300 rounded-md focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="normal">Normal</option>
                                        <option value="important">Important</option>
                                        <option value="urgent">Urgent</option>
                                    </select>
                                </div>

                                <div class="flex space-x-4">
                                    <input type="text" name="content" id="message-input"
                                           class="flex-1 rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Type your message...">
                                    <button type="submit"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Send
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const messagesContainer = document.getElementById('messages-container');
        const messageForm = document.getElementById('message-form');
        const messageInput = document.getElementById('message-input');
        const messageTypeBtns = document.querySelectorAll('.message-type-btn');
        let currentMessageType = 'text';

        // Message type selection
        messageTypeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                messageTypeBtns.forEach(b => b.classList.remove('bg-indigo-100', 'text-indigo-700'));
                btn.classList.add('bg-indigo-100', 'text-indigo-700');
                currentMessageType = btn.dataset.type;
                
                // Update input placeholder based on type
                switch(currentMessageType) {
                    case 'emoji':
                        messageInput.placeholder = 'Type an emoji...';
                        break;
                    case 'gif':
                        messageInput.placeholder = 'Search for a GIF...';
                        break;
                    case 'sticker':
                        messageInput.placeholder = 'Choose a sticker...';
                        break;
                    default:
                        messageInput.placeholder = 'Type your message...';
                }
            });
        });

        // Scroll to bottom of messages
        function scrollToBottom() {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        scrollToBottom();

        // Handle form submission
        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(messageForm);
            const content = formData.get('content').trim();
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
                        receiver_id: {{ $user->id }},
                        message_type: currentMessageType,
                        is_private: formData.get('is_private') === 'on',
                        priority: formData.get('priority')
                    })
                });

                if (response.ok) {
                    messageInput.value = '';
                    const data = await response.json();
                    appendMessage(data.message, data.sender);
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        });

        // Append new message to the container
        function appendMessage(message, sender) {
            const div = document.createElement('div');
            div.className = 'flex justify-end';
            
            let priorityClass = '';
            if (message.priority === 'urgent') {
                priorityClass = 'border-l-4 border-red-500';
            } else if (message.priority === 'important') {
                priorityClass = 'border-l-4 border-yellow-500';
            }

            let privacyIcon = message.is_private ? 
                '<svg class="w-4 h-4 text-gray-500 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5 9V7a5 5 0 0110 0v2h1a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6a2 2 0 012-2h1zm4-2.5a.5.5 0 01.5-.5h1a.5.5 0 01.5.5v2h-2V6.5z"/></svg>' : '';

            div.innerHTML = `
                <div class="max-w-[70%] bg-indigo-100 rounded-lg p-3 ${priorityClass}">
                    <div class="text-sm">
                        <div class="flex items-center">
                            <p class="font-medium text-gray-900">You</p>
                            ${privacyIcon}
                        </div>
                        <p class="text-gray-800">${message.content}</p>
                        <p class="text-xs text-gray-500 mt-1">Just now</p>
                    </div>
                </div>
            `;
            messagesContainer.appendChild(div);
            scrollToBottom();
        }

        // Listen for new messages
        window.Echo.private('chat.{{ auth()->id() }}')
            .listen('MessageSent', (e) => {
                if (e.message.sender_id === {{ $user->id }}) {
                    const div = document.createElement('div');
                    div.className = 'flex';
                    
                    let priorityClass = '';
                    if (e.message.priority === 'urgent') {
                        priorityClass = 'border-l-4 border-red-500';
                    } else if (e.message.priority === 'important') {
                        priorityClass = 'border-l-4 border-yellow-500';
                    }

                    let privacyIcon = e.message.is_private ? 
                        '<svg class="w-4 h-4 text-gray-500 ml-1" fill="currentColor" viewBox="0 0 20 20"><path d="M5 9V7a5 5 0 0110 0v2h1a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6a2 2 0 012-2h1zm4-2.5a.5.5 0 01.5-.5h1a.5.5 0 01.5.5v2h-2V6.5z"/></svg>' : '';

                    div.innerHTML = `
                        <div class="max-w-[70%] bg-gray-100 rounded-lg p-3 ${priorityClass}">
                            <div class="text-sm">
                                <div class="flex items-center">
                                    <p class="font-medium text-gray-900">${e.message.sender.name}</p>
                                    ${privacyIcon}
                                </div>
                                <p class="text-gray-800">${e.message.content}</p>
                                <p class="text-xs text-gray-500 mt-1">Just now</p>
                            </div>
                        </div>
                    `;
                    messagesContainer.appendChild(div);
                    scrollToBottom();

                    // Mark message as read
                    fetch(`/messages/${e.message.id}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                }
            });
    </script>
    @endpush
</x-app-layout>
