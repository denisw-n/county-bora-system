@extends('layouts.admin')

@section('title', $ward->name . ' | Incidents')

@section('content')
    <header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
        <div class="flex items-center gap-8">
            <a href="{{ route('admin.wards.index') }}" class="text-gray-400 hover:text-gray-900 transition flex items-center gap-2">
                <span class="text-lg">←</span>
                <span class="text-[10px] font-black uppercase tracking-widest">Back to Wards</span>
            </a>
            <div class="h-4 w-[1px] bg-gray-200"></div>
            <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">{{ $ward->name }} Ward</span>
        </div>
        
        <div class="flex items-center gap-4">
            <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Sift by Department:</label>
            <form action="{{ route('admin.wards.show', $ward->id) }}" method="GET" id="siftForm">
                <select name="dept_id" 
                        onchange="document.getElementById('siftForm').submit()"
                        class="bg-[#F3F4F6] border-none rounded-xl px-4 py-2 text-[11px] font-black uppercase tracking-tight text-gray-700 focus:ring-2 focus:ring-[#00872E] cursor-pointer">
                    <option value="">All Operational Units</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('dept_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->dept_name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </header>

    <div class="p-8 max-w-[1400px] mx-auto space-y-6">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sub-County</p>
                <h4 class="text-lg font-black text-gray-800 uppercase">{{ $ward->sub_county }}</h4>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Incidents</p>
                <h4 class="text-lg font-black text-gray-800 uppercase">{{ $reports->total() }}</h4>
            </div>
            <div class="bg-[#00872E] p-6 rounded-[2rem] shadow-lg shadow-green-100">
                <p class="text-[10px] font-black text-white/70 uppercase tracking-widest mb-1">Active Sift</p>
                <h4 class="text-lg font-black text-white uppercase">
                    {{ request('dept_id') ? $departments->firstWhere('id', request('dept_id'))->dept_name : 'Unified View' }}
                </h4>
            </div>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50">
                <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest">Incident Registry</h3>
            </div>
            
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                        <th class="px-8 py-5">Tracking Number</th>
                        <th class="px-8 py-5">Category</th>
                        <th class="px-8 py-5">Assigned Unit</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Activity</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-semibold text-gray-600">
                    @forelse($reports as $report)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition group">
                        <td class="px-8 py-5">
                            <span class="font-black text-gray-800 uppercase tracking-tight">
                                {{ $report->tracking_number }}
                            </span>
                        </td>
                        <td class="px-8 py-5 uppercase text-[10px] tracking-widest">
                            {{ $report->category }}
                        </td>
                        <td class="px-8 py-5">
                            <span class="bg-green-50 text-[#00872E] px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">
                                {{ $report->department->dept_name ?? 'Unassigned' }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full {{ $report->status == 'resolved' ? 'bg-green-500' : 'bg-orange-400' }}"></div>
                                <span class="uppercase text-[10px] font-black tracking-widest">{{ $report->status }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right font-mono text-[10px] text-gray-400">
                            {{ $report->created_at->format('d/m/Y') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-16 text-center text-gray-400 font-bold uppercase tracking-widest text-[10px]">
                            No incidents logged for this department in {{ $ward->name }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="p-6 bg-gray-50/30">
                {{ $reports->links() }}
            </div>
        </div>
    </div>
@endsection