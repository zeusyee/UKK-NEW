@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-chart-bar mr-3"></i>Laporan & Cetak
        </h2>
        <p class="mt-2 text-sm text-gray-600">Cetak laporan subtasks, cards, dan projects berdasarkan periode waktu tertentu</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Subtasks Report Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h3 class="text-xl font-bold">Laporan Subtasks</h3>
                        <p class="text-sm text-blue-100 mt-1">Member Performance</p>
                    </div>
                    <i class="fas fa-tasks text-4xl opacity-50"></i>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">
                    Laporan detail subtasks yang telah dikerjakan member berdasarkan hari, bulan, atau periode tertentu
                </p>
                <ul class="space-y-2 mb-6 text-sm text-gray-700">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Filter by Member</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Filter by Project</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Status Review</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Completion Time</li>
                </ul>
                <a href="{{ route('admin.reports.subtasks', ['report_type' => 'daily', 'date' => now()->format('Y-m-d')]) }}" 
                   class="block w-full bg-blue-500 hover:bg-blue-600 text-white text-center font-semibold py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>Buat Laporan
                </a>
            </div>
        </div>

        <!-- Cards Report Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h3 class="text-xl font-bold">Laporan Cards</h3>
                        <p class="text-sm text-green-100 mt-1">Task Overview</p>
                    </div>
                    <i class="fas fa-clipboard-list text-4xl opacity-50"></i>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">
                    Laporan cards yang dibuat dan status penyelesaiannya per periode
                </p>
                <ul class="space-y-2 mb-6 text-sm text-gray-700">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Filter by Project</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Completion Status</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Subtasks Progress</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Assigned Members</li>
                </ul>
                <a href="{{ route('admin.reports.cards', ['report_type' => 'daily', 'date' => now()->format('Y-m-d')]) }}" 
                   class="block w-full bg-green-500 hover:bg-green-600 text-white text-center font-semibold py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>Buat Laporan
                </a>
            </div>
        </div>

        <!-- Projects Report Card -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                <div class="flex items-center justify-between text-white">
                    <div>
                        <h3 class="text-xl font-bold">Laporan Projects</h3>
                        <p class="text-sm text-purple-100 mt-1">Project Summary</p>
                    </div>
                    <i class="fas fa-project-diagram text-4xl opacity-50"></i>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 text-sm mb-4">
                    Laporan project yang dibuat, status, dan statistik penyelesaian
                </p>
                <ul class="space-y-2 mb-6 text-sm text-gray-700">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Filter by Status</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Team Members</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Deadline Status</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Progress Overview</li>
                </ul>
                <a href="{{ route('admin.reports.projects', ['report_type' => 'daily', 'date' => now()->format('Y-m-d')]) }}" 
                   class="block w-full bg-purple-500 hover:bg-purple-600 text-white text-center font-semibold py-3 px-4 rounded-lg transition-colors">
                    <i class="fas fa-file-alt mr-2"></i>Buat Laporan
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Access Section -->
    <div class="mt-8 bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-6 border border-gray-200">
        <h3 class="text-lg font-bold text-gray-900 mb-4">
            <i class="fas fa-bolt mr-2 text-yellow-500"></i>Quick Access
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.reports.subtasks', ['report_type' => 'daily', 'date' => now()->format('Y-m-d')]) }}" 
               class="flex items-center justify-between bg-white p-4 rounded-lg hover:shadow-md transition-shadow border border-gray-200">
                <div>
                    <div class="text-sm text-gray-600">Subtasks</div>
                    <div class="font-semibold text-gray-900">Laporan Hari Ini</div>
                </div>
                <i class="fas fa-calendar-day text-blue-500 text-2xl"></i>
            </a>
            
            <a href="{{ route('admin.reports.cards', ['report_type' => 'monthly', 'month' => now()->format('Y-m')]) }}" 
               class="flex items-center justify-between bg-white p-4 rounded-lg hover:shadow-md transition-shadow border border-gray-200">
                <div>
                    <div class="text-sm text-gray-600">Cards</div>
                    <div class="font-semibold text-gray-900">Laporan Bulan Ini</div>
                </div>
                <i class="fas fa-calendar-alt text-green-500 text-2xl"></i>
            </a>
            
            <a href="{{ route('admin.reports.projects', ['report_type' => 'monthly', 'month' => now()->format('Y-m')]) }}" 
               class="flex items-center justify-between bg-white p-4 rounded-lg hover:shadow-md transition-shadow border border-gray-200">
                <div>
                    <div class="text-sm text-gray-600">Projects</div>
                    <div class="font-semibold text-gray-900">Laporan Bulan Ini</div>
                </div>
                <i class="fas fa-calendar-week text-purple-500 text-2xl"></i>
            </a>
        </div>
    </div>

    <!-- Info Section -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-500 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong class="font-semibold">Tips:</strong> Setiap laporan dapat di-filter berdasarkan periode waktu (harian, bulanan, atau custom range) dan dapat langsung dicetak dalam format PDF.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
