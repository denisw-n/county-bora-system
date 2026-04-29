@extends('layouts.admin')

@section('title', 'Report Details | County Bora')

@section('content')
<header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
    <div class="flex items-center gap-8">
        <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">Incident Detail</span>
        <nav class="flex gap-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
            <a href="{{ route('admin.reports.index') }}" class="py-5 hover:text-gray-900 transition">← Back to Index</a>
            <span class="text-gray-900 py-5">Case #{{ $report->tracking_number }}</span>
        </nav>
    </div>
</header>

<div class="p-8 max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-3 gap-10">
    
    {{-- LEFT COLUMN: INCIDENT INFO --}}
    <div class="lg:col-span-2 space-y-8">
        <div class="bg-white rounded-[2.5rem] p-10 border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <span class="text-[#00872E] text-[10px] font-black uppercase tracking-[0.2em]">{{ $report->category }}</span>
                    <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tighter mt-1">{{ $report->title }}</h1>
                </div>
                <div class="flex flex-col items-end">
                    <span class="px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest
                        @if($report->status == 'resolved') bg-[#00872E] text-white @elseif($report->status == 'rejected') bg-red-600 text-white @else bg-[#FEDF0E] text-[#716200] @endif">
                        {{ $report->status }}
                    </span>
                    <span class="text-[9px] text-gray-400 font-bold uppercase mt-2">Priority: {{ $report->priority }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8 mb-10 pb-10 border-b border-gray-50">
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Location Reference</h4>
                    <p class="text-sm font-bold text-gray-800">{{ $report->location ?? 'Street Location Not Provided' }}</p>
                    <p class="text-[10px] text-gray-400 mt-1 font-mono tracking-tight">{{ $report->latitude }}, {{ $report->longitude }}</p>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Reported By</h4>
                    <p class="text-sm font-bold text-gray-800">{{ $report->user->name ?? ($report->user->first_name . ' ' . $report->user->last_name) }}</p>
                    <p class="text-[10px] text-gray-400 mt-1 font-mono tracking-tight">{{ $report->user->email }}</p>
                </div>
            </div>

            <div class="space-y-8">
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Detailed Description</h4>
                    <div class="bg-gray-50 rounded-3xl p-6 text-gray-600 leading-relaxed text-sm font-medium">
                        {{ $report->description }}
                    </div>
                </div>

                @if($report->image_path)
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Field Evidence</h4>
                    <div class="rounded-[2rem] overflow-hidden border border-gray-100 shadow-inner max-h-[500px]">
                        <img src="{{ asset('storage/' . $report->image_path) }}" class="w-full h-auto object-cover" alt="Incident Evidence">
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN: ADMIN ACTIONS --}}
    <div class="space-y-8">
        {{-- Check if report is resolved or rejected to lock the management console --}}
        @if($report->status == 'resolved' || $report->status == 'rejected')
            <div class="bg-gray-900 rounded-[2.5rem] p-10 shadow-2xl text-white border-t-4 border-[#00872E]">
                <h3 class="text-[#FEDF0E] text-[10px] font-black uppercase tracking-[0.3em] mb-4">Case Archived</h3>
                <div class="space-y-4">
                    <p class="text-[11px] text-gray-400 uppercase font-bold leading-relaxed">
                        This incident has been marked as <span class="text-white">{{ $report->status }}</span>. The management console is now locked to preserve record integrity.
                    </p>
                    <div class="bg-white/5 rounded-2xl p-4 border border-white/10 flex items-center gap-4">
                        <div class="bg-[#00872E]/20 p-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#00872E]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-gray-300">Finalized Record</span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-gray-900 rounded-[2.5rem] p-10 shadow-2xl text-white">
                <h3 class="text-[#FEDF0E] text-[10px] font-black uppercase tracking-[0.3em] mb-6">Management Console</h3>
                
                <form action="{{ route('admin.reports.update', $report->id) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-500 block mb-2">Assign Department</label>
                        <select name="dept_id" class="w-full bg-white/10 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-[#FEDF0E] transition">
                            <option value="" class="text-black">No Department</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" class="text-black" {{ $report->dept_id == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->dept_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-[9px] font-black uppercase tracking-widest text-gray-500 block mb-2">Update Workflow Status</label>
                        <select name="status" class="w-full bg-white/10 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-[#FEDF0E] transition">
                            <option value="pending" class="text-black" {{ $report->status == 'pending' ? 'selected' : '' }}>Pending Approval</option>
                            <option value="dispatched" class="text-black" {{ $report->status == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                            <option value="in_progress" class="text-black" {{ $report->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" class="text-black" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="rejected" class="text-black" {{ $report->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-[#00872E] hover:bg-[#006D24] text-white font-black py-4 rounded-2xl transition uppercase text-[10px] tracking-widest shadow-lg">
                        Apply Updates
                    </button>
                </form>
            </div>
        @endif

        {{-- AUDIT LOG (Mini) --}}
        <div class="bg-white rounded-[2rem] p-8 border border-gray-100">
            <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Audit Information</h4>
            <div class="space-y-3">
                <div class="flex justify-between text-[10px] font-bold">
                    <span class="text-gray-400 uppercase tracking-tighter">Created</span>
                    <span class="text-gray-800">{{ $report->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-[10px] font-bold">
                    <span class="text-gray-400 uppercase tracking-tighter">Modified</span>
                    <span class="text-gray-800">{{ $report->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection