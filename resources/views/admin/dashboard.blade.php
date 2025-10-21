@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ \App\Models\User::count() }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-users text-blue-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Projects Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Projects</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ \App\Models\Project::count() }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-project-diagram text-green-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Working Users Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Working Users</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ \App\Models\User::where('current_task_status', 'working')->count() }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-user-clock text-yellow-500 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Idle Users Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Idle Users</p>
                    <p class="text-2xl font-semibold text-gray-800">{{ \App\Models\User::where('current_task_status', 'idle')->count() }}</p>
                </div>
                <div class="p-3 bg-gray-100 rounded-full">
                    <i class="fas fa-user-alt-slash text-gray-500 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Recent Projects -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Projects</h2>
                    <a href="{{ route('admin.projects.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach(\App\Models\Project::with(['creator', 'members'])->latest()->take(5)->get() as $project)
                        <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <a href="{{ route('admin.projects.show', $project) }}" class="text-lg font-medium text-blue-600 hover:text-blue-800">
                                        {{ $project->project_name }}
                                    </a>
                                    <p class="text-sm text-gray-500">Created by {{ $project->creator->full_name }}</p>
                                </div>
                                <span class="text-sm text-gray-500">
                                    {{ $project->members->count() }} members
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Users</h2>
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">View All</a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                        <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $user->full_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->current_task_status === 'working' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->current_task_status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection