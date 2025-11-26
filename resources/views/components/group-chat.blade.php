@props(['projectId'])

<div class="group-chat-container bg-white rounded-lg shadow-md" data-project-id="{{ $projectId }}">
    <!-- Chat Header -->
    <div class="chat-header bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-comments text-2xl mr-3"></i>
                <div>
                    <h3 class="text-lg font-bold">Quick Chat Preview</h3>
                    <p class="text-xs text-blue-100">Latest messages from the team</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('group-chat.room', $projectId) }}" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs rounded-full font-semibold transition-colors">
                    <i class="fas fa-arrow-right mr-1"></i>Open Full Chat
                </a>
                <span class="online-count px-3 py-1 bg-green-500 text-white text-xs rounded-full font-semibold">
                    <i class="fas fa-message text-xs mr-1"></i>
                    <span id="messageCount">0</span> messages
                </span>
                <button onclick="refreshChat()" class="p-2 hover:bg-blue-800 rounded-lg transition-colors" title="Refresh">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Chat Messages Container -->
    <div class="chat-messages-container bg-gray-50 p-4 overflow-y-auto" id="chatMessages" style="height: 500px; max-height: 500px;">
        <!-- Messages will be loaded here -->
        <div class="text-center py-8 text-gray-400" id="noMessages">
            <i class="fas fa-comment-slash text-4xl mb-3"></i>
            <p class="text-sm">No messages yet. Start the conversation!</p>
        </div>
    </div>

    <!-- Comment Input Form -->
    <div class="chat-input-container bg-white border-t border-gray-200 p-4 rounded-b-lg">
        <form id="chatForm" class="flex items-end space-x-3">
            @csrf
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Your Message</label>
                    <span class="text-xs text-gray-500 font-medium">
                        <i class="fas fa-user-circle mr-1"></i>{{ Auth::user()->full_name ?? Auth::user()->username }}
                    </span>
                </div>
                <textarea 
                    id="messageInput" 
                    name="comment_text"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none" 
                    rows="2" 
                    placeholder="Type your message here... (Press Enter to send, Shift+Enter for new line)"
                    required
                ></textarea>
            </div>
            <button 
                type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded-lg transition-colors flex items-center justify-center whitespace-nowrap"
            >
                <i class="fas fa-paper-plane mr-2"></i>Send
            </button>
        </form>
    </div>
</div>

@push('styles')
<style>
.group-chat-container {
    margin: 1.5rem 0;
}

