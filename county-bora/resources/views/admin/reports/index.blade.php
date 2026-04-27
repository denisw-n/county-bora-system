<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports | County Bora Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .sidebar-item-active { background-color: #FEDF0E; color: #716200; font-weight: 900; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    </style>
</head>
<body class="bg-[#F5F5F5] antialiased">
    <div class="flex min-h-screen">
        
        <aside class="w-[280px] bg-[#00872E] text-white flex flex-col fixed h-full shadow-2xl z-30">
            <div class="p-6 flex items-center gap-3 border-b border-white/10">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Nairobi County Logo" class="w-full h-full object-contain" onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Nairobi_County_Coat_of_Arms.png/600px-Nairobi_County_Coat_of_Arms.png';">
                </div>
                <div>
                    <h1 class="font-black leading-tight text-sm tracking-tight uppercase">Nairobi County</h1>
                    <p class="text-[9px] font-bold opacity-50 tracking-widest uppercase">Admin Monolith</p>
                </div>
            </div>

            <nav class="flex-grow overflow-y-auto custom-scrollbar p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">⊞</span> Dashboard
                </a>
                
                <div class="pt-6 pb-2 px-4 text-[10px] font-black opacity-40 uppercase tracking-[0.2em]">Operations</div>
                
                <a href="{{ route('admin.reports.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.reports.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>⚠</span> Reports
                </a>

                <a href="{{ route('admin.wards.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.wards.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>🏢</span> Wards
                </a>

                <a href="{{ route('admin.departments.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.departments.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>📁</span> Departments
                </a>

                <a href="{{ route('admin.spatial.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.spatial.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>🗺</span> Spatial Awareness
                </a>

                <div class="pt-6 pb-2 px-4 text-[10px] font-black opacity-40 uppercase tracking-[0.2em]">Management</div>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-white/70 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold transition">
                    <span>📢</span> Public Communication
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-white/70 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold transition">
                    <span>🛡</span> User Verification
                </a>
                
                <a href="{{ route('admin.logs.index') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.logs.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>📋</span> System Audit
                </a>
            </nav>

            <div class="p-4 bg-[#007A29] border-t border-white/5">
                <div class="bg-[#FEDF0E] p-3 rounded-xl text-[#716200] text-[10px] font-black text-center shadow-lg mb-4 cursor-pointer">★ EMERGENCY HOTLINES</div>
                <div class="space-y-1 px-2">
                    <a href="#" class="block text-[11px] text-white/50 hover:text-white transition">⚙ Settings</a>
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">@csrf<button class="text-[11px] text-red-300 hover:text-red-100 font-bold">➔ Logout</button></form>
                </div>
            </div>
        </aside>

        <main class="flex-grow ml-[280px]">
            <header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
                <div class="flex items-center gap-8">
                    <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">Infrastructure Reports</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-lg bg-[#00872E] flex items-center justify-center text-white font-black text-xs shadow-sm uppercase">
                        {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
                    </div>
                </div>
            </header>

            <div class="p-8 max-w-[1400px] mx-auto">
                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest">Active Incident Queue</h3>
                    </div>

                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                <th class="px-8 py-5">Report ID</th>
                                <th class="px-8 py-5">Issue</th>
                                <th class="px-8 py-5">Reporter</th>
                                <th class="px-8 py-5">Status</th>
                                <th class="px-8 py-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs font-semibold text-gray-600">
                            @foreach($reports as $report)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition">
                                <td class="px-8 py-5 font-black text-gray-400">#{{ strtoupper(substr($report->id, 0, 8)) }}</td>
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-800">{{ $report->title }}</div>
                                    <div class="text-[10px] opacity-60">{{ $report->location }}</div>
                                </td>
                                <td class="px-8 py-5 font-bold">{{ $report->user->name ?? 'System Admin' }}</td>
                                <td class="px-8 py-5">
                                    <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase {{ $report->status == 'pending' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600' }}">
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <button onclick="openReviewModal('{{ $report->id }}')" class="bg-[#00872E] text-white text-[9px] font-black px-4 py-2 rounded-lg hover:bg-[#006D24] transition uppercase tracking-widest shadow-sm">
                                        Review & Assign
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <div class="p-6">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="reviewModal" class="fixed inset-0 bg-black/60 hidden z-50 flex items-center justify-center backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-[2.5rem] p-8 shadow-2xl scale-95 transition-all">
            <h3 class="font-black text-gray-800 mb-6 uppercase tracking-widest text-sm">Assign Incident Task</h3>
            <form id="updateForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Target Department</label>
                        <select name="dept_id" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00872E]">
                            <option value="1">Environment & Sanitation</option>
                            <option value="2">Roads & Public Works</option>
                            <option value="3">City Planning</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Priority Level</label>
                        <select name="priority" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00872E]">
                            <option value="low">Routine</option>
                            <option value="medium">Urgent</option>
                            <option value="high">Critical / Emergency</option>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2">Audit Remarks</label>
                        <textarea name="audit_remarks" rows="3" class="w-full bg-gray-50 border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00872E]" placeholder="Enter instructions for the department..."></textarea>
                    </div>
                    <input type="hidden" name="status" value="assigned">
                </div>
                <div class="mt-8 flex gap-3">
                    <button type="submit" class="flex-1 bg-[#00872E] text-white font-black py-4 rounded-2xl shadow-lg hover:bg-[#006D24] transition uppercase text-xs tracking-widest">Update & Dispatch</button>
                    <button type="button" onclick="closeModal()" class="px-6 py-4 text-gray-400 font-bold text-xs uppercase tracking-widest">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReviewModal(id) {
            const modal = document.getElementById('reviewModal');
            document.getElementById('updateForm').action = `/admin/reports/${id}`;
            modal.classList.remove('hidden');
        }
        function closeModal() {
            document.getElementById('reviewModal').classList.add('hidden');
        }
    </script>
</body>
</html>