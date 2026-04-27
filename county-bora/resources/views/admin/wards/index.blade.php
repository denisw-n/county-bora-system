<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wards | County Bora Admin</title>
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
                    <img src="{{ asset('images/logo.png') }}" 
                         alt="Nairobi County Logo" 
                         class="w-full h-full object-contain"
                         onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Nairobi_County_Coat_of_Arms.png/600px-Nairobi_County_Coat_of_Arms.png';">
                </div>
                <div>
                    <h1 class="font-black leading-tight text-sm tracking-tight uppercase">Nairobi County</h1>
                    <p class="text-[9px] font-bold opacity-50 tracking-widest uppercase">Admin Monolith</p>
                </div>
            </div>

            <nav class="flex-grow overflow-y-auto custom-scrollbar p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">⊞</span> Dashboard
                </a>
                
                <div class="pt-6 pb-2 px-4 text-[10px] font-black opacity-40 uppercase tracking-[0.2em]">Operations</div>
                
                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.reports.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>⚠</span> Reports
                </a>

                <a href="{{ route('admin.wards.index') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.wards.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>🏢</span> Wards
                </a>

                <a href="{{ route('admin.departments.index') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.departments.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>📁</span> Departments
                </a>

                <a href="{{ route('admin.spatial.index') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.spatial.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
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
                <div class="bg-[#FEDF0E] p-3 rounded-xl text-[#716200] text-[10px] font-black text-center shadow-lg mb-4 cursor-pointer">
                    ★ EMERGENCY HOTLINES
                </div>
                <div class="space-y-1 px-2">
                    <a href="#" class="block text-[11px] text-white/50 hover:text-white transition">⚙ Settings</a>
                    <form method="POST" action="{{ route('logout') }}" class="pt-2">
                        @csrf
                        <button class="text-[11px] text-red-300 hover:text-red-100 font-bold">➔ Logout</button>
                    </form>
                </div>
            </div>
        </aside>

        <main class="flex-grow ml-[280px]">
            <header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
                <div class="flex items-center gap-8">
                    <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">Ward Management</span>
                    <nav class="flex gap-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                        <a href="{{ route('admin.dashboard') }}" class="py-5 hover:text-gray-900 transition">Global View</a>
                        <a href="{{ route('admin.wards.index') }}" class="text-[#00872E] border-b-2 border-[#00872E] py-5">Ward Registry</a>
                    </nav>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-lg bg-[#00872E] flex items-center justify-center text-white font-black text-xs shadow-sm uppercase">
                        {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
                    </div>
                </div>
            </header>

            <div class="p-8 max-w-[1400px] mx-auto space-y-6">
                
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-xl text-xs font-bold uppercase tracking-widest mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
                    <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest mb-6">Register New Ward</h3>
                    <form action="{{ route('admin.wards.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @csrf
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block tracking-widest">Ward Name</label>
                            <input type="text" name="name" required class="w-full bg-[#F3F4F6] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00872E]" placeholder="e.g. Roysambu">
                        </div>
                        <div>
                            <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block tracking-widest">Sub-County</label>
                            <input type="text" name="sub_county" required class="w-full bg-[#F3F4F6] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00872E]" placeholder="e.g. Kasarani">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-[#00872E] text-white font-black py-3 rounded-xl hover:bg-[#006D24] transition uppercase text-[10px] tracking-widest shadow-lg">
                                Add Ward to System
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-8 border-b border-gray-50">
                        <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest">Active Ward Directory</h3>
                    </div>
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                                <th class="px-8 py-5">Ward Name</th>
                                <th class="px-8 py-5">Sub-County</th>
                                <th class="px-8 py-5 text-center">Active Incidents</th>
                                <th class="px-8 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs font-semibold text-gray-600">
                            @foreach($wards as $ward)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition">
                                <td class="px-8 py-5 font-bold text-gray-800 uppercase tracking-tight">{{ $ward->name }}</td>
                                <td class="px-8 py-5 text-gray-400 font-bold uppercase">{{ $ward->sub_county }}</td>
                                <td class="px-8 py-5 text-center">
                                    <span class="bg-[#FEDF0E] text-[#716200] px-3 py-1 rounded-full font-black text-[10px] shadow-sm">
                                        {{ $ward->reports_count ?? 0 }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right flex justify-end gap-4">
                                    <button onclick="editWard('{{ $ward->id }}', '{{ $ward->name }}', '{{ $ward->sub_county }}')" 
                                            class="text-[#00872E] hover:text-green-800 font-black uppercase text-[10px] tracking-widest">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.wards.destroy', $ward->id) }}" method="POST" onsubmit="return confirm('Archive this ward?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-400 hover:text-red-600 font-black uppercase text-[10px] tracking-widest">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-6 bg-gray-50/30">
                        {{ $wards->links() }}
                    </div>
                </div>
            </div>
        </main>
    </div>

    <form id="update-form" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="name" id="edit-name">
        <input type="hidden" name="sub_county" id="edit-sub_county">
    </form>

    <script>
        function editWard(id, currentName, currentSubCounty) {
            const newName = prompt("Update Ward Name:", currentName);
            const newSubCounty = prompt("Update Sub-County:", currentSubCounty);

            if (newName && newSubCounty) {
                const form = document.getElementById('update-form');
                form.action = `/admin/wards/${id}`;
                document.getElementById('edit-name').value = newName;
                document.getElementById('edit-sub_county').value = newSubCounty;
                form.submit();
            }
        }
    </script>
</body>
</html>