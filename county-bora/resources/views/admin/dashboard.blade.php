<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>County Bora | Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Public Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAF5]">
    <div class="flex min-h-screen">
        
        <aside class="fixed left-0 top-0 h-full w-[256px] bg-[#166534] shadow-xl z-20 flex flex-col">
            <div class="p-6 text-white font-black text-xl border-b border-white/10">
                COUNTY BORA
            </div>
            <nav class="flex-grow p-4 space-y-2">
                <a href="#" class="block px-4 py-2 bg-[#FEDF0E] text-[#716200] rounded-lg font-bold">Dashboard</a>
                <a href="#" class="block px-4 py-2 text-white/70 hover:bg-white/10 rounded-lg">Reports</a>
                <a href="#" class="block px-4 py-2 text-white/70 hover:bg-white/10 rounded-lg">Users</a>
            </nav>
            <div class="p-4 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-4 py-2 text-red-200 hover:text-white">Logout</button>
                </form>
            </div>
        </aside>

        <main class="flex-grow pl-[256px]">
            <header class="sticky top-0 h-16 bg-white/70 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-8 z-10">
                <h1 class="text-[#166534] font-black text-lg tracking-tight uppercase">Dashboard Overview</h1>
                <div class="flex items-center gap-4">
                    <div class="bg-[#E7E9E4] rounded-xl px-4 py-2 text-slate-400 text-xs italic">
                        Search infrastructure...
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-slate-300 border border-slate-200"></div>
                </div>
            </header>

            <div class="p-8 max-w-[1200px]">
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-[#006D24] p-6 rounded-lg relative overflow-hidden h-[143px] text-white">
                        <p class="text-[#BBF7D0] text-[12px] font-bold uppercase tracking-widest">Active Reports</p>
                        <h2 class="text-4xl font-black mt-2">154</h2>
                        <p class="text-[#86EFAC] text-[10px] mt-2">↑ 12% increase</p>
                    </div>
                    <div class="bg-[#FEDF0E] p-6 rounded-lg relative overflow-hidden h-[143px] text-[#716200]">
                        <p class="opacity-60 text-[12px] font-bold uppercase tracking-widest">Pending Review</p>
                        <h2 class="text-4xl font-black mt-2">42</h2>
                        <p class="opacity-60 text-[10px] mt-2">Requires Action</p>
                    </div>
                    <div class="bg-[#E1E3DE] p-6 rounded-lg relative overflow-hidden h-[143px]">
                        <p class="text-[#3E4A3D] text-[12px] font-bold uppercase tracking-widest">System Health</p>
                        <h2 class="text-4xl font-black mt-2">98%</h2>
                        <p class="text-[#15803D] text-[10px] mt-2">All systems live</p>
                    </div>
                </div>

                <div class="grid grid-cols-12 gap-6">
                    <div class="col-span-8 bg-white rounded-2xl shadow-sm border border-slate-100 h-[520px] flex flex-col overflow-hidden">
                        <div class="p-5 border-b border-slate-50 flex justify-between bg-white">
                            <h3 class="font-black text-[#191C1A]">Spatial Awareness</h3>
                            <div class="flex gap-2">
                                <span class="bg-[#FEE2E2] text-[#991B1B] text-[10px] font-bold px-3 py-1 rounded-full">Critical</span>
                                <span class="bg-[#DCFCE7] text-[#166534] text-[10px] font-bold px-3 py-1 rounded-full">Stable</span>
                            </div>
                        </div>
                        <div class="flex-grow bg-[#E2E8F0] relative">
                            <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
                            <div class="absolute top-1/3 left-1/2 w-4 h-4 bg-red-500 rounded-full border-2 border-white animate-pulse"></div>
                            <div class="absolute top-1/2 left-1/4 w-4 h-4 bg-yellow-500 rounded-full border-2 border-white"></div>
                        </div>
                    </div>

                    <div class="col-span-4 bg-[#006D24] rounded-2xl p-6 text-white h-[520px]">
                        <h3 class="font-bold flex items-center gap-2 mb-6">
                            <span class="text-[#FACC15]">★</span> Recent Activity
                        </h3>
                        <div class="space-y-4">
                            <div class="bg-white/5 border-l-4 border-[#FACC15] p-4 rounded-r">
                                <p class="text-xs font-bold">User Identity Verified</p>
                                <p class="text-[10px] text-white/50">Admin approved License #229-B</p>
                                <p class="text-[9px] text-[#FACC15] font-black mt-2">JUST NOW</p>
                            </div>
                            <div class="bg-white/5 p-4 rounded">
                                <p class="text-xs font-bold">New Drainage Report</p>
                                <p class="text-[10px] text-white/50">Nairobi CBD Area</p>
                                <p class="text-[9px] text-white/30 mt-2">15 MINS AGO</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>