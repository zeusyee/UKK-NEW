@extends('layouts.admin')

@section('title', 'Laporan Subtasks')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Reports
            </a>
            <h2 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-tasks mr-3"></i>Laporan Subtasks
            </h2>
            <p class="mt-1 text-sm text-gray-600">Periode: <strong>{{ $periodTitle }}</strong></p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route('admin.reports.subtasks', array_merge(request()->all(), ['print' => '1'])) }}" 
               class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>Download PDF
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6 no-print">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-filter mr-2"></i>Filter Laporan</h3>
        <form action="{{ route('admin.reports.subtasks') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Report Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Periode</label>
                    <select name="report_type" id="report_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" onchange="toggleDateFields()">
                        <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="custom" {{ request('report_type') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                <!-- Daily Date -->
                <div id="daily_field" style="{{ request('report_type') != 'daily' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="date" value="{{ request('date', now()->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <!-- Monthly -->
                <div id="monthly_field" style="{{ request('report_type') != 'monthly' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <!-- Custom Range Start -->
                <div id="custom_start_field" style="{{ request('report_type') != 'custom' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <!-- Custom Range End -->
                <div id="custom_end_field" style="{{ request('report_type') != 'custom' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <!-- Member Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Member (Optional)</label>
                    <select name="member_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Semua Member</option>
                        @foreach(\App\Models\User::where('role', 'member')->orderBy('full_name')->get() as $user)
                            <option value="{{ $user->user_id }}" {{ request('member_id') == $user->user_id ? 'selected' : '' }}>
                                {{ $user->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project (Optional)</label>
                    <select name="project_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Semua Project</option>
                        @foreach(\App\Models\Project::orderBy('project_name')->get() as $project)
                            <option value="{{ $project->project_id }}" {{ request('project_id') == $project->project_id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-6 rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>Tampilkan Laporan
                </button>
                <a href="{{ route('admin.reports.subtasks', ['report_type' => 'daily', 'date' => now()->format('Y-m-d')]) }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-2 px-6 rounded-lg transition-colors">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-blue-600 font-semibold">Total Subtasks</div>
                    <div class="text-2xl font-bold text-blue-900">{{ $totalTasks }}</div>
                </div>
                <i class="fas fa-tasks text-blue-500 text-3xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-green-600 font-semibold">Approved</div>
                    <div class="text-2xl font-bold text-green-900">{{ $totalApproved }}</div>
                </div>
                <i class="fas fa-check-circle text-green-500 text-3xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-red-600 font-semibold">Rejected</div>
                    <div class="text-2xl font-bold text-red-900">{{ $totalRejected }}</div>
                </div>
                <i class="fas fa-times-circle text-red-500 text-3xl opacity-50"></i>
            </div>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm text-yellow-600 font-semibold">Pending</div>
                    <div class="text-2xl font-bold text-yellow-900">{{ $totalPending }}</div>
                </div>
                <i class="fas fa-clock text-yellow-500 text-3xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Member Statistics -->
    @if($memberStats->count() > 0)
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4"><i class="fas fa-users mr-2"></i>Statistik Per Member</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tasks</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rejected</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Success Rate</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($memberStats as $stat)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $stat['user']->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $stat['user']->username }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">
                            {{ $stat['total_tasks'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $stat['approved'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                {{ $stat['rejected'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                {{ $stat['pending'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $successRate = $stat['total_tasks'] > 0 ? round(($stat['approved'] / $stat['total_tasks']) * 100, 1) : 0;
                            @endphp
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $successRate }}%"></div>
                                </div>
                                <span class="text-sm font-semibold">{{ $successRate }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Subtasks Details -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold"><i class="fas fa-list mr-2"></i>Detail Subtasks ({{ $subtasks->count() }})</h3>
        </div>
        
        @if($subtasks->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtask</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Card / Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completed At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reviewed By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($subtasks as $subtask)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $subtask->subtask_title }}</div>
                            @if($subtask->description)
                            <div class="text-sm text-gray-500">{{ Str::limit($subtask->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $subtask->card->card_title ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $subtask->card->board->project->project_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $subtask->assignedUser->full_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $subtask->completed_at ? $subtask->completed_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($subtask->status === 'approved')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>Approved
                                </span>
                            @elseif($subtask->status === 'rejected')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>Rejected
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>{{ ucfirst($subtask->status) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $subtask->reviewer->full_name ?? '-' }}
                            @if($subtask->reviewed_at)
                            <div class="text-xs text-gray-400">{{ $subtask->reviewed_at->format('d M Y') }}</div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">Tidak ada data subtasks untuk periode ini</p>
        </div>
        @endif
    </div>
</div>

<script>
function toggleDateFields() {
    const reportType = document.getElementById('report_type').value;
    document.getElementById('daily_field').style.display = reportType === 'daily' ? 'block' : 'none';
    document.getElementById('monthly_field').style.display = reportType === 'monthly' ? 'block' : 'none';
    document.getElementById('custom_start_field').style.display = reportType === 'custom' ? 'block' : 'none';
    document.getElementById('custom_end_field').style.display = reportType === 'custom' ? 'block' : 'none';
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endsection
