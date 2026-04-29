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

<div class="p-8 max-w-[1400px] mx-auto space-y-10">
    {{-- SUCCESS ALERT --}}
    @if(session('success'))
        <div class="bg-[#00872E] text-white p-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg animate-in fade-in slide-in-from-top-4">
            ✓ {{ session('success') }}
        </div>
    @endif

    {{-- ERROR ALERT --}}
    @if ($errors->any())
        <div class="bg-red-600 text-white p-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>⚠ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- RAPID STATUS UPDATE CONSOLE --}}
    <div class="bg-gray-900 rounded-[3rem] p-10 shadow-2xl relative overflow-hidden">
        <div class="relative z-10 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <span class="text-[#FEDF0E] text-[10px] font-black uppercase tracking-[0.3em]">Quick Update Console</span>
                <h2 class="text-white text-2xl font-black uppercase mt-2 tracking-tighter">Search & Update Status</h2>
                <p class="text-gray-400 text-[10px] uppercase mt-2 tracking-widest leading-relaxed">Search by Tracking ID (NCC-...) to instantly update progress.</p>
            </div>

            <div class="space-y-4">
                <div class="relative">
                    <input type="text" id="reportPredictor" 
                        placeholder="ENTER TRACKING ID (e.g. NCC...)" 
                        class="w-full bg-white/10 border-none rounded-2xl px-6 py-4 text-white text-xs font-black placeholder:text-gray-600 focus:ring-2 focus:ring-[#FEDF0E] uppercase tracking-widest">
                    <div id="predictionList" class="absolute z-50 w-full bg-white mt-2 rounded-2xl shadow-2xl border border-gray-100 hidden overflow-hidden"></div>
                </div>

                <div id="quickUpdateForm" class="hidden animate-in fade-in slide-in-from-top-2">
                    <form action="{{ route('admin.reports.quickStatusUpdate') }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="report_id" id="quick_report_id">
                        <select name="status" required class="flex-1 bg-[#FEDF0E] border-none rounded-xl px-4 py-4 text-[10px] font-black uppercase tracking-widest text-[#716200]">
                            <option value="dispatched">Mark Dispatched</option>
                            <option value="in_progress">Work In Progress</option>
                            <option value="resolved">Issue Resolved</option>
                            <option value="rejected">Reject Report</option>
                        </select>
                        <button type="submit" class="bg-white text-gray-900 font-black px-6 rounded-xl text-[10px] uppercase hover:bg-gray-100 transition">Update</button>
                    </form>
                    <p id="targetDisplay" class="text-[#FEDF0E] text-[8px] font-black uppercase mt-2 ml-1 tracking-widest"></p>
                </div>
            </div>
        </div>
        <div class="absolute -right-20 -top-20 w-64 h-64 bg-[#00872E]/10 rounded-full blur-3xl"></div>
    </div>

    {{-- INCIDENTS TABLE --}}
    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-gray-50 flex justify-between items-center">
            <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest">Incoming Incidents</h3>
        </div>

        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                    <th class="px-8 py-5">Tracking ID</th>
                    <th class="px-8 py-5">Category & Title</th>
                    <th class="px-8 py-5">Assigned Dept</th>
                    <th class="px-8 py-5">Priority</th>
                    <th class="px-8 py-5">Status</th>
                    <th class="px-8 py-5 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="text-xs font-semibold text-gray-600">
                @forelse($reports as $report)
                <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition group">
                    <td class="px-8 py-5">
                        <a href="{{ route('admin.reports.show', $report->id) }}" 
                           class="font-black text-[#00872E] hover:text-[#006D24] transition-colors tracking-tighter hover:underline decoration-2 underline-offset-4 block">
                            {{ $report->tracking_number }}
                        </a>
                    </td>
                    
                    <td class="px-8 py-5">
                        <div class="flex flex-col">
                            <span class="text-gray-400 uppercase text-[9px] font-black tracking-tighter">{{ $report->category }}</span>
                            <span class="text-gray-800 font-bold uppercase truncate max-w-[200px]">{{ $report->title }}</span>
                        </div>
                    </td>

                    <td class="px-8 py-5 font-bold text-gray-700">
                        {{ $report->department->dept_name ?? 'NOT ASSIGNED' }}
                    </td>
                    <td class="px-8 py-5">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase 
                            {{ $report->priority == 'high' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $report->priority }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        @php
                            $statusColors = [
                                'pending' => 'bg-[#FEDF0E] text-[#716200]',
                                'dispatched' => 'bg-blue-100 text-blue-600',
                                'in_progress' => 'bg-purple-100 text-purple-600',
                                'resolved' => 'bg-[#00872E] text-white',
                                'rejected' => 'bg-red-100 text-red-600'
                            ];
                        @endphp
                        <span class="{{ $statusColors[$report->status] ?? 'bg-gray-100' }} px-3 py-1 rounded-full font-black text-[9px] uppercase">
                            {{ $report->status }}
                        </span>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <div class="flex justify-end gap-4">
                            <a href="{{ route('admin.reports.show', $report->id) }}" class="text-gray-400 hover:text-gray-900 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                            
                            {{-- LOGIC FIX: Check if status allows further updates --}}
                            @if($report->status == 'pending')
                                <button onclick="openDispatchModal('{{ $report->id }}', 'dispatch')" class="text-[#00872E] hover:underline font-black uppercase text-[10px] tracking-widest">
                                    Dispatch
                                </button>
                            @elseif($report->status != 'resolved' && $report->status != 'rejected')
                                <button onclick="openDispatchModal('{{ $report->id }}', 'update')" class="text-blue-600 hover:underline font-black uppercase text-[10px] tracking-widest">
                                    Update
                                </button>
                            @else
                                <span class="text-gray-300 font-black uppercase text-[10px] tracking-widest">Archived</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-[10px]">
                        No active incidents.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-6 bg-gray-50/30 border-t border-gray-50">
            {{ $reports->links() }}
        </div>
    </div>
</div>

{{-- MODAL SYSTEM --}}
<div id="dispatchModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm p-4">
    <div class="bg-white rounded-[2.5rem] p-10 w-full max-w-md shadow-2xl animate-in zoom-in-95 duration-200">
        <h3 id="modalTitle" class="font-black text-gray-900 text-sm uppercase tracking-widest mb-6">Dispatch Incident</h3>
        <form id="dispatchForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div id="deptSection">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Assign Department</label>
                    <select name="dept_id" id="deptSelect" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-4 text-xs font-bold focus:ring-2 focus:ring-[#00872E]">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->dept_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Workflow Status</label>
                    <select name="status" id="statusSelect" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-4 text-xs font-bold focus:ring-2 focus:ring-[#00872E]">
                        <option value="dispatched">Dispatched</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <div id="prioritySection">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Operational Priority</label>
                    <select name="priority" id="prioritySelect" class="w-full bg-gray-50 border-none rounded-2xl px-4 py-4 text-xs font-bold focus:ring-2 focus:ring-[#00872E]">
                        <option value="low">Routine</option>
                        <option value="medium">Urgent</option>
                        <option value="high">Critical</option>
                    </select>
                </div>

                <div class="pt-4 space-y-3">
                    <button type="submit" class="w-full bg-[#00872E] text-white font-black py-4 rounded-2xl hover:bg-[#006D24] transition uppercase text-xs tracking-widest shadow-lg">
                        Confirm Action
                    </button>
                    <button type="button" onclick="closeModal()" class="w-full text-gray-400 font-bold py-2 text-[10px] uppercase tracking-widest hover:text-gray-900 transition">
                        Dismiss
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // Predictive Search Logic
    const predictor = document.getElementById('reportPredictor');
    const list = document.getElementById('predictionList');
    const updateForm = document.getElementById('quickUpdateForm');

    predictor.addEventListener('input', function(e) {
        let query = e.target.value;
        if (query.length < 2) { list.classList.add('hidden'); return; }

        fetch("{{ route('admin.reports.search') }}?q=" + query)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                if (data.length > 0) {
                    list.classList.remove('hidden');
                    data.forEach(item => {
                        let div = document.createElement('div');
                        div.className = "p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 flex justify-between items-center group";
                        div.innerHTML = `
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black uppercase text-[#00872E] group-hover:underline">${item.tracking_number}</span>
                                <span class="text-[8px] font-bold text-gray-400 uppercase">${item.title}</span>
                            </div>
                            <span class="text-[9px] font-black text-gray-300 uppercase">${item.category}</span>
                        `;
                        div.onclick = () => {
                            document.getElementById('quick_report_id').value = item.id;
                            document.getElementById('targetDisplay').innerText = `Targeting: ${item.tracking_number}`;
                            predictor.value = item.tracking_number;
                            list.classList.add('hidden');
                            updateForm.classList.remove('hidden');
                        };
                        list.appendChild(div);
                    });
                }
            });
    });

    // Modal Controls
    function openDispatchModal(reportId, mode) {
        const modal = document.getElementById('dispatchModal');
        const form = document.getElementById('dispatchForm');
        const title = document.getElementById('modalTitle');
        const deptSection = document.getElementById('deptSection');
        const prioritySection = document.getElementById('prioritySection');
        const statusSelect = document.getElementById('statusSelect');
        const deptSelect = document.getElementById('deptSelect');
        const prioritySelect = document.getElementById('prioritySelect');

        form.action = `/admin/reports/${reportId}`;
        
        if(mode === 'dispatch') {
            title.innerText = "Dispatch Incident";
            statusSelect.value = "dispatched";
            deptSection.style.display = "block";
            prioritySection.style.display = "block";
            deptSelect.disabled = false;
            prioritySelect.disabled = false;
        } else {
            title.innerText = "Update Progress";
            deptSection.style.display = "none";
            prioritySection.style.display = "none";
            deptSelect.disabled = true;
            prioritySelect.disabled = true;
        }
        modal.classList.remove('hidden');
    }

    function closeModal() { document.getElementById('dispatchModal').classList.add('hidden'); }

    window.onclick = function(event) {
        const modal = document.getElementById('dispatchModal');
        if (event.target == modal) { closeModal(); }
    }
</script>
@endsection