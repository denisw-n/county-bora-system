@extends('layouts.admin')

@section('title', $department->dept_name . ' | Task Ledger')

@section('content')
<header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
    <div class="flex items-center gap-8">
        <nav class="flex gap-2 text-[10px] font-black uppercase tracking-widest">
            <a href="{{ route('admin.departments.index') }}" class="text-gray-400 hover:text-gray-900 transition">Registry</a>
            <span class="text-gray-300">/</span>
            <span class="text-[#00872E]">{{ $department->dept_name }} Ledger</span>
        </nav>
    </div>
    <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-gray-400">
        System Local Time: <span class="text-gray-900 ml-1">{{ now()->format('H:i') }} EAT</span>
    </div>
</header>

<div class="p-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <span class="block text-[9px] font-black uppercase text-gray-400 tracking-[0.2em] mb-1">Assigned Workload</span>
            <span class="text-2xl font-black text-gray-900">{{ $department->reports->count() }} <small class="text-[10px] text-gray-400">INCIDENTS</small></span>
        </div>
        
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
            <span class="block text-[9px] font-black uppercase text-amber-500 tracking-[0.2em] mb-1">Active Operations</span>
            <span class="text-2xl font-black text-gray-900">{{ $department->reports->where('status', '!=', 'resolved')->count() }}</span>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm border-l-4 border-l-[#00872E]">
            <span class="block text-[9px] font-black uppercase text-[#00872E] tracking-[0.2em] mb-1">Success Rate</span>
            @php 
                $total = $department->reports->count();
                $resolved = $department->reports->where('status', 'resolved')->count();
                $rate = $total > 0 ? round(($resolved / $total) * 100) : 0;
            @endphp
            <span class="text-2xl font-black text-gray-900">{{ $rate }}% <small class="text-[10px] text-gray-400 uppercase">Resolved</small></span>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-gray-100 overflow-hidden shadow-2xl shadow-gray-200/50">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50">
                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Tracking Reference</th>
                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Incident Details</th>
                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Temporal Data</th>
                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400">Operational Status</th>
                    <th class="p-6 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Review</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($department->reports as $report)
                <tr class="hover:bg-gray-50/30 transition-all duration-200 group">
                    <td class="p-6">
                        <span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-lg text-[10px] font-black tracking-tighter">
                            {{ $report->tracking_number }}
                        </span>
                    </td>
                    <td class="p-6">
                        <div class="font-black text-gray-800 text-sm mb-0.5 uppercase tracking-tight">{{ $report->category }}</div>
                        <div class="text-[11px] text-gray-400 font-medium line-clamp-1 max-w-xs">{{ $report->description }}</div>
                    </td>
                    <td class="p-6">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Assigned</div>
                        <div class="text-xs font-bold text-gray-700 mb-2">{{ $report->created_at->format('M d, Y') }}</div>
                        
                        @if($report->status == 'resolved')
                            <div class="text-[10px] font-black text-[#00872E] uppercase tracking-tighter">Resolved</div>
                            <div class="text-xs font-bold text-[#00872E]">{{ $report->updated_at->format('M d, Y') }}</div>
                        @endif
                    </td>
                    <td class="p-6">
                        @php
                            $statusColors = [
                                'pending' => 'bg-red-50 text-red-600',
                                'dispatched' => 'bg-indigo-50 text-indigo-600',
                                'in_progress' => 'bg-amber-50 text-amber-600',
                                'resolved' => 'bg-green-50 text-[#00872E]'
                            ];
                            $colorClass = $statusColors[str_replace(' ', '_', strtolower($report->status))] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest {{ $colorClass }}">
                            {{ str_replace(['_', '-'], ' ', $report->status) }}
                        </span>
                    </td>
                    <td class="p-6 text-right">
                        <a href="{{ route('admin.reports.show', $report->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400 group-hover:bg-[#00872E] group-hover:text-white transition-all">
                            →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-20 text-center">
                        <div class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-300">Operational Log Empty</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection