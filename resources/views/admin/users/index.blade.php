@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                Users Management
            </h2>
            <button onclick="openCreateModal()" class="bg-primary text-white font-medium py-2.5 px-4 rounded-lg hover:bg-primary/90 transition-colors duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add User
            </button>
        </div>
    </div>

    <div class="p-6">
        <!-- Success/Error Messages -->
        <div id="messageContainer"></div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="usersTableBody">
                    @foreach($users as $user)
                        <tr id="user-{{ $user->user_id }}">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->full_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->username }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $user->current_task_status === 'idle' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800' }}">
                                    {{ ucfirst($user->current_task_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($user->user_id !== auth()->id())
                                    <button onclick="openEditModal({{ $user->user_id }})" class="text-blue-600 hover:text-blue-900 mr-4 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </button>
                                    <button onclick="deleteUser({{ $user->user_id }})" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-gray-400 text-sm">Current user</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New User
            </h3>
        </div>
        <form id="createForm" class="p-6">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="create_username" class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" id="create_username" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                    <div id="create_username_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <label for="create_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" id="create_email" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                    <div id="create_email_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <label for="create_password" class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                    <input type="password" name="password" id="create_password" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                    <div id="create_password_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <label for="create_full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" id="create_full_name" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                    <div id="create_full_name_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <label for="create_role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="role" id="create_role" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                    </select>
                    <div id="create_role_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeCreateModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Create User
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-xl shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit User
            </h3>
        </div>
        <form id="editForm" class="p-6">
            @csrf
            @method('PUT')
            <input type="hidden" name="user_id" id="edit_user_id">
            <div class="space-y-4">
                <div>
                    <label for="edit_username" class="block text-sm font-medium text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" id="edit_username" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                    <div id="edit_username_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <label for="edit_email" class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" id="edit_email" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                    <div id="edit_email_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <label for="edit_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" id="edit_password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                    <p class="mt-1.5 text-xs text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Leave blank to keep current password
                    </p>
                    <div id="edit_password_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <label for="edit_full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="full_name" id="edit_full_name" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                    <div id="edit_full_name_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>

                <div>
                    <label for="edit_role" class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                    <select name="role" id="edit_role" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors duration-200">
                        <option value="member">Member</option>
                        <option value="admin">Admin</option>
                    </select>
                    <div id="edit_role_error" class="mt-1.5 text-sm text-red-600 hidden"></div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-5 py-2.5 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors duration-200 flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// CSRF Token for AJAX requests
let csrfToken = null;
const csrfMetaTag = document.querySelector('meta[name="csrf-token"]');
if (csrfMetaTag) {
    csrfToken = csrfMetaTag.getAttribute('content');
} else {
    // Fallback: try to get from CSRF token input if meta tag doesn't exist
    const csrfInput = document.querySelector('input[name="_token"]');
    if (csrfInput) {
        csrfToken = csrfInput.value;
    }
}

// Show message function
function showMessage(message, type = 'success') {
    const messageContainer = document.getElementById('messageContainer');
    const bgColor = type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200';
    const textColor = type === 'success' ? 'text-green-800' : 'text-red-800';
    const iconColor = type === 'success' ? 'text-green-600' : 'text-red-600';
    const iconPath = type === 'success' ? 
        'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' :
        'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';

    messageContainer.innerHTML = `
        <div class="mb-4 p-4 ${bgColor} border rounded-lg flex items-center">
            <svg class="w-5 h-5 ${iconColor} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}"></path>
            </svg>
            <span class="${textColor}">${message}</span>
        </div>
    `;
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        messageContainer.innerHTML = '';
    }, 5000);
}

// Clear errors function
function clearErrors(type) {
    const errorElements = document.querySelectorAll(`[id$="${type}_username_error"], [id$="${type}_email_error"], [id$="${type}_password_error"], [id$="${type}_full_name_error"], [id$="${type}_role_error"]`);
    errorElements.forEach(element => {
        element.classList.add('hidden');
        element.textContent = '';
    });
}

// Display errors function
function displayErrors(errors, type) {
    clearErrors(type);
    
    for (const field in errors) {
        const errorElement = document.getElementById(`${type}_${field}_error`);
        if (errorElement) {
            errorElement.textContent = errors[field][0];
            errorElement.classList.remove('hidden');
        }
    }
}

// Modal Management Functions - OUTSIDE DOMContentLoaded to be globally accessible
function openCreateModal() {
    console.log('Opening create modal');
    document.getElementById('createModal').classList.remove('hidden');
    document.getElementById('createForm').reset();
    clearErrors('create');
}

function closeCreateModal() {
    console.log('Closing create modal');
    document.getElementById('createModal').classList.add('hidden');
}

async function openEditModal(userId) {
    console.log('Opening edit modal for user:', userId);
    try {
        const response = await fetch(`/admin/users/${userId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            const user = data.user;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('edit_user_id').value = user.user_id;
            document.getElementById('edit_username').value = user.username;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_full_name').value = user.full_name;
            document.getElementById('edit_role').value = user.role;
            document.getElementById('edit_password').value = '';
            clearErrors('edit');
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('Failed to load user data', 'error');
    }
}

function closeEditModal() {
    console.log('Closing edit modal');
    document.getElementById('editModal').classList.add('hidden');
}

async function deleteUser(userId) {
    console.log('Deleting user:', userId);
    if (!confirm('Are you sure you want to delete this user?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            // Remove user row from table
            document.getElementById(`user-${userId}`).remove();
            showMessage(data.message, 'success');
        } else {
            showMessage(data.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('Failed to delete user', 'error');
    }
}

// Event Listeners - hanya untuk form submissions
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing event listeners');
    
    // Create form submission
    const createForm = document.getElementById('createForm');
    if (createForm) {
        createForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Create form submitted');
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Loading state
            submitButton.innerHTML = 'Creating...';
            submitButton.disabled = true;
            
            try {
                const response = await fetch('{{ route("admin.users.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    closeCreateModal();
                    showMessage(data.message, 'success');
                    // Reload page to show new user
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (data.errors) {
                        displayErrors(data.errors, 'create');
                    } else {
                        showMessage(data.message, 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('An error occurred while creating the user', 'error');
            } finally {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    }
    
    // Edit form submission
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            console.log('Edit form submitted');
            
            const userId = document.getElementById('edit_user_id').value;
            const formData = new FormData(this);
            
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Loading state
            submitButton.innerHTML = 'Updating...';
            submitButton.disabled = true;
            
            try {
                const response = await fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    closeEditModal();
                    showMessage(data.message, 'success');
                    // Reload page to show updated user
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (data.errors) {
                        displayErrors(data.errors, 'edit');
                    } else {
                        showMessage(data.message, 'error');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('An error occurred while updating the user', 'error');
            } finally {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            }
        });
    }
    
    // Close modals when clicking outside
    document.getElementById('createModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });
    
    document.getElementById('editModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCreateModal();
            closeEditModal();
        }
    });
    
    console.log('Event listeners initialized');
});
</script>
@endpush