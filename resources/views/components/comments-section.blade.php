@props(['cardId' => null, 'subtaskId' => null, 'type' => 'card'])

<div class="comments-section bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-comments text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Comments</h1>
                <p class="text-sm text-gray-500 mt-1">Join the conversation</p>
            </div>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold text-gray-900 comment-count">0</div>
            <div class="text-sm text-gray-500">comments</div>
        </div>
    </div>

    <!-- Add Comment Form -->
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-plus text-white text-sm"></i>
            </div>
            <h2 class="text-xl font-semibold text-gray-800">Add a comment</h2>
        </div>
        
        <form class="comment-form" id="commentForm">
            @csrf
            <input type="hidden" id="comment_type" name="comment_type" value="{{ $type }}">
            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl border-2 border-gray-200 p-6 mb-6 transition-all duration-300 hover:border-blue-300 focus-within:border-blue-400 focus-within:shadow-lg">
                <label for="comment_text" class="block text-sm font-semibold text-gray-700 mb-3">
                    <i class="fas fa-edit mr-2 text-blue-500"></i>Write your comment here...
                </label>
                <textarea 
                    id="comment_text" 
                    name="comment_text" 
                    rows="4" 
                    class="w-full px-4 py-3 bg-transparent border-0 focus:ring-0 focus:outline-none resize-none text-gray-700 placeholder-gray-400 text-lg font-light leading-relaxed"
                    placeholder="Share your thoughts, feedback, or updates..."
                    required
                ></textarea>
                <div class="flex items-center justify-between pt-4 border-t border-gray-200 mt-4">
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle"></i>
                        <span>Be respectful and constructive</span>
                    </div>
                    <div class="text-sm text-gray-500" id="charCount">0 characters</div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button 
                    type="submit" 
                    class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold px-8 py-4 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center gap-3 group"
                >
                    <i class="fas fa-paper-plane group-hover:scale-110 transition-transform duration-300"></i>
                    <span class="text-lg">Post Comment</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Comments List -->
    <div class="comments-list">
        <div class="text-center py-16" id="noComments">
            <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i class="fas fa-comment-slash text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-500 mb-3">No comments yet</h3>
            <p class="text-gray-400 max-w-md mx-auto leading-relaxed">
                Be the first to share your thoughts and start the conversation!
            </p>
        </div>
        
        <div id="commentsList" class="space-y-4"></div>
    </div>
</div>

@push('styles')
<style>
.comments-section {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
}

/* Comment Item Styles - More Structured Layout */
.comment-item {
    background: white;
    border: 1px solid #f1f5f9;
    border-radius: 1rem;
    padding: 1.25rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
    position: relative;
}

.comment-item:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    border-color: #e2e8f0;
}

/* Comment Header - Structured Layout */
.comment-header {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
}

.comment-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
}

.comment-user-info {
    flex: 1;
    min-width: 0;
}

.comment-user-main {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
    flex-wrap: wrap;
}

.comment-author-initials {
    font-weight: 700;
    color: #1e293b;
    font-size: 0.875rem;
}

.comment-author-name {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.you-badge {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 0.125rem 0.5rem;
    border-radius: 0.5rem;
    font-size: 0.625rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.125rem;
}

.comment-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.comment-time {
    display: flex;
    align-items: center;
    gap: 0.375rem;
    font-size: 0.75rem;
    color: #64748b;
}

.time-icon {
    opacity: 0.6;
    font-size: 0.6875rem;
}

.comment-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-left: auto;
    flex-shrink: 0;
}

/* Action Buttons - Icon Only */
.btn-action {
    padding: 0.5rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 2rem;
    height: 2rem;
}

.btn-edit {
    background: rgba(59, 130, 246, 0.1);
    color: #3b82f6;
}

.btn-edit:hover {
    background: #3b82f6;
    color: white;
    transform: scale(1.05);
}

.btn-delete {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
}

.btn-delete:hover {
    background: #ef4444;
    color: white;
    transform: scale(1.05);
}

/* Comment Content */
.comment-content {
    margin-left: 3.25rem; /* Align with avatar space */
}

.comment-text {
    color: #374151;
    white-space: pre-wrap;
    word-wrap: break-word;
    line-height: 1.5;
    font-size: 0.875rem;
    background: #f8fafc;
    padding: 0.75rem 1rem;
    border-radius: 0.75rem;
    border-left: 3px solid #e2e8f0;
    margin-bottom: 0.5rem;
}

/* Edit Form Styles */
.edit-comment-form {
    margin-top: 0.75rem;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 0.75rem;
    border: 1px solid #e2e8f0;
}

.edit-comment-form textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 0.5rem;
    padding: 0.75rem;
    margin-bottom: 0.75rem;
    font-size: 0.875rem;
    resize: vertical;
    font-family: inherit;
    line-height: 1.5;
    background: white;
}

.edit-comment-form textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.edit-form-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}

.btn-save {
    background: #10b981;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    transition: all 0.2s ease;
}

