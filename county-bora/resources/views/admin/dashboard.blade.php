@extends('layouts.admin')

@section('title', 'County Bora | Admin Dashboard')

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        #dashboardMap { height: 450px; width: 100%; border-radius: 1.5rem; z-index: 10; }
        .custom-pin-dashboard { background: none; border: none; cursor: pointer; }
        .leaflet-popup-content-wrapper { border-radius: 1rem; padding: 4px; }
    </style>
@endpush

@section('content')
    <header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
        <div class="flex items-center gap-8">
            <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">Nairobi County Admin</span>
            <nav class="flex gap-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                <a href="{{ route('admin.dashboard') }}" class="text-[#00872E] border-b-2 border-[#00872E] py-5">Global View</a>
                <a href="{{ route('admin.reports.index') }}" class="py-5 hover:text-gray-900 transition">Analytics</a>
            </nav>
        </div>
        <div class="flex items-center gap-4">
            <div class="w-8 h-8 rounded-lg bg-[#00872E] flex items-center justify-center text-white font-black text-xs shadow-sm uppercase">
                {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
            </div>
        </div>
    </header>

    <div class="p-8 max-w-[1400px] mx-auto space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-[#006D24] p-6 rounded-[2rem] text-white shadow-xl h-40 flex flex-col justify-center transition hover:scale-[1.02] duration-300">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Active Reports</span>
                <h2 class="text-4xl font-black mt-1">{{ count($reports ?? []) }}</h2>
            </div>
            <div class="bg-[#FEDF0E] p-6 rounded-[2rem] text-[#716200] shadow-xl h-40 flex flex-col justify-center transition hover:scale-[1.02] duration-300">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60">Resolution Rate</span>
                <h2 class="text-4xl font-black mt-1">84.2%</h2>
            </div>
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm h-40 flex flex-col justify-center transition hover:scale-[1.02] duration-300">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">System Health</span>
                <h2 class="text-4xl font-black mt-1 text-gray-800">99.9%</h2>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            <div class="col-span-12 lg:col-span-8 bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-8">
                    <h3 class="font-black text-gray-800 text-sm tracking-tight uppercase">Spatial Awareness Overview</h3>
                    <div class="flex gap-2">
                        <span class="px-3 py-1 bg-green-50 text-green-600 text-[9px] font-black rounded-full border border-green-100 uppercase">Interactive Preview</span>
                    </div>
                </div>
                <div class="bg-[#F9FAF9] rounded-3xl overflow-hidden border border-gray-50">
                    <div id="dashboardMap"></div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-4 bg-[#004D1A] p-8 rounded-[2.5rem] text-white shadow-2xl flex flex-col h-full">
                <h3 class="font-black text-xs mb-8 flex justify-between items-center tracking-widest uppercase">
                    <span>Live System Audit</span>
                    <span class="bg-red-500 text-[8px] px-2 py-0.5 rounded font-black animate-pulse">LIVE</span>
                </h3>
                <div class="space-y-4 flex-grow">
                    <div class="bg-white/10 p-5 rounded-2xl border-l-4 border-[#FEDF0E]">
                        <p class="text-[11px] font-black uppercase tracking-tight">System Status</p>
                        <p class="text-[10px] text-white/50 leading-relaxed mt-1">
                            Admin Monolith active. Mapping {{ count($reports ?? []) }} coordinates across Nairobi.
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.reports.index') }}" class="mt-8 block text-center bg-[#FEDF0E] text-[#716200] text-[10px] font-black py-3 rounded-xl uppercase tracking-widest hover:opacity-90 transition">
                    View Full Audit Trail
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('dashboardMap', { zoomControl: false, attributionControl: false }).setView([-1.286389, 36.817223], 12);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { subdomains: 'abcd', maxZoom: 20 }).addTo(map);

            const reports = @json($reports ?? []);
            
            reports.forEach(report => {
                if (report.latitude && report.longitude) {
                    let color = '#EF4444'; 
                    const statusLower = (report.status || '').toLowerCase();

                    if(['assigned', 'in progress', 'in_progress', 'dispatched'].includes(statusLower)) {
                        color = '#F59E0B';
                    } else if(statusLower === 'resolved') {
                        color = '#10B981';
                    }

                    const dotIcon = L.divIcon({
                        className: 'custom-pin-dashboard',
                        html: `<div style="background-color: ${color}; width: 12px; height: 12px; border: 2px solid white; border-radius: 50%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>`,
                        iconSize: [12, 12],
                        iconAnchor: [6, 6]
                    });

                    const marker = L.marker([report.latitude, report.longitude], { icon: dotIcon }).addTo(map);

                    marker.bindPopup(`
                        <div class="p-1 font-sans text-center">
                            <h4 class="text-xs font-bold text-gray-800">${report.title ?? 'Incident'}</h4>
                            <p class="text-[9px] font-black uppercase" style="color: ${color}">${report.status}</p>
                        </div>
                    `, { closeButton: false });

                    marker.on('mouseover', function() { this.openPopup(); });
                    marker.on('mouseout', function() { this.closePopup(); });
                    marker.on('click', function() { window.location.href = `/admin/reports/${report.id}`; });
                }
            });
        });
    </script>
@endsection