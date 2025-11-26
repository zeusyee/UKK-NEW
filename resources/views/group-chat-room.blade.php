@extends('layouts.member')

@section('title', 'Group Chat - ' . $project->project_name)

@section('content')
<style>
/* ==========================================
   MODERN CHAT APPLICATION DESIGN (FULL)
   ========================================== */

/* Background chat seperti WhatsApp */
.chat-messages {
    background: #e5ddd5;
    scrollbar-width: thin;
    scrollbar-color: #bbb transparent;
    padding: 12px;
}

.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 6px;
}

/* BUBBLE WRAPPER */
.message-bubble {
    display: flex;
    margin-bottom: 14px;
    animation: fadeIn 0.25s ease-out;
}

/* Me (kanan) */
.message-bubble.own {
    justify-content: flex-end;
}

/* Other (kiri) */
.message-bubble.other {
    justify-content: flex-start;
}

/* Bubble container */
.message-container {
    max-width: 75%;
    display: flex;
    flex-direction: column;
}

/* Nama pengirim */
.message-author-name {
    font-size: 0.7rem;
    font-weight: 600;
    margin-bottom: 3px;
    padding-left: 4px;
    color: #4b5563;
}

/* Bubble BASE STYLE */
.message-content {
    padding: 10px 14px;
    line-height: 1.45;
    border-radius: 12px;
    position: relative;
    font-size: 0.95rem;
    word-break: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.12);
}

/* Bubble kiri */
.message-bubble.other .message-content {
    background: white;
    border: 1px solid #e5e7eb;
    border-top-left-radius: 4px;
}

/* Bubble kanan (warna biru) */
.message-bubble.own .message-content {
    background: #93c5fd; /* Biru lebih terang */
    color: #111;
    border-top-right-radius: 4px;
}

/* Bubble tail kiri */
.message-bubble.other .message-content::after {
    content: "";
    position: absolute;
    left: -6px;
    top: 8px;
    width: 0;
    height: 0;
    border-top: 6px solid white;
    border-left: 6px solid transparent;
}

/* Bubble tail kanan */
.message-bubble.own .message-content::after {
    content: "";
    position: absolute;
    right: -6px;
    top: 8px;
    width: 0;
    height: 0;
    border-top: 6px solid #93c5fd;
    border-right: 6px solid transparent;
}

/* Timestamp */
.message-time {
    font-size: 0.68rem;
    opacity: 0.55;
    margin-top: 2px;
    padding: 0 5px;
}

.message-bubble.own .message-time {
    text-align: right;
}

/* Empty state */
.empty-state {
    text-align: center;
    margin-top: 50px;
    opacity: 0.5;
}

/* INPUT BAR - FIXED LAYOUT */
.chat-input-area {
    background: #f8f9fa;
    border-top: 1px solid #d1d5db;
    padding: 12px 16px;
    display: flex;
    align-items: flex-end;
    gap: 12px;
    width: 100%;
}

/* Form container mengambil lebar penuh */
.chat-input-area form {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    width: 100%;
}

/* Textarea modern - MEMANJANG KE KANAN */
.message-input {
    flex: 1;
    min-height: 48px;
    max-height: 120px;
    padding: 12px 16px;
    border-radius: 24px;
    border: 1px solid #cbd5e1;
    background: white;
    resize: none;
    font-size: 0.95rem;
    line-height: 1.4;
    transition: 0.2s;
    font-family: inherit;
}

.message-input:focus {
    border-color: #3b82f6; /* Biru */
    box-shadow: 0 0 0 2px rgba(59,130,246,0.25);
    outline: none;
}

/* Send button warna biru */
.send-button {
    background: #3b82f6; /* Biru */
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    border: none;
    cursor: pointer;
    transition: .2s;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    flex-shrink: 0;
}

.send-button:hover {
    background: #2563eb; /* Biru lebih gelap */
    transform: scale(1.06);
}

.send-button:active {
    transform: scale(0.98);
}

/* Fade animation */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}

/* MOBILE RESPONSIVE */
@media (max-width: 600px) {
    .message-container {
        max-width: 87%;
    }
    .message-content {
        font-size: 0.9rem;
        padding: 9px 12px;
    }
    
    .chat-input-area {
        padding: 12px;
        gap: 8px;
    }
    
    .message-input {
        padding: 10px 14px;
        min-height: 44px;
    }
    
    .send-button {
        width: 44px;
        height: 44px;
    }
}

/* Toast notification */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: white;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    border-left: 4px solid #3b82f6; /* Biru */
}

.toast-error {
    border-left-color: #ef4444;
}

