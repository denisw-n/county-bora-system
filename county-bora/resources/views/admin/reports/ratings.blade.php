@extends('layouts.admin')

@section('title', 'Report Ratings & Feedback')

@section('content')
<div class="p-8 max-w-[1200px] mx-auto">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-xl font-black uppercase text-gray-800 tracking-tight">Report Ratings</h1>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Review feedback submitted by citizens</p>
        </div>
        <a href="{{ route('admin.reports.index') }}" class="bg-gray-100 px-4 py-2 rounded-xl text-[10px] font-black uppercase text-gray-600 hover:bg-gray-200 transition">
            ← Back to Reports
        </a>
    </div>
    
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        @if($ratings->isEmpty())
            <div class="p-12 text-center">
                <p class="text-sm text-gray-400">No ratings have been submitted yet.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-gray-50/50">
                        <tr class="text-gray-400 uppercase tracking-widest text-left">
                            <th class="px-8 py-4">User</th>
                            <th class="px-4 py-4">Report Title</th>
                            <th class="px-4 py-4">Rating</th>
                            <th class="px-4 py-4">Comment</th>
                            <th class="px-4 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ratings as $rating)
                            <tr class="border-t border-gray-50 hover:bg-gray-50/50 transition">
                                <td class="px-8 py-4 font-black text-gray-800">
                                    {{ $rating->user->full_name ?? 'Anonymous' }}
                                </td>
                                <td class="px-4 py-4 font-medium">{{ $rating->report->title ?? 'N/A' }}</td>
                                <td class="px-4 py-4">
                                    <span class="bg-[#FEDF0E]/20 text-[#716200] px-3 py-1 rounded-lg font-black text-[10px]">
                                        {{ $rating->stars }} ★
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-gray-600 italic max-w-xs truncate">{{ $rating->comment ?? 'No comment provided' }}</td>
                                <td class="px-4 py-4 text-gray-400">{{ $rating->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6 border-t border-gray-100">
                {{ $ratings->links() }}
            </div>
        @endif
    </div>
</div>
@endsection