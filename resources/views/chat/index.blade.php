@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sidebar with recent chats and groups -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Chats</h5>
                    <div class="list-group mb-4">
                        @foreach($recentChats as $chat)
                            <a href="{{ route('chat.show', $chat->id) }}" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                {{ $chat->name }}
                                @if($chat->unread_count > 0)
                                    <span class="badge bg-primary rounded-pill">{{ $chat->unread_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    <h5 class="card-title">Groups</h5>
                    <div class="list-group">
                        @foreach($groups as $group)
                            <a href="{{ route('chat.group', $group->id) }}" 
                               class="list-group-item list-group-item-action">
                                {{ $group->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Main chat area -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div id="chat-messages" class="chat-messages mb-4" style="height: 400px; overflow-y: auto;">
                        @if(isset($messages))
                            @foreach($messages as $message)
                                <div class="message mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : '' }}">
                                    <div class="message-content d-inline-block p-2 rounded {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 70%;">
                                        <p class="mb-1">{{ $message->content }}</p>
                                        <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted">
                                Select a chat to start messaging
                            </div>
                        @endif
                    </div>

                    @if(isset($currentChat))
                        <form id="message-form" class="message-input" action="{{ route('messages.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="receiver_id" value="{{ $currentChat->id }}">
                            <div class="input-group">
                                <input type="text" 
                                       name="content" 
                                       class="form-control" 
                                       placeholder="Type your message..." 
                                       required 
                                       autocomplete="off">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane"></i> Send
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const messageForm = document.getElementById('message-form');

    // Scroll to bottom of messages
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Handle form submission
    if (messageForm) {
        messageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    // Add the new message to the chat
                    const messageDiv = document.createElement('div');
                    messageDiv.className = 'message mb-3 text-end';
                    messageDiv.innerHTML = `
                        <div class="message-content d-inline-block p-2 rounded bg-primary text-white" style="max-width: 70%;">
                            <p class="mb-1">${result.message.content}</p>
                            <small class="text-muted">${new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</small>
                        </div>
                    `;
                    chatMessages.appendChild(messageDiv);
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    
                    // Clear the input
                    this.reset();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }
});
</script>
@endpush
@endsection