/* Main container layout */
.chat-main-container {
    display: flex;
    flex-direction: column;
    height: 70vh;
    background: white;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
</style>

    <div class="mb-6">
        <a href="{{ route('member.project.details', $project->project_id) }}" class="text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-left mr-2"></i>Back to Project
        </a>
    </div>

    <!-- Page Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg shadow-lg p-4 sm:p-6 mb-4 sm:mb-6">
        <div class="flex items-center justify-between">
            <div class="text-white flex-1">
                <h1 class="text-xl sm:text-3xl font-bold mb-1 sm:mb-2">
                    <i class="fas fa-comments mr-2 sm:mr-3"></i>{{ $project->project_name }}
                </h1>
                <p class="text-blue-100 text-xs sm:text-sm">
                    <i class="fas fa-users mr-1 sm:mr-2"></i>
                    Team Communication Room
                </p>
            </div>
            <div class="text-white text-right hidden sm:block">
                <div class="text-4xl font-bold" id="memberCount">0</div>
                <p class="text-blue-100 text-sm">Members Online</p>
            </div>
        </div>
    </div>

    <!-- Chat Container -->
    <div class="max-w-7xl mx-auto">
        <div class="chat-main-container">
            <!-- Chat Messages Container -->
            <div class="chat-messages flex-1 overflow-y-auto" id="chatMessages">
                <div class="empty-state py-12" id="noMessages">
                    <i class="fas fa-comments text-5xl sm:text-6xl mb-4" style="color: rgba(0,0,0,0.2);"></i>
                    <p class="text-sm sm:text-base">No messages yet. Start the conversation!</p>
                </div>
            </div>

            <!-- Chat Input Container - FIXED LAYOUT -->
            <div class="chat-input-area">
                <form id="chatForm" class="flex items-end w-full gap-3">
                    @csrf
                    
                    <!-- Textarea yang memanjang ke kanan -->
                    <div class="flex-1">
                        <textarea 
                            id="messageInput" 
                            name="comment_text"
                            class="message-input w-full"
                            rows="1"
                            placeholder="Type a message..."
                            required
                        ></textarea>
                    </div>
                    
                    <!-- Send Button -->
                    <button 
                        type="submit"
                        class="send-button"
                        title="Send message"
                    >
                        <i class="fas fa-paper-plane text-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
/* v2.0 - Clean bubble chat with external labels */
.chat-messages {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f9fafb;
    background: #f9fafb;
}

.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background-color: rgba(0,0,0,0.2);
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background-color: rgba(0,0,0,0.3);
}

