@extends('layouts.admin')

@section('title', 'Projects')

@section('content')

        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">Projects</h2>
                <a href="{{ route('admin.projects.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Create New Project
                </a>
            </div>

            @php
                $readyToCompleteProjects = $projects->filter(function($project) {
                    $project->load('boards.cards');
                    return $project->isReadyToComplete();
                });
            @endphp

            @if($readyToCompleteProjects->count() > 0)
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-lg shadow-md">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <h3 class="text-lg font-semibold text-green-800 mb-2">
                                <i class="fas fa-trophy mr-2"></i>Projects Ready to Complete!
                            </h3>
                            <p class="text-sm text-green-700 mb-3">
                                The following {{ $readyToCompleteProjects->count() }} project(s) have all cards completed and are ready to be marked as finished:
                            </p>
                            <div class="space-y-2">
                                @foreach($readyToCompleteProjects as $project)
                                    <div class="bg-white p-3 rounded-lg border border-green-200 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <i class="fas fa-project-diagram text-green-600"></i>
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $project->project_name }}</p>
                                                <p class="text-xs text-gray-500">All tasks completed</p>
                                            </div>
                                        </div>
                                        <form action="{{ route('admin.projects.complete', $project) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="return confirm('Mark this project as completed? All team members will be set to idle status.')"
                                                    class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors shadow-sm">
                                                <i class="fas fa-check-double mr-2"></i>Complete Project
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created By
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Deadline
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($projects as $project)
                        <tr class="project-row {{ $project->status === 'completed' ? 'bg-gray-50' : '' }}" data-project-id="{{ $project->project_id }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $project->project_name }}
                                    @if($project->status === 'completed')
                                        <i class="fas fa-check-circle text-green-500 ml-2" title="Completed"></i>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ Str::limit($project->description, 50) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($project->status === 'completed')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Completed
                                    </span>
                                    @if($project->completed_at)
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ $project->completed_at->format('M d, Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-play-circle mr-1"></i> Active
                                    </span>
                                    @php
                                        $project->load('boards.cards');
                                        if ($project->isReadyToComplete()) {
                                            echo '<div class="text-xs text-green-600 mt-1 font-semibold"><i class="fas fa-trophy mr-1"></i>Ready to complete!</div>';
                                        }
                                    @endphp
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $project->creator->full_name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($project->status === 'completed')
                                    <div class="flex flex-col">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 w-fit">
                                            <i class="fas fa-check-circle mr-1"></i>Completed
                                        </span>
                                        @if($project->completed_at)
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $project->completed_at->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </div>
                                @elseif($project->deadline)
                                    <div>{{ date('M d, Y', strtotime($project->deadline)) }}</div>
                                    @php
                                        $daysUntil = (int) floor(now()->diffInDays($project->deadline, false));
                                    @endphp
                                    <div class="mt-1">
                                        @if($daysUntil < 0)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-exclamation-circle mr-1"></i>{{ abs($daysUntil) }} hari terlambat
                                            </span>
                                        @elseif($daysUntil == 0)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                                <i class="fas fa-clock mr-1"></i>Hari ini!
                                            </span>
                                        @elseif($daysUntil <= 7)
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-hourglass-half mr-1"></i>{{ $daysUntil }} hari tersisa
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>{{ $daysUntil }} hari tersisa
                                            </span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">No deadline</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.projects.show', $project) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                
                                @if($project->status === 'active')
                                    <button type="button" 
                                            class="edit-project-btn text-indigo-600 hover:text-indigo-900 mr-3"
                                            data-project-id="{{ $project->project_id }}"
                                            data-project-name="{{ $project->project_name }}"
                                            data-description="{{ $project->description }}"
                                            data-deadline="{{ $project->deadline ? date('Y-m-d', strtotime($project->deadline)) : '' }}">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('Are you sure you want to delete this project?')">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 mr-3" title="Cannot edit completed project">
                                        <i class="fas fa-lock mr-1"></i>Locked
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Project Modal -->
    <div id="editProjectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center pb-3 border-b">
                    <h3 class="text-lg font-bold">Edit Project</h3>
                    <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-4">
                    <form id="editProjectForm">
                        <input type="hidden" id="editProjectId" name="project_id">
                        
                        <div class="mb-4">
                            <label for="edit_project_name" class="block text-gray-700 text-sm font-bold mb-2">
                                Project Name *
                            </label>
                            <input type="text" 
                                   name="project_name" 
                                   id="edit_project_name" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                   required>
                        </div>

                        <div class="mb-4">
                            <label for="edit_description" class="block text-gray-700 text-sm font-bold mb-2">
                                Description
                            </label>
                            <textarea name="description" 
                                      id="edit_description" 
                                      rows="4"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>

                        <div class="mb-4">
                            <label for="edit_deadline" class="block text-gray-700 text-sm font-bold mb-2">
                                Deadline
                            </label>
                            <input type="date" 
                                   name="deadline" 
                                   id="edit_deadline" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <button type="button" 
                                    id="cancelEditBtn"
                                    class="text-gray-600 hover:text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Update Project
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('editProjectModal');
            const editBtns = document.querySelectorAll('.edit-project-btn');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const editForm = document.getElementById('editProjectForm');
            
            // Open modal when edit button is clicked
            editBtns.forEach(button => {
                button.addEventListener('click', function() {
                    const projectId = this.dataset.projectId;
                    const projectName = this.dataset.projectName;
                    const description = this.dataset.description;
                    const deadline = this.dataset.deadline;
                    
                    document.getElementById('editProjectId').value = projectId;
                    document.getElementById('edit_project_name').value = projectName;
                    document.getElementById('edit_description').value = description;
                    document.getElementById('edit_deadline').value = deadline;
                    
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                });
            });
            
            // Close modal functions
            function closeModal() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
            
            closeModalBtn.addEventListener('click', closeModal);
            cancelEditBtn.addEventListener('click', closeModal);
            
            // Close modal when clicking outside the modal content
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
            
            // Handle form submission
            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const projectId = document.getElementById('editProjectId').value;
                const formData = new FormData(editForm);
                
                // Convert FormData to regular object
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                
                // Remove the project_id from data since we use it in URL
                delete data.project_id;
                
                // Make AJAX request
                fetch(`/admin/projects/${projectId}/update-ajax`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showNotification('Project updated successfully', 'success');
                        
                        // Close modal
                        closeModal();
                        
                        // Update the table row with new values
                        const row = document.querySelector(`tr[data-project-id="${projectId}"]`);
                        if (row) {
                            row.querySelector('.text-gray-900').innerHTML = 
                                `${data.project.project_name} ${data.project.status === 'completed' ? '<i class="fas fa-check-circle text-green-500 ml-2" title="Completed"></i>' : ''}`;
                            
                            const descriptionDisplay = data.project.description.length > 50 
                                ? data.project.description.substring(0, 50) + '...' 
                                : data.project.description;
                            
                            row.querySelector('.text-gray-500').textContent = descriptionDisplay;
                            
                            // Update deadline display
                            if (data.project.deadline) {
                                const deadlineDate = new Date(data.project.deadline);
                                const formattedDate = deadlineDate.toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                });
                                
                                const today = new Date();
                                today.setHours(0, 0, 0, 0);
                                const diffTime = deadlineDate - today;
                                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                                
                                let badgeHTML = '';
                                if (diffDays < 0) {
                                    badgeHTML = `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i>${Math.abs(diffDays)} hari terlambat
                                    </span>`;
                                } else if (diffDays === 0) {
                                    badgeHTML = `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                        <i class="fas fa-clock mr-1"></i>Hari ini!
                                    </span>`;
                                } else if (diffDays <= 7) {
                                    badgeHTML = `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-hourglass-half mr-1"></i>${diffDays} hari tersisa
                                    </span>`;
                                } else {
                                    badgeHTML = `<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>${diffDays} hari tersisa
                                    </span>`;
                                }
                                
                                row.cells[3].innerHTML = `<div>${formattedDate}</div><div class="mt-1">${badgeHTML}</div>`;
                            } else {
                                row.cells[3].innerHTML = '<span class="text-gray-400">No deadline</span>';
                            }
                        }
                    } else {
                        throw new Error(data.message || 'Failed to update project');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification(error.message || 'An error occurred while updating the project', 'error');
                });
            });
            
            // Notification function
            function showNotification(message, type) {
                // Remove any existing notifications
                const existing = document.querySelector('.notification');
                if (existing) existing.remove();
                
                const notification = document.createElement('div');
                notification.className = `notification fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg text-white z-50 ${
                    type === 'success' ? 'bg-green-500' : 'bg-red-500'
                }`;
                notification.textContent = message;
                
                document.body.appendChild(notification);
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    notification.remove();
                }, 3000);
            }
        });
    </script>
@endsection