.btn-save:hover {
    background: #059669;
    transform: scale(1.02);
}

.btn-cancel {
    background: #6b7280;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    transition: all 0.2s ease;
}

.btn-cancel:hover {
    background: #4b5563;
    transform: scale(1.02);
}

/* Empty State */
#noComments {
    transition: all 0.5s ease;
}

/* Animation */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.comment-item {
    animation: slideInUp 0.4s ease-out;
}

/* Mobile Responsive */
@media (max-width: 768px) {
    .comments-section {
        padding: 1.5rem;
        border-radius: 1rem;
    }
    
    .comment-header {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .comment-user-main {
        flex: 1;
        min-width: 0;
    }
    
    .comment-meta {
        order: 3;
        width: 100%;
        margin-top: 0.5rem;
        padding-left: 3rem;
    }
    
    .comment-actions {
        margin-left: 0;
    }
    
    .comment-content {
        margin-left: 0;
        margin-top: 0.75rem;
    }
    
    .comment-avatar {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 0.625rem;
        font-size: 0.75rem;
    }
}

/* Compact layout for better structure */
.comment-user-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.25rem;
}

.comment-meta-row {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

/* Icon styles */
.meta-icon {
    width: 12px;
    height: 12px;
    opacity: 0.6;
}

.action-icon {
    width: 14px;
    height: 14px;
}

/* Ensure proper text truncation */
.comment-author-name {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Hover effects */
.comment-item {
    transition: all 0.2s ease;
}

.comment-item:hover {
    transform: translateY(-1px);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cardId = {{ $cardId ?? 'null' }};
    const subtaskId = {{ $subtaskId ?? 'null' }};
    const type = '{{ $type }}';
    const currentUserId = {{ auth()->id() }};
    
    // Load comments on page load
    loadComments();
    
    // Character count and auto-resize
    const commentTextarea = document.getElementById('comment_text');
    const charCount = document.getElementById('charCount');
    
    if (commentTextarea && charCount) {
        commentTextarea.addEventListener('input', function() {
            // Auto-resize
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 200) + 'px';
            
            // Character count
            const length = this.value.length;
            charCount.textContent = `${length} characters`;
            
            if (length > 500) {
                charCount.className = 'text-sm text-orange-500 font-medium';
            } else if (length > 0) {
                charCount.className = 'text-sm text-green-500 font-medium';
            } else {
                charCount.className = 'text-sm text-gray-500';
            }
        });
        
        // Initial character count
        charCount.textContent = '0 characters';
    }
    
    // Submit comment form
    const commentForm = document.getElementById('commentForm');
    
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const commentText = document.getElementById('comment_text').value.trim();
            const commentType = document.getElementById('comment_type').value;
            
            if (!commentText) {
                showToast('Please enter a comment', 'error');
                return;
            }
            
            const submitBtn = commentForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Posting...';
            submitBtn.disabled = true;
            
            const url = type === 'card' 
                ? `/comments/card/${cardId}`
                : `/comments/subtask/${subtaskId}`;
            
            // Create FormData
            const formData = new FormData();
            formData.append('comment_text', commentText);
            formData.append('comment_type', commentType);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch(url, {
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
                if (data.success) {
                    document.getElementById('comment_text').value = '';
                    commentTextarea.style.height = 'auto';
                    charCount.textContent = '0 characters';
                    charCount.className = 'text-sm text-gray-500';
                    loadComments();
                    showToast('Comment posted successfully!', 'success');
                } else {
                    showToast(data.message || 'Failed to post comment', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Failed to post comment', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Load comments function
    function loadComments() {
        const url = type === 'card' 
            ? `/comments/card/${cardId}`
            : `/comments/subtask/${subtaskId}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayComments(data.data);
                }
            })
            .catch(error => {
                console.error('Failed to load comments:', error);
            });
    }
    
    // Display comments
    function displayComments(comments) {
        const commentsList = document.getElementById('commentsList');
        const noComments = document.getElementById('noComments');
        const commentCount = document.querySelector('.comment-count');
        
        if (commentCount) {
            commentCount.textContent = comments.length;
        }
        
        if (comments.length === 0) {
            if (noComments) noComments.style.display = 'block';
            if (commentsList) commentsList.innerHTML = '';
            return;
        }
        
        if (noComments) noComments.style.display = 'none';
        if (commentsList) {
            commentsList.innerHTML = '';
            comments.forEach(comment => {
                const commentHtml = createCommentHtml(comment);
                commentsList.insertAdjacentHTML('beforeend', commentHtml);
            });
        }
    }
    
    // Create comment HTML - Structured like the image
    function createCommentHtml(comment) {
        const isOwner = currentUserId === comment.user_id;
        const timeAgo = formatTimeAgo(comment.created_at);
        const userName = comment.user.full_name || comment.user.username;
        const initials = userName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
        const isCurrentUser = comment.user_id === currentUserId;
        
        return `
            <div class="comment-item" data-comment-id="${comment.comment_id}">
                <div class="comment-header">
                    <div class="comment-avatar">
                        ${initials}
                    </div>
                    
                    <div class="comment-user-info">
                        <div class="comment-user-row">
                            <span class="comment-author-initials">${initials}</span>
                            <span class="comment-author-name">
                                ${escapeHtml(userName)}
                                ${isCurrentUser ? '<span class="you-badge">✔️ You</span>' : ''}
                            </span>
                        </div>
                        
                        <div class="comment-meta-row">
                            <span class="comment-time">
                                <i class="fas fa-search time-icon"></i>
                                ${timeAgo}
                            </span>
                        </div>
                    </div>
                    
                    ${isOwner ? `
                    <div class="comment-actions">
                        <button class="btn-action btn-edit edit-comment-btn" data-comment-id="${comment.comment_id}" title="Edit comment">
                            <i class="fas fa-edit action-icon"></i>
                        </button>
                        <button class="btn-action btn-delete delete-comment-btn" data-comment-id="${comment.comment_id}" title="Delete comment">
                            <i class="fas fa-trash action-icon"></i>
                        </button>
                    </div>
                    ` : ''}
                </div>
                
                <div class="comment-content">
                    <div class="comment-text">${escapeHtml(comment.comment_text)}</div>
                </div>
            </div>
        `;
    }
    
    // Event delegation for edit comment
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-comment-btn')) {
            const btn = e.target.closest('.edit-comment-btn');
            const commentId = btn.dataset.commentId;
            const commentItem = btn.closest('.comment-item');
            const commentTextEl = commentItem.querySelector('.comment-text');
            const commentText = commentTextEl.textContent;
            const commentActions = commentItem.querySelector('.comment-actions');
            
            const editForm = `
                <form class="edit-comment-form" data-comment-id="${commentId}">
                    <textarea class="w-full" rows="3" required placeholder="Edit your comment...">${commentText}</textarea>
                    <div class="edit-form-actions">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button type="button" class="btn-cancel cancel-edit-btn">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            `;
            
            commentTextEl.style.display = 'none';
            if (commentActions) commentActions.style.display = 'none';
            commentItem.insertAdjacentHTML('beforeend', editForm);
            
            // Focus the textarea
            const textarea = commentItem.querySelector('textarea');
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
        }
        
        // Cancel edit
        if (e.target.closest('.cancel-edit-btn')) {
            const commentItem = e.target.closest('.comment-item');
            const commentTextEl = commentItem.querySelector('.comment-text');
            const commentActions = commentItem.querySelector('.comment-actions');
            commentTextEl.style.display = 'block';
            if (commentActions) commentActions.style.display = 'flex';
            const editForm = commentItem.querySelector('.edit-comment-form');
            if (editForm) editForm.remove();
        }
        
        // Delete comment
        if (e.target.closest('.delete-comment-btn')) {
            if (!confirm('Are you sure you want to delete this comment?')) {
                return;
            }
            
            const btn = e.target.closest('.delete-comment-btn');
            const commentId = btn.dataset.commentId;
            const commentItem = btn.closest('.comment-item');
            
            // Create FormData
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'DELETE');
            
            fetch(`/comments/${commentId}`, {
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
                if (data.success) {
                    loadComments();
                    showToast('Comment deleted successfully', 'success');
                } else {
                    showToast(data.message || 'Failed to delete comment', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Failed to delete comment', 'error');
            });
        }
    });
    
    // Submit edit form
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('edit-comment-form')) {
            e.preventDefault();
            const form = e.target;
            const commentId = form.dataset.commentId;
            const newText = form.querySelector('textarea').value.trim();
            
            if (!newText) {
                showToast('Comment cannot be empty', 'error');
                return;
            }
            
            // Create FormData
            const formData = new FormData();
            formData.append('comment_text', newText);
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PUT');
            
            fetch(`/comments/${commentId}`, {
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
                if (data.success) {
                    loadComments();
                    showToast('Comment updated successfully!', 'success');
                } else {
                    showToast(data.message || 'Failed to update comment', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Failed to update comment', 'error');
            });
        }
    });
    
    // Helper functions
    function formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 60) return 'Just now';
        if (seconds < 3600) {
            const mins = Math.floor(seconds / 60);
            return `${mins} minute${mins > 1 ? 's' : ''} ago`;
        }
        if (seconds < 86400) {
            const hours = Math.floor(seconds / 3600);
            return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        }
        if (seconds < 604800) {
            const days = Math.floor(seconds / 86400);
            return `${days} day${days > 1 ? 's' : ''} ago`;
        }
        
        return date.toLocaleDateString('en-US', { 
            month: 'short', 
            day: 'numeric' 
        });
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function showToast(message, type) {
        // Remove existing toasts
        document.querySelectorAll('.custom-toast').forEach(toast => toast.remove());
        
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `custom-toast fixed top-6 right-6 px-4 py-3 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white font-semibold`;
        toast.innerHTML = `
            <div class="flex items-center gap-2">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>
@endpush