.chat-messages-container {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

.chat-messages-container::-webkit-scrollbar {
    width: 8px;
}

.chat-messages-container::-webkit-scrollbar-track {
    background: #f7fafc;
}

.chat-messages-container::-webkit-scrollbar-thumb {
    background-color: #cbd5e0;
    border-radius: 4px;
}

.chat-messages-container::-webkit-scrollbar-thumb:hover {
    background-color: #a0aec0;
}

.message-bubble {
    max-width: 70%;
    margin-bottom: 1.5rem;
    animation: slideIn 0.3s ease-out;
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-own {
    margin-left: auto;
    flex-direction: row-reverse;
}

.message-own .message-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.message-other .message-content {
    background: white;
    color: #2d3748;
    border: 1px solid #e5e7eb;
}

.message-content {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    word-wrap: break-word;
    flex: 1;
}

.message-header {
    display: flex;
    align-items: center;
    margin-bottom: 0.25rem;
    font-size: 0.75rem;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.message-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.25rem;
    font-size: 0.75rem;
    opacity: 0.7;
}

.role-badge {
    display: inline-block;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-size: 0.625rem;
    font-weight: 600;
    text-transform: uppercase;
}

.role-admin { background: #fef3c7; color: #92400e; }
.role-leader { background: #dbeafe; color: #1e40af; }
.role-member { background: #d1fae5; color: #065f46; }
.role-all { background: #e5e7eb; color: #374151; }

.message-actions {
    display: none;
    margin-top: 0.5rem;
}

.message-bubble:hover .message-actions {
    display: flex;
    gap: 0.5rem;
}

.message-actions button {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.btn-delete {
    background: #fee2e2;
    color: #991b1b;
    border: none;
}

.btn-delete:hover {
    background: #fecaca;
}

.toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    padding: 1rem 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    z-index: 9999;
    animation: slideInUp 0.3s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.toast-success {
    background: #10b981;
    color: white;
}

.toast-error {
    background: #ef4444;
    color: white;
}
</style>
@endpush

@push('scripts')
<script>
console.log('=== GROUP CHAT SCRIPT LOADED ===');

document.addEventListener('DOMContentLoaded', function() {
    const projectId = {{ $projectId }};
    const currentUserId = {{ Auth::id() }};
    
    console.log('Project ID:', projectId);
    console.log('Current User ID:', currentUserId);
    
    // Auto-refresh every 10 seconds
    let autoRefreshInterval = null;
    
    // Load messages on page load
    loadMessages();
    startAutoRefresh();
    
    // Submit message form
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    
    if (chatForm) {
        // Handle Enter key
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                chatForm.dispatchEvent(new Event('submit'));
            }
        });
        
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageText = messageInput.value.trim();
            
            console.log('Sending message:', messageText);
            
            if (!messageText) {
                showToast('Please enter a message', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('comment_text', messageText);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch(`/group-chat/${projectId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                console.log('Message sent:', data);
                
                if (data.success) {
                    messageInput.value = '';
                    loadMessages();
                    showToast('Message sent!', 'success');
                } else {
                    showToast(data.message || 'Failed to send message', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Failed to send message', 'error');
            });
        });
    }
    
    // Load messages function
    function loadMessages() {
        fetch(`/group-chat/${projectId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessages(data.data);
                }
            })
            .catch(error => {
                console.error('Failed to load messages:', error);
            });
    }
    
    // Display messages
    function displayMessages(messages) {
        const container = document.getElementById('chatMessages');
        const noMessages = document.getElementById('noMessages');
        const messageCount = document.getElementById('messageCount');
        
        if (messages.length === 0) {
            noMessages.style.display = 'block';
            messageCount.textContent = '0';
            return;
        }
        
        noMessages.style.display = 'none';
        messageCount.textContent = messages.length;
        
        // Store current scroll position
        const isAtBottom = container.scrollHeight - container.scrollTop === container.clientHeight;
        
        container.innerHTML = messages.map(message => {
            const isOwn = message.user_id === currentUserId;
            const userName = message.user.full_name || message.user.username;
            const initials = userName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
            const timestamp = new Date(message.created_at).toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            const roleEmoji = {
                'all': '📢',
                'admin': '👑',
                'leader': '⭐',
                'member': '👥'
            };
            
            const roleLabel = {
                'all': 'Semua',
                'admin': 'Admin',
                'leader': 'Leader',
                'member': 'Member'
            };
            
            return `
                <div class="message-bubble ${isOwn ? 'message-own' : 'message-other'}" data-message-id="${message.comment_id}">
                    ${!isOwn ? `<div class="message-avatar">${initials}</div>` : ''}
                    
                    <div>
                        <div class="message-content">
                            ${!isOwn ? `
                                <div class="message-header">
                                    <span class="font-semibold">${userName}</span>
                                </div>
                            ` : ''}
                            
                            <div class="message-text" style="white-space: pre-wrap;">${escapeHtml(message.comment_text)}</div>
                            
                            <div class="message-meta" style="color: ${isOwn ? 'rgba(255,255,255,0.7)' : 'rgba(0,0,0,0.5)'};">
                                <i class="fas fa-clock text-xs"></i>
                                <span>${timestamp}</span>
                            </div>
                            
                            ${isOwn ? `
                                <div class="message-actions">
                                    <button onclick="deleteMessage(${message.comment_id})" class="btn-delete">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                    
                    ${isOwn ? `<div class="message-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">${initials}</div>` : ''}
                </div>
            `;
        }).join('');
        
        // Scroll to bottom if was at bottom, or if new message from current user
        if (isAtBottom || messages[messages.length - 1]?.user_id === currentUserId) {
            container.scrollTop = container.scrollHeight;
        }
    }
    
    // Delete message function
    window.deleteMessage = function(messageId) {
        if (!confirm('Are you sure you want to delete this message?')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch(`/group-chat/${messageId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMessages();
                showToast('Message deleted', 'success');
            } else {
                showToast(data.message || 'Failed to delete message', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to delete message', 'error');
        });
    };
    
    // Refresh chat function
    window.refreshChat = function() {
        loadMessages();
        showToast('Chat refreshed', 'success');
    };
    
    // Start auto-refresh
    function startAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        autoRefreshInterval = setInterval(loadMessages, 10000); // Refresh every 10 seconds
    }
    
    // Stop auto-refresh when page is hidden
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            if (autoRefreshInterval) {
                clearInterval(autoRefreshInterval);
            }
        } else {
            startAutoRefresh();
            loadMessages();
        }
    });
    
    // Show toast notification
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
    
    // Escape HTML for XSS protection
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endpush
