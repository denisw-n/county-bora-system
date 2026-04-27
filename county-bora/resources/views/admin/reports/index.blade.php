@extends('layouts.admin')

@section('title', 'Reports | County Bora Admin')

@section('content')
<header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
    <div class="flex items-center gap-8">
        <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">Operations Center</span>
        <nav class="flex gap-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
            <a href="{{ route('admin.dashboard') }}" class="py-5 hover:text-gray-900 transition">Global View</a>
            <a href="{{ route('admin.reports.index') }}" class="text-[#00872E] border-b-2 border-[#00872E] py-5">Incident Reports</a>
        </nav>
    </div>
</header>

<div class="p-8 max-w-[1400px] mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-[#00872E] text-white p-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg">
            ✓ {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest">Incoming Incidents</h3>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                    <th class="px-8 py-5">Report ID</th>
                    <th class="px-8 py-5">Category</th>
                    <th class="px-8 py-5">Ward</th>
                    <th class="px-8 py-5">Priority</th>
                    <th class="px-8 py-5">Status</th>
                    <th class="px-8 py-5 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs font-semibold text-gray-600">
                @forelse($reports as $report)
                <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition">
                    <td class="px-8 py-5 font-bold text-gray-800">#{{ strtoupper(substr($report->id, 0, 8)) }}</td>
                    <td class="px-8 py-5 text-gray-400 uppercase">{{ $report->category }}</td>
                    <td class="px-8 py-5 font-bold text-[#00872E]">{{ $report->ward->name ?? 'N/A' }}</td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase 
                            {{ $report->priority == 'high' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $report->priority }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <span class="bg-[#FEDF0E] text-[#716200] px-3 py-1 rounded-full font-black text-[9px] uppercase">
                            {{ $report->status }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <button onclick="openDispatchModal('{{ $report->id }}')" class="text-[#00872E] hover:underline font-black uppercase text-[10px] tracking-widest">
                            Review & Dispatch
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-[10px]">
                        No active incidents reported.
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

<div id="dispatchModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-md shadow-2xl">
        <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest mb-6">Dispatch Incident</h3>
        
        <form id="dispatchForm" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Target Department</label>
                    <select name="dept_id" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00872E]">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Priority Level</label>
                    <select name="priority" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00872E]">
                        <option value="low">Routine</option>
                        <option value="medium">Urgent</option>
                        <option value="high">Critical</option>
                    </select>
                </div>

                <input type="hidden" name="status" value="dispatched">

                <button type="submit" class="w-full bg-[#00872E] text-white font-black py-4 rounded-2xl hover:bg-[#006D24] transition uppercase text-xs tracking-widest shadow-lg mt-4">
                    Confirm Dispatch
                </button>
                <button type="button" onclick="closeModal()" class="w-full text-gray-400 font-bold py-2 text-[10px] uppercase tracking-widest">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openDispatchModal(reportId) {
        const modal = document.getElementById('dispatchModal');
        const form = document.getElementById('dispatchForm');
        form.action = `/admin/reports/${reportId}`;
        modal.classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('dispatchModal').classList.add('hidden');
    }
</script>
@endsection