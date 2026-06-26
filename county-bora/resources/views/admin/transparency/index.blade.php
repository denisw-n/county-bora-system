@extends('layouts.admin')

@section('title', 'Transparency Portal')

@section('content')
<div class="p-8 max-w-[1400px] mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-black text-gray-800 uppercase tracking-widest">Transparency Portal</h1>
        <form action="{{ route('admin.transparency.refresh') }}" method="POST">
            @csrf
            <button type="submit" class="bg-[#00872E] text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-[#006D24] transition shadow-lg">
                Sync All Stats
            </button>
        </form>
    </div>

    {{-- Main Top Charts --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-xs font-black uppercase text-gray-400 mb-4 tracking-wider">Departmental Performance</h3>
            <div class="relative h-[250px] w-full"><canvas id="barChart"></canvas></div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-xs font-black uppercase text-gray-400 mb-4 tracking-wider">Historical Trend</h3>
            <div class="relative h-[250px] w-full"><canvas id="lineChart"></canvas></div>
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-xs font-black uppercase text-gray-400 mb-4 tracking-wider">Status Distribution</h3>
            <div class="relative h-[250px] w-full"><canvas id="doughnutChart"></canvas></div>
        </div>
    </div>

    {{-- Deep Dive Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-xs font-black uppercase text-gray-400 mb-4 tracking-wider">Dept. Efficiency Index</h3>
            <div class="relative h-[300px] w-full"><canvas id="radarChart"></canvas></div>
        </div>
        
        <div class="grid grid-cols-1 gap-4">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-black uppercase text-gray-400 mb-4 tracking-wider">Top 5 Performing Wards</h3>
                <div class="relative h-[150px] w-full"><canvas id="topWardsChart"></canvas></div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-xs font-black uppercase text-gray-400 mb-4 tracking-wider">Bottom 5 Performing Wards</h3>
                <div class="relative h-[150px] w-full"><canvas id="bottomWardsChart"></canvas></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const data = @json($chartData);
        const config = { responsive: true, maintainAspectRatio: false };

        new Chart(document.getElementById('barChart'), {
            type: 'bar',
            data: { labels: data.labels, datasets: [{ label: 'Score %', data: data.performance, backgroundColor: '#00872E', borderRadius: 6 }] },
            options: { ...config }
        });

        // Updated Line Chart with dynamic labels and fixed 0-100 scale
        new Chart(document.getElementById('lineChart'), {
            type: 'line',
            data: { 
                labels: Array.from({length: data.trends.length}, (_, i) => i + 1), 
                datasets: [{ 
                    label: 'System Efficiency (%)', 
                    data: data.trends, 
                    borderColor: '#00872E', 
                    tension: 0.4, 
                    fill: true, 
                    backgroundColor: 'rgba(0, 135, 46, 0.1)' 
                }] 
            },
            options: { 
                ...config,
                scales: {
                    y: { min: 0, max: 100 }
                }
            }
        });

        new Chart(document.getElementById('doughnutChart'), {
            type: 'doughnut',
            data: { 
                labels: Object.keys(data.status), 
                datasets: [{ data: Object.values(data.status), backgroundColor: ['#00872E', '#FFCE56', '#36A2EB', '#9966FF'] }] 
            },
            options: { ...config, plugins: { legend: { position: 'bottom' } } }
        });

        new Chart(document.getElementById('radarChart'), {
            type: 'radar',
            data: {
                labels: data.radarLabels,
                datasets: [{ label: 'Efficiency Index', data: data.radarData, backgroundColor: 'rgba(0, 135, 46, 0.2)', borderColor: '#00872E' }]
            },
            options: { ...config, scales: { r: { beginAtZero: true, max: 100 } } }
        });

        new Chart(document.getElementById('topWardsChart'), {
            type: 'bar',
            data: { labels: data.topWardsLabels, datasets: [{ label: 'Score %', data: data.topWardsScores, backgroundColor: '#00872E', borderRadius: 4 }] },
            options: { ...config, indexAxis: 'y', scales: { x: { max: 100 } } }
        });

        new Chart(document.getElementById('bottomWardsChart'), {
            type: 'bar',
            data: { labels: data.bottomWardsLabels, datasets: [{ label: 'Score %', data: data.bottomWardsScores, backgroundColor: '#DC2626', borderRadius: 4 }] },
            options: { ...config, indexAxis: 'y', scales: { x: { max: 100 } } }
        });
    </script>

    {{-- Filters & Lists --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <form action="{{ route('admin.transparency.index') }}" method="GET" class="flex flex-wrap gap-6 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Filter by Ward</label>
                <select name="ward_id" class="w-full text-sm border-gray-100 rounded-xl bg-gray-50 focus:ring-0">
                    <option value="">All Wards</option>
                    @foreach($wards as $ward)
                        <option value="{{ $ward->id }}" {{ request('ward_id') == $ward->id ? 'selected' : '' }}>{{ $ward->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-[10px] font-black uppercase text-gray-400 mb-2">Filter by Department</label>
                <select name="dept_id" class="w-full text-sm border-gray-100 rounded-xl bg-gray-50 focus:ring-0">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('dept_id') == $dept->id ? 'selected' : '' }}>{{ $dept->dept_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-800 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase hover:bg-black transition">Apply</button>
                <a href="{{ route('admin.transparency.index') }}" class="px-6 py-3 rounded-xl font-black text-[10px] uppercase text-gray-400 hover:text-gray-800 transition">Clear</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="font-black text-xs uppercase text-gray-400 mb-6 tracking-wider">Daily Operational Performance</h2>
            @if(!isset($processedStats) || $processedStats->isEmpty())
                <div class="text-center py-10 text-gray-400 border-2 border-dashed rounded-xl">No data found.</div>
            @else
                <div class="space-y-4">
                    @foreach($processedStats as $data)
                        <div class="p-5 border border-gray-100 rounded-2xl bg-gray-50/50">
                            <h3 class="font-black text-gray-800 text-sm">{{ $data['dept_name'] }}</h3>
                            <div class="grid grid-cols-4 gap-2 mt-4">
                                @foreach($data['totals'] as $status => $count)
                                    <div class="bg-white border p-2 rounded-lg text-center"><p class="text-[8px] uppercase font-black text-gray-400">{{ $status }}</p><p class="text-sm font-black">{{ $count }}</p></div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="font-black text-sm uppercase text-gray-500 mb-4">Latest Snapshots</h2>
            <div class="space-y-3">
                @foreach($snapshots as $stat)
                    <div class="p-3 border rounded-xl flex justify-between items-center">
                        <div>
                            <p class="text-xs font-bold">{{ $stat->department->dept_name ?? 'N/A' }}</p>
                            <p class="text-[9px] text-gray-400 uppercase">{{ $stat->metric_type }}</p>
                            <p class="text-[9px] text-gray-400 font-bold uppercase">
                                {{ $stat->created_at ? $stat->created_at->format('M d, H:i') : 'N/A' }}
                            </p>
                        </div>
                        <p class="text-sm font-black text-[#00872E]">{{ number_format($stat->percentage, 1) }}%</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection