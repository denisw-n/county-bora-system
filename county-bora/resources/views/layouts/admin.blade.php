<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>County Bora | @yield('title', 'Admin Portal')</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Public Sans', sans-serif; transition: all 0.3s ease; }
        .sidebar-item-active { background-color: #FEDF0E; color: #716200; font-weight: 900; }
        .emergency-active { background-color: #7F1D1D; border: 2px solid #FEDF0E; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
        
        /* Retraction Styles */
        .sidebar-collapsed { width: 80px !important; }
        .sidebar-collapsed .nav-text, 
        .sidebar-collapsed .sidebar-header-text,
        .sidebar-collapsed .nav-section-title { display: none; }
        .sidebar-collapsed .nav-item { justify-content: center; padding-left: 0; padding-right: 0; }
        .content-expanded { margin-left: 80px !important; }
        
        aside, main { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
</head>
<body class="bg-[#F5F5F5] antialiased">
    <div class="flex min-h-screen">
        {{-- ASIDE --}}
        <aside id="sidebar" class="w-[280px] bg-[#00872E] text-white flex flex-col fixed h-full shadow-2xl z-30">
            
            {{-- HEADER / REFRESH TRIGGER --}}
            <div class="p-6 flex items-center gap-3 border-b border-white/10 relative">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg p-1.5 overflow-hidden transition-transform group-active:scale-95">
                        <img src="{{ asset('images/logo.png') }}" 
                             alt="Logo" 
                             class="w-full h-full object-contain"
                             onerror="this.onerror=null; this.src='https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Nairobi_County_Coat_of_Arms.png/600px-Nairobi_County_Coat_of_Arms.png';">
                    </div>
                    <div class="sidebar-header-text">
                        <h1 class="font-black leading-tight text-sm tracking-tight uppercase">Nairobi City County</h1>
                        <p class="text-[9px] font-bold opacity-50 tracking-widest uppercase">Admin Monolith</p>
                    </div>
                </a>

                {{-- Retract Toggle Button --}}
                <button onclick="toggleSidebar()" class="absolute -right-3 top-10 bg-[#FEDF0E] text-[#716200] w-6 h-6 rounded-full flex items-center justify-center shadow-md hover:scale-110 transition-all">
                    <span id="toggle-icon" class="text-[10px] font-black">◀</span>
                </button>
            </div>

            <nav class="flex-grow overflow-y-auto custom-scrollbar p-4 space-y-1">
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-item flex items-center gap-3 px-4 py-3 rounded-xl text-xs transition {{ request()->routeIs('admin.dashboard') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">⊞</span> <span class="nav-text">Dashboard</span>
                </a>
                
                <div class="nav-section-title pt-6 pb-2 px-4 text-[10px] font-black opacity-40 uppercase tracking-[0.2em]">Operations</div>
                
                <a href="{{ route('admin.reports.index') }}" 
                   class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.reports.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">⚠</span> <span class="nav-text">Reports</span>
                </a>

                <a href="{{ route('admin.wards.index') }}" 
                   class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.wards.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">🏢</span> <span class="nav-text">Wards</span>
                </a>

                <a href="{{ route('admin.departments.index') }}" 
                   class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.departments.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">📁</span> <span class="nav-text">Departments</span>
                </a>

                <a href="{{ route('admin.spatial.index') }}" 
                   class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.spatial.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">🗺</span> <span class="nav-text">Spatial Awareness</span>
                </a>

                <div class="nav-section-title pt-6 pb-2 px-4 text-[10px] font-black opacity-40 uppercase tracking-[0.2em]">Management</div>
                
                <a href="{{ route('admin.communication.index') }}" 
                   class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.communication.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">📢</span> <span class="nav-text">Public Communication</span>
                </a>

                <a href="{{ route('admin.users.verification') }}" 
                   class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.users.verification') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">🛡</span> <span class="nav-text">User verification</span>
                </a>

                <a href="{{ route('admin.logs.index') }}" 
                   class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs transition {{ request()->routeIs('admin.logs.*') ? 'sidebar-item-active shadow-md' : 'text-white/70 hover:bg-white/10 font-semibold' }}">
                    <span class="text-lg">📋</span> <span class="nav-text">System audit</span>
                </a>
            </nav>

            <div class="p-4 bg-[#007A29] border-t border-white/5">
                <a href="{{ route('admin.hotlines.index') }}" 
                   class="p-3 rounded-xl text-white text-[10px] font-black text-center shadow-lg mb-4 flex items-center justify-center gap-2 transition-all active:scale-95 {{ request()->routeIs('admin.hotlines.*') ? 'emergency-active' : 'bg-[#991B1B] hover:bg-[#7F1D1D]' }}">
                    <span class="text-xs">★</span> <span class="nav-text uppercase tracking-widest">Hotlines</span>
                </a>

                <div class="space-y-1 px-2 nav-text">
                    <a href="#" class="text-[11px] text-white/60 hover:text-white font-black flex items-center gap-2 transition mb-2">
                        <span>⚙</span> System settings
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="pt-1">
                        @csrf
                        <button type="submit" class="text-[11px] text-red-300 hover:text-red-100 font-black flex items-center gap-2">
                            <span>➔</span> Logout
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <main id="main-content" class="flex-grow ml-[280px]">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('main-content');
            const icon = document.getElementById('toggle-icon');
            
            sidebar.classList.toggle('sidebar-collapsed');
            main.classList.toggle('content-expanded');
            
            // Toggle icon direction
            if (sidebar.classList.contains('sidebar-collapsed')) {
                icon.innerText = '▶';
            } else {
                icon.innerText = '◀';
            }
        }
    </script>
    @stack('scripts')
</body>
</html>