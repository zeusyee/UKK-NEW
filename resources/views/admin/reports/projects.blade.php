@extends('layouts.admin')

@section('title', 'Laporan Projects')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Reports
            </a>
            <h2 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-project-diagram mr-3"></i>Laporan Projects
            </h2>
            <p class="mt-1 text-sm text-gray-600">Periode: <strong>{{ $periodTitle }}</strong></p>
        </div>
        <a href="{{ route('admin.reports.projects', array_merge(request()->all(), ['print' => '1'])) }}" 
           class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg">
            <i class="fas fa-file-pdf mr-2"></i>Download PDF
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.reports.projects') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Periode</label>
                    <select name="report_type" id="report_type" class="w-full border-gray-300 rounded-lg" onchange="toggleDateFields()">
                        <option value="daily" {{ request('report_type') == 'daily' ? 'selected' : '' }}>Harian</option>
                        <option value="monthly" {{ request('report_type') == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                        <option value="custom" {{ request('report_type') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>
                <div id="daily_field" style="{{ request('report_type') != 'daily' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" name="date" value="{{ request('date', now()->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div id="monthly_field" style="{{ request('report_type') != 'monthly' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                    <input type="month" name="month" value="{{ request('month', now()->format('Y-m')) }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div id="custom_start_field" style="{{ request('report_type') != 'custom' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div id="custom_end_field" style="{{ request('report_type') != 'custom' ? 'display:none' : '' }}">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border-gray-300 rounded-lg">
                        <option value="">Semua</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-purple-500 hover:bg-purple-600 text-white font-semibold py-2 px-6 rounded-lg">
                    <i class="fas fa-search mr-2"></i>Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="text-sm text-blue-600 font-semibold">Total Projects</div>
            <div class="text-2xl font-bold text-blue-900">{{ $totalProjects }}</div>
        </div>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="text-sm text-green-600 font-semibold">Active</div>
            <div class="text-2xl font-bold text-green-900">{{ $activeProjects }}</div>
        </div>
        <div class="bg-purple-50 border-l-4 border-purple-500 p-4 rounded">
            <div class="text-sm text-purple-600 font-semibold">Completed</div>
            <div class="text-2xl font-bold text-purple-900">{{ $completedProjects }}</div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold">Detail Projects ({{ $projects->count() }})</h3>
        </div>
        @if($projects->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Members</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($projects as $project)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $project->project_name }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($project->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $project->creator->full_name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $project->members->count() }} members</td>
                        <td class="px-6 py-4 text-sm">{{ $project->deadline ? date('d M Y', strtotime($project->deadline)) : '-' }}</td>
                        <td class="px-6 py-4">
                            @if($project->status === 'completed')
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Active</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-500">Tidak ada data projects untuk periode ini</div>
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
@endsection
