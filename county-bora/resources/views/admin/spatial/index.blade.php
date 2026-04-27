<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>County Bora | Spatial Awareness</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: 'Public Sans', sans-serif; }
        .sidebar-item-active { background-color: #FEDF0E; color: #716200; font-weight: 900; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        #incidentMap { height: calc(100vh - 64px); width: 100%; z-index: 10; }
        .leaflet-popup-content-wrapper { border-radius: 1.5rem; padding: 8px; }
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
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition font-semibold text-white/70 hover:bg-white/10">
                    <span class="text-lg">⊞</span> Dashboard
                </a>
                
                <div class="pt-6 pb-2 px-4 text-[10px] font-black opacity-40 uppercase tracking-[0.2em]">Operations</div>
                
                <a href="{{ route('admin.reports.index') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.reports.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold hover:text-white' }}">
                    <span>⚠</span> Reports
                </a>

                <a href="{{ route('admin.wards.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-white/70 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold transition">
                    <span>🏢</span> Wards
                </a>
                <a href="{{ route('admin.departments.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-white/70 hover:bg-white/10 hover:text-white rounded-xl text-xs font-semibold transition">
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
                    <a href="#" class="block text-[11px] text-white/50 hover:text-white transition">❓ Support</a>
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
                    <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">Live Incident Map</span>
                    <nav class="flex gap-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                        <a href="{{ route('admin.dashboard') }}" class="py-5 hover:text-gray-900 transition">Dashboard</a>
                        <a href="{{ route('admin.spatial.index') }}" class="text-[#00872E] border-b-2 border-[#00872E] py-5">Spatial Engine</a>
                        <a href="{{ route('admin.reports.index') }}" class="py-5 hover:text-gray-900 transition">Reports</a>
                    </nav>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <input type="text" placeholder="Search GPS points..." class="bg-[#F3F4F6] border-none rounded-xl pl-4 pr-10 py-2 text-[11px] w-64 focus:ring-2 focus:ring-[#00872E]">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">🔍</span>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-[#00872E] flex items-center justify-center text-white font-black text-xs shadow-sm uppercase">
                        {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
                    </div>
                </div>
            </header>

            <div class="relative">
                <div class="absolute bottom-10 right-10 z-[1000] bg-white/95 backdrop-blur shadow-2xl rounded-[2.5rem] p-6 w-72 border border-gray-100">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-5">Incident Resolution States</h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-3 text-xs font-bold text-gray-700">
                                <span class="w-3 h-3 rounded-full bg-[#EF4444] shadow-sm"></span> Pending
                            </span>
                            <span class="text-xs font-black bg-red-50 text-red-600 px-2 py-0.5 rounded-lg">{{ $stats['pending'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-3 text-xs font-bold text-gray-700">
                                <span class="w-3 h-3 rounded-full bg-[#F59E0B] shadow-sm"></span> In Progress
                            </span>
                            <span class="text-xs font-black bg-yellow-50 text-yellow-600 px-2 py-0.5 rounded-lg">{{ $stats['progress'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="flex items-center gap-3 text-xs font-bold text-gray-700">
                                <span class="w-3 h-3 rounded-full bg-[#10B981] shadow-sm"></span> Resolved
                            </span>
                            <span class="text-xs font-black bg-green-50 text-green-600 px-2 py-0.5 rounded-lg">{{ $stats['resolved'] }}</span>
                        </div>
                    </div>
                    <div class="mt-5 pt-5 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-[10px] font-black uppercase text-gray-400">Total Surveillance</span>
                        <span class="text-sm font-black text-[#00872E]">{{ $stats['total'] }}</span>
                    </div>
                </div>

                <div id="incidentMap"></div>
            </div>
        </main>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('incidentMap', { zoomControl: false }).setView([-1.286389, 36.817223], 13);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
        L.control.zoom({ position: 'topright' }).addTo(map);

        const reports = @json($reports);

        reports.forEach(report => {
            let pinColor = '#EF4444'; // Pending
            if(report.status === 'In Progress') pinColor = '#F59E0B';
            if(report.status === 'Resolved') pinColor = '#10B981';

            const marker = L.circleMarker([report.latitude, report.longitude], {
                radius: 10,
                fillColor: pinColor,
                color: "#fff",
                weight: 3,
                opacity: 1,
                fillOpacity: 1
            }).addTo(map);

            marker.bindPopup(`
                <div class="p-2 min-w-[200px]">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-gray-100 text-gray-500">${report.priority}</span>
                        <span class="text-[9px] font-black uppercase text-[#00872E]">${report.status}</span>
                    </div>
                    <h3 class="font-black text-sm text-gray-800 mb-1">${report.category}</h3>
                    <p class="text-[11px] text-gray-500 leading-relaxed mb-3">${report.description}</p>
                    <a href="/admin/reports/${report.id}" class="block text-center bg-[#00872E] text-white text-[10px] font-black py-2 rounded-xl uppercase tracking-widest hover:bg-[#007026] transition">View Details</a>
                </div>
            `);
        });
    </script>
</body>
</html>