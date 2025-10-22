@extends('layouts.leader')

@section('title', 'Leader Dashboard')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-6">My Projects (as Leader)</h2>

        @if($leadProjects->isEmpty())
            <div class="text-gray-500 text-center py-8">
                <i class="fas fa-folder-open text-6xl mb-4 text-gray-300"></i>
                <p>You are not assigned as a leader to any projects yet.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($leadProjects as $project)
                    <div class="bg-white border-2 border-green-200 rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <div class="flex justify-between items-start mb-3">
                                <h3 class="text-xl font-semibold text-gray-800">{{ $project->project_name }}</h3>
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                    <i class="fas fa-crown mr-1"></i>Leader
                                </span>
                            </div>
                            
                            <p class="text-gray-600 mb-4 text-sm">{{ Str::limit($project->description, 100) }}</p>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-calendar text-gray-400 w-5"></i>
                                    <span class="text-gray-600">Deadline:</span>
                                    <span class="ml-2 font-medium">
                                        {{ $project->deadline ? $project->deadline->format('M d, Y') : 'No deadline' }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-user text-gray-400 w-5"></i>
                                    <span class="text-gray-600">Created by:</span>
                                    <span class="ml-2">{{ $project->creator->full_name }}</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-users text-gray-400 w-5"></i>
                                    <span class="text-gray-600">Team size:</span>
                                    <span class="ml-2 font-medium">{{ $project->members->count() }} members</span>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t border-gray-200">
                                <a href="{{ route('leader.project.details', $project) }}" 
                                   class="block w-
                                   full text-center bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors">
                                    <i class="fas fa-tasks mr-2"></i>Manage Project
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection