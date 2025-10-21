@extends('layouts.member')

@section('title', 'Member Dashboard')

@section('content')
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-semibold mb-6">My Projects</h2>

        @if($assignedProjects->isEmpty())
            <div class="text-gray-500 text-center py-4">
                <p>You are not assigned to any projects yet.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($assignedProjects as $project)
                    <div class="bg-white border rounded-lg shadow-sm hover:shadow-md transition-shadow">
                        <div class="p-5">
                            <h3 class="text-xl font-semibold mb-2">{{ $project->project_name }}</h3>
                            <p class="text-gray-600 mb-4">{{ Str::limit($project->description, 100) }}</p>
                            
                            <div class="mb-4">
                                <div class="flex items-center mb-2">
                                    <span class="text-sm text-gray-600">Deadline:</span>
                                    <span class="ml-2 text-sm">{{ $project->deadline ? $project->deadline->format('M d, Y') : 'No deadline set' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-600">Created by:</span>
                                    <span class="ml-2 text-sm">{{ optional($project->creator)->full_name }}</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('member.project.details', $project) }}" 
                                   class="inline-block bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection