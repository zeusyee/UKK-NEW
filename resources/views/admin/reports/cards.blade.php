@extends('layouts.admin')

@section('title', 'Laporan Cards')

@section('content')
<div class="px-4 py-6 sm:px-0">
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('admin.reports.index') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i>Kembali ke Reports
            </a>
            <h2 class="text-3xl font-bold text-gray-900">
                <i class="fas fa-clipboard-list mr-3"></i>Laporan Cards
            </h2>
            <p class="mt-1 text-sm text-gray-600">Periode: <strong>{{ $periodTitle }}</strong></p>
        </div>
        <a href="{{ route('admin.reports.cards', array_merge(request()->all(), ['print' => '1'])) }}" 
           class="bg-red-500 hover:bg-red-600 text-white font-semibold py-2 px-4 rounded-lg">
            <i class="fas fa-file-pdf mr-2"></i>Download PDF
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('admin.reports.cards') }}" method="GET">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Project (Optional)</label>
                    <select name="project_id" class="w-full border-gray-300 rounded-lg">
                        <option value="">Semua Project</option>
                        @foreach(\App\Models\Project::orderBy('project_name')->get() as $project)
                            <option value="{{ $project->project_id }}" {{ request('project_id') == $project->project_id ? 'selected' : '' }}>
                                {{ $project->project_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-6 rounded-lg">
                    <i class="fas fa-search mr-2"></i>Tampilkan Laporan
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
            <div class="text-sm text-blue-600 font-semibold">Total Cards</div>
            <div class="text-2xl font-bold text-blue-900">{{ $totalCards }}</div>
        </div>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="text-sm text-green-600 font-semibold">Completed</div>
            <div class="text-2xl font-bold text-green-900">{{ $completedCards }}</div>
        </div>
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
            <div class="text-sm text-yellow-600 font-semibold">In Progress</div>
            <div class="text-2xl font-bold text-yellow-900">{{ $inProgressCards }}</div>
        </div>
    </div>

    <!-- Cards Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold">Detail Cards ({{ $cards->count() }})</h3>
        </div>
        @if($cards->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Card</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project / Board</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created At</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cards as $card)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $card->card_title }}</div>
                            <div class="text-sm text-gray-500">{{ Str::limit($card->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $card->board->project->project_name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $card->board->board_name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $card->assignedUser->full_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $card->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            @php
                                $totalSubtasks = $card->subtasks->count();
                                $completedSubtasks = $card->subtasks->where('status', 'done')->count();
                                $progress = $totalSubtasks > 0 ? round(($completedSubtasks / $totalSubtasks) * 100) : 0;
                            @endphp
                            <div class="flex items-center">
                                <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                                <span class="text-sm">{{ $progress }}%</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $completedSubtasks }}/{{ $totalSubtasks }} subtasks</div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-12 text-center text-gray-500">Tidak ada data cards untuk periode ini</div>
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
