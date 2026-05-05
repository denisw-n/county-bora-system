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

            <div class="space-y-12">
                <div>
                    <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Detailed Description</h4>
                    <div class="bg-gray-50 rounded-3xl p-6 text-gray-600 leading-relaxed text-sm font-medium">
                        {{ $report->description }}
                    </div>
                </div>

                {{-- NEW: CITIZEN RATING SECTION --}}
                @if($report->rating)
                <div class="bg-[#FEDF0E]/5 border border-[#FEDF0E]/20 rounded-[2rem] p-8">
                    <h4 class="text-[10px] font-black text-[#716200] uppercase tracking-widest mb-4">Citizen Satisfaction Rating</h4>
                    <div class="flex items-start gap-6">
                        <div class="bg-white px-6 py-4 rounded-2xl shadow-sm border border-[#FEDF0E]/30 flex flex-col items-center">
                            <span class="text-3xl font-black text-gray-900">{{ $report->rating->stars }}</span>
                            <div class="flex text-[#FEDF0E] mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 {{ $i <= $report->rating->stars ? 'fill-current' : 'text-gray-200' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                @endfor
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Feedback Provided</p>
                            <p class="text-sm font-bold text-gray-800 leading-relaxed italic">
                                "{{ $report->rating->comment ?? 'No written feedback provided.' }}"
                            </p>
                            <p class="text-[9px] text-gray-400 mt-2 font-bold uppercase tracking-tighter">
                                Submitted {{ $report->rating->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- MULTI-IMAGE EVIDENCE GALLERY --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Field Evidence Gallery</h4>
                        <span class="text-[9px] font-black bg-gray-100 px-2 py-1 rounded text-gray-500 uppercase">{{ $report->media->count() }} Files</span>
                    </div>

                    @if($report->media->count() > 0)
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($report->media as $image)
                                <div class="group relative aspect-square rounded-[2rem] overflow-hidden border border-gray-100 shadow-sm bg-gray-50">
                                    <img src="{{ asset('storage/' . $image->file_path) }}" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                         alt="Evidence">
                                    
                                    {{-- Lightbox / Full View Trigger --}}
                                    <a href="{{ asset('storage/' . $image->file_path) }}" target="_blank" 
                                       class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 border-2 border-dashed border-gray-100 rounded-[2.5rem] py-12 flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-200 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 00-2 2z" />
                            </svg>
                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No Visual Evidence Attached</span>
                        </div>
                    @endif
                </div>
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
                        <select name="dept_id" class="w-full bg-white/10 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-[#FEDF0E] transition text-white">
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
                        <select name="status" class="w-full bg-white/10 border-none rounded-xl px-4 py-3 text-xs font-bold focus:ring-2 focus:ring-[#FEDF0E] transition text-white">
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