.message-bubble {
    margin-bottom: 0.625rem;
    animation: slideIn 0.2s ease-out;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    padding: 0 0.5rem;
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

.message-bubble.own {
    flex-direction: row-reverse;
    justify-content: flex-start;
}

.message-bubble.other {
    flex-direction: row;
    justify-content: flex-start;
}

.message-content-wrapper {
    max-width: 70%;
    display: flex;
    flex-direction: column;
}

.message-author-name {
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b7280;
    margin-bottom: 0.25rem;
    padding-left: 0.25rem;
}

.message-content {
    padding: 0.75rem 1rem;
    border-radius: 1.25rem;
    position: relative;
    word-wrap: break-word;
    word-break: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.message-bubble.other .message-content {
    background: #e5e7eb;
    color: #1f2937;
}

.message-bubble.own .message-content {
    background: #60a5fa; /* Biru */
    color: white;
}

.message-text {
    white-space: pre-wrap;
    line-height: 1.5;
    font-size: 0.938rem;
}

.message-time {
    font-size: 0.688rem;
    color: #9ca3af;
    margin-top: 0.25rem;
    padding-left: 0.25rem;
}

.message-bubble.own .message-time {
    text-align: right;
    padding-right: 0.25rem;
    padding-left: 0;
}

.message-actions {
    position: absolute;
    top: 0.5rem;
    right: -2.5rem;
    opacity: 0;
    transition: opacity 0.2s;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.message-bubble.own:hover .message-actions {
    opacity: 1;
}

.btn-delete {
    background: transparent;
    color: #dc2626;
    border: none;
    padding: 0.375rem 0.625rem;
    font-size: 0.75rem;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.btn-delete:hover {
    background: #fee2e2;
}

.toast {
    position: fixed;
    bottom: 2rem;
    right: 2rem;
    padding: 0.875rem 1.25rem;
    border-radius: 0.75rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 9999;
    animation: slideInUp 0.3s ease-out;
    font-size: 0.875rem;
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
    background: #3b82f6; /* Biru */
    color: white;
}

.toast-error {
    background: #dc2626;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: rgba(0,0,0,0.4);
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .chat-messages {
        height: calc(100vh - 260px) !important;
        max-height: calc(100vh - 260px) !important;
        padding: 0.5rem;
    }
    
    .message-bubble {
        padding: 0 0.25rem;
        margin-bottom: 0.5rem;
    }
    
    .message-content-wrapper {
        max-width: 80%;
    }
    
    .message-content {
        padding: 0.625rem 0.875rem;
        border-radius: 1.125rem;
    }
    
    .message-text {
        font-size: 0.875rem;
    }
    
    .message-author-name {
        font-size: 0.688rem;
    }
    
    .message-time {
        font-size: 0.625rem;
    }
    
    .btn-delete {
        font-size: 0.688rem;
        padding: 0.25rem 0.5rem;
    }
    
    .toast {
        bottom: 1rem;
        right: 1rem;
        left: 1rem;
        padding: 0.75rem 1rem;
        font-size: 0.813rem;
    }
    
    .message-actions {
        position: static;
        opacity: 1;
        margin-top: 0.25rem;
        display: inline-block;
    }
}

@media (max-width: 480px) {
    .message-content-wrapper {
        max-width: 85%;
    }
}

/* Input area */
.chat-input-area {
    background: white;
    border-top: 1px solid #e5e7eb;
    padding: 0;
}

.message-input {
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 1.25rem;
    resize: none;
    transition: all 0.2s;
}

.message-input:focus {
    outline: none;
    background: white;
    border-color: #60a5fa; /* Biru */
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
}

.send-button {
    background: #3b82f6; /* Biru */
    min-width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
}

.send-button:hover {
    background: #2563eb; /* Biru lebih gelap */
    transform: scale(1.05);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.4);
}

@media (max-width: 640px) {
    .send-button {
        border-radius: 1rem;
        min-width: auto;
        padding: 0 1.25rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
console.log('=== GROUP CHAT ROOM SCRIPT LOADED ===');

document.addEventListener('DOMContentLoaded', function() {
    const projectId = {{ $project->project_id }};
    const currentUserId = {{ Auth::id() }};
    
    console.log('Project ID:', projectId);
    console.log('Current User ID:', currentUserId);
    
    // Auto-refresh every 3 seconds (reduced from 5)
    let autoRefreshInterval = null;
    let lastMessageId = 0;
    
    // Load messages on page load
    loadMessages();
    startAutoRefresh();
    
    // Handle message input Enter key
    const messageInput = document.getElementById('messageInput');
    const chatForm = document.getElementById('chatForm');
    
    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
    });
    
    messageInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });
    
    // Submit message form
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
                messageInput.style.height = 'auto';
                // Immediately load messages to show the new one
                loadMessages(true);
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
    
    // Load messages function
    function loadMessages(scrollToBottom = false) {
        fetch(`/group-chat/${projectId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayMessages(data.data, scrollToBottom);
                }
            })
            .catch(error => {
                console.error('Failed to load messages:', error);
            });
    }
    
    // Display messages
    function displayMessages(messages, forceScrollToBottom = false) {
        const container = document.getElementById('chatMessages');
        const noMessages = document.getElementById('noMessages');
        
        if (!messages || messages.length === 0) {
            if (noMessages) noMessages.style.display = 'block';
            return;
        }
        
        if (noMessages) noMessages.style.display = 'none';
        
        // Check if there are new messages
        const latestMessageId = messages[messages.length - 1]?.comment_id || 0;
        const hasNewMessages = lastMessageId > 0 && latestMessageId > lastMessageId;
        lastMessageId = latestMessageId;
        
        // Store current scroll position
        const isAtBottom = (container.scrollHeight - container.scrollTop) <= (container.clientHeight + 50);
        
        container.innerHTML = messages.map(message => {
            if (!message || !message.user) return '';
            
            const isOwn = message.user_id === currentUserId;
            const userName = message.user.full_name || message.user.username || 'Unknown';
            const timestamp = new Date(message.created_at).toLocaleString('id-ID', {
                day: '2-digit',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit'
            });
            
            return `
                <div class="message-bubble ${isOwn ? 'own' : 'other'}">
                    <div class="message-content-wrapper">
                        ${!isOwn ? `
                            <div class="message-author-name">${userName}</div>
                        ` : ''}
                        <div class="message-content">
                            <div class="message-text">${escapeHtml(message.comment_text)}</div>
                            ${isOwn ? `
                                <div class="message-actions">
                                    <button onclick="deleteMessage(${message.comment_id})" class="btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            ` : ''}
                        </div>
                        <div class="message-time">${timestamp}</div>
                    </div>
                </div>
            `;
        }).filter(html => html).join('');
        
        // Scroll to bottom if was at bottom, has new messages, or forced
        if (isAtBottom || hasNewMessages || forceScrollToBottom) {
            setTimeout(() => {
                container.scrollTop = container.scrollHeight;
            }, 100);
        }
    }
    
    // Delete message function
    window.deleteMessage = function(messageId) {
        if (!confirm('Are you sure you want to delete this message?')) {
            return;
        }
        
        fetch(`/group-chat/${messageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadMessages(false);
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
    
    // Start auto-refresh
    function startAutoRefresh() {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
        }
        autoRefreshInterval = setInterval(() => loadMessages(false), 2000); // Refresh every 2 seconds for real-time feel
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