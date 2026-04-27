@extends('layouts.admin')

@section('title', 'Spatial Awareness Engine')

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #incidentMap { height: calc(100vh - 64px); width: 100%; z-index: 10; }
        .leaflet-popup-content-wrapper { border-radius: 1.5rem; padding: 8px; border: none; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); }
        .leaflet-popup-tip { display: none; }
    </style>
@endpush

@section('content')
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
        <div class="absolute bottom-10 right-10 z-[1000] bg-white/95 backdrop-blur shadow-2xl rounded-[2.5rem] p-6 w-72 border border-gray-100 animate-fade-in-up">
            <h4 class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-5">Incident Resolution States</h4>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-3 text-xs font-bold text-gray-700">
                        <span class="w-3 h-3 rounded-full bg-[#EF4444] shadow-sm"></span> Pending
                    </span>
                    <span class="text-xs font-black bg-red-50 text-red-600 px-2 py-0.5 rounded-lg">{{ $stats['pending'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-3 text-xs font-bold text-gray-700">
                        <span class="w-3 h-3 rounded-full bg-[#F59E0B] shadow-sm"></span> In Progress
                    </span>
                    <span class="text-xs font-black bg-yellow-50 text-yellow-600 px-2 py-0.5 rounded-lg">{{ $stats['progress'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="flex items-center gap-3 text-xs font-bold text-gray-700">
                        <span class="w-3 h-3 rounded-full bg-[#10B981] shadow-sm"></span> Resolved
                    </span>
                    <span class="text-xs font-black bg-green-50 text-green-600 px-2 py-0.5 rounded-lg">{{ $stats['resolved'] ?? 0 }}</span>
                </div>
            </div>
            <div class="mt-5 pt-5 border-t border-gray-100 flex justify-between items-center">
                <span class="text-[10px] font-black uppercase text-gray-400">Total Surveillance</span>
                <span class="text-sm font-black text-[#00872E]">{{ $stats['total'] ?? 0 }}</span>
            </div>
        </div>

        <div id="incidentMap"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const map = L.map('incidentMap', { zoomControl: false }).setView([-1.286389, 36.817223], 13);
            
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap contributors &copy; CARTO'
            }).addTo(map);

            L.control.zoom({ position: 'topright' }).addTo(map);

            const reports = @json($reports);

            reports.forEach(report => {
                let pinColor = '#EF4444'; // Pending
                if(report.status === 'assigned' || report.status === 'In Progress') pinColor = '#F59E0B';
                if(report.status === 'resolved' || report.status === 'Resolved') pinColor = '#10B981';

                const marker = L.circleMarker([report.latitude, report.longitude], {
                    radius: 10,
                    fillColor: pinColor,
                    color: "#fff",
                    weight: 3,
                    opacity: 1,
                    fillOpacity: 1
                }).addTo(map);

                marker.bindPopup(`
                    <div class="p-2 min-w-[200px] font-sans">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-gray-100 text-gray-500">${report.priority ?? 'Normal'}</span>
                            <span class="text-[9px] font-black uppercase text-[#00872E]">${report.status}</span>
                        </div>
                        <h3 class="font-black text-sm text-gray-800 mb-1">${report.title ?? 'Incident Report'}</h3>
                        <p class="text-[11px] text-gray-500 leading-relaxed mb-3">${report.description ?? 'No details provided.'}</p>
                        <a href="/admin/reports/${report.id}" class="block text-center bg-[#00872E] text-white text-[10px] font-black py-2 rounded-xl uppercase tracking-widest hover:bg-[#007026] transition shadow-md">Review Details</a>
                    </div>
                `);
            });
        });
    </script>
@endsection