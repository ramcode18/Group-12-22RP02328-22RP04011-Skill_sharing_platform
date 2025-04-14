// Message handling
class MessageHandler {
    constructor() {
        this.messageForm = document.getElementById('message-form');
        this.messageInput = document.getElementById('message-input');
        this.messagesContainer = document.getElementById('messages-container');
        this.messageTypeBtns = document.querySelectorAll('.message-type-btn');
        this.currentMessageType = 'text';
        this.searchInput = document.getElementById('message-search');
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Message form submission
        this.messageForm?.addEventListener('submit', (e) => this.handleMessageSubmit(e));

        // Message type selection
        this.messageTypeBtns?.forEach(btn => {
            btn.addEventListener('click', () => this.handleMessageTypeChange(btn));
        });

        // Search functionality
        this.searchInput?.addEventListener('input', debounce((e) => this.handleSearch(e.target.value), 300));

        // Reply and comment handlers
        document.addEventListener('click', (e) => {
            if (e.target.matches('.reply-btn')) {
                this.handleReply(e.target.dataset.messageId);
            } else if (e.target.matches('.comment-btn')) {
                this.handleComment(e.target.dataset.messageId);
            }
        });
    }

    async handleMessageSubmit(e) {
        e.preventDefault();
        const formData = new FormData(this.messageForm);
        const content = formData.get('content').trim();
        if (!content) return;

        try {
            const response = await fetch('/messages', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    content,
                    receiver_id: formData.get('receiver_id'),
                    message_type: this.currentMessageType,
                    is_private: formData.get('is_private') === 'on',
                    priority: formData.get('priority')
                })
            });

            if (response.ok) {
                const data = await response.json();
                this.appendMessage(data.message);
                this.messageForm.reset();
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error sending message:', error);
        }
    }

    async handleReply(messageId) {
        const content = prompt('Enter your reply:');
        if (!content) return;

        try {
            const response = await fetch(`/messages/${messageId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content })
            });

            if (response.ok) {
                const data = await response.json();
                this.appendReply(data.message, messageId);
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error sending reply:', error);
        }
    }

    async handleComment(messageId) {
        const content = prompt('Enter your comment:');
        if (!content) return;

        try {
            const response = await fetch(`/messages/${messageId}/comment`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ content })
            });

            if (response.ok) {
                const data = await response.json();
                this.appendComment(data.message, messageId);
                this.scrollToBottom();
            }
        } catch (error) {
            console.error('Error sending comment:', error);
        }
    }

    handleMessageTypeChange(btn) {
        this.messageTypeBtns.forEach(b => b.classList.remove('bg-indigo-100', 'text-indigo-700'));
        btn.classList.add('bg-indigo-100', 'text-indigo-700');
        this.currentMessageType = btn.dataset.type;
        
        // Update input placeholder
        const placeholders = {
            text: 'Type your message...',
            emoji: 'Type an emoji...',
            gif: 'Search for a GIF...',
            sticker: 'Choose a sticker...'
        };
        this.messageInput.placeholder = placeholders[this.currentMessageType] || placeholders.text;
    }

    async handleSearch(query) {
        if (!query) {
            this.resetSearch();
            return;
        }

        try {
            const response = await fetch(`/messages/search?query=${encodeURIComponent(query)}`);
            if (response.ok) {
                const messages = await response.json();
                this.displaySearchResults(messages);
            }
        } catch (error) {
            console.error('Error searching messages:', error);
        }
    }

    appendMessage(message) {
        const messageElement = this.createMessageElement(message);
        this.messagesContainer.appendChild(messageElement);
    }

    appendReply(reply, parentId) {
        const parentMessage = document.getElementById(`message-${parentId}`);
        const repliesContainer = parentMessage.querySelector('.replies-container') || 
            this.createRepliesContainer(parentMessage);
        
        const replyElement = this.createReplyElement(reply);
        repliesContainer.appendChild(replyElement);
    }

    appendComment(comment, parentId) {
        const parentMessage = document.getElementById(`message-${parentId}`);
        const commentsContainer = parentMessage.querySelector('.comments-container') || 
            this.createCommentsContainer(parentMessage);
        
        const commentElement = this.createCommentElement(comment);
        commentsContainer.appendChild(commentElement);
    }

    createMessageElement(message) {
        const div = document.createElement('div');
        div.id = `message-${message.id}`;
        div.className = 'message-container mb-4';
        
        const isOwn = message.sender_id === parseInt(document.body.dataset.userId);
        const priorityClass = this.getPriorityClass(message.priority);
        
        div.innerHTML = `
            <div class="flex ${isOwn ? 'justify-end' : ''}">
                <div class="max-w-[70%] ${isOwn ? 'bg-indigo-100' : 'bg-gray-100'} rounded-lg p-3 ${priorityClass}">
                    <div class="text-sm">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-900">${isOwn ? 'You' : message.sender.name}</p>
                                ${message.is_private ? this.getPrivateIcon() : ''}
                            </div>
                            <div class="flex space-x-2">
                                <button class="reply-btn text-xs text-gray-500 hover:text-indigo-600" data-message-id="${message.id}">Reply</button>
                                <button class="comment-btn text-xs text-gray-500 hover:text-indigo-600" data-message-id="${message.id}">Comment</button>
                            </div>
                        </div>
                        <div class="message-content">${this.formatMessageContent(message.content)}</div>
                        <div class="mt-1 flex items-center justify-between">
                            <p class="text-xs text-gray-500">${this.formatDate(message.created_at)}</p>
                            ${this.getReadStatus(message)}
                        </div>
                    </div>
                </div>
            </div>
            <div class="replies-container ml-8 mt-2 space-y-2"></div>
            <div class="comments-container ml-8 mt-2 space-y-2"></div>
        `;
        
        return div;
    }

    createRepliesContainer(parentElement) {
        const container = document.createElement('div');
        container.className = 'replies-container ml-8 mt-2 space-y-2';
        parentElement.appendChild(container);
        return container;
    }

    createCommentsContainer(parentElement) {
        const container = document.createElement('div');
        container.className = 'comments-container ml-8 mt-2 space-y-2';
        parentElement.appendChild(container);
        return container;
    }

    getPriorityClass(priority) {
        const classes = {
            urgent: 'border-l-4 border-red-500',
            important: 'border-l-4 border-yellow-500',
            normal: ''
        };
        return classes[priority] || '';
    }

    getPrivateIcon() {
        return `
            <svg class="w-4 h-4 text-gray-500 ml-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M5 9V7a5 5 0 0110 0v2h1a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2v-6a2 2 0 012-2h1zm4-2.5a.5.5 0 01.5-.5h1a.5.5 0 01.5.5v2h-2V6.5z"/>
            </svg>
        `;
    }

    getReadStatus(message) {
        if (message.is_read_by_receiver) {
            return `<p class="text-xs text-gray-500">Read ${this.formatDate(message.read_at)}</p>`;
        }
        return '<p class="text-xs text-gray-500">Sent</p>';
    }

    formatMessageContent(content) {
        // Convert URLs to links
        content = content.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" class="text-blue-600 hover:underline">$1</a>');
        
        // Convert markdown-style formatting
        content = content.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        content = content.replace(/_(.*?)_/g, '<em>$1</em>');
        
        return content;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString();
    }

    scrollToBottom() {
        this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }

    resetSearch() {
        // Implement search reset logic
    }

    displaySearchResults(messages) {
        // Implement search results display logic
    }
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize message handler when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new MessageHandler();
});
