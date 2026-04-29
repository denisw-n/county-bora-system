@extends('layouts.admin')

@section('title', 'Emergency Hotlines')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Emergency hotlines</h2>
            <p class="text-sm text-gray-500 font-medium text-[11px] uppercase tracking-wider">Manage critical county emergency contact services.</p>
        </div>
        {{-- Fixed Button: Matches your red emergency theme --}}
        <button onclick="toggleModal('addHotlineModal')" 
                class="bg-[#991B1B] text-white px-6 py-3 rounded-2xl font-black text-[11px] uppercase shadow-lg hover:bg-red-800 transition-all active:scale-95 flex items-center gap-2">
            <span class="text-lg leading-none">+</span> Add New Service
        </button>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Service Name</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Phone Number</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($hotlines as $hotline)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-8 py-5 font-bold text-gray-800 uppercase text-xs">{{ $hotline->service_name }}</td>
                    <td class="px-8 py-5">
                        <span class="bg-gray-100 px-4 py-2 rounded-xl font-mono text-gray-600 font-black text-xs tracking-tighter border border-gray-200">
                            {{ $hotline->phone_number }}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <form action="{{ route('admin.hotlines.update', $hotline->id) }}" method="POST" class="flex justify-center">
                            @csrf @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $hotline->is_active ? 0 : 1 }}">
                            <button type="submit" class="flex flex-col items-center gap-1 group">
                                <div class="w-10 h-5 rounded-full relative transition-colors {{ $hotline->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                    <div class="absolute top-1 {{ $hotline->is_active ? 'right-1' : 'left-1' }} w-3 h-3 bg-white rounded-full transition-all shadow-sm"></div>
                                </div>
                                <span class="text-[9px] font-black uppercase tracking-tighter {{ $hotline->is_active ? 'text-green-600' : 'text-gray-400 group-hover:text-gray-600' }}">
                                    {{ $hotline->is_active ? 'Online' : 'Offline' }}
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="px-8 py-5 text-right space-x-6">
                        <button onclick="editHotline({{ json_encode($hotline) }})" class="text-blue-600 hover:text-blue-800 font-black text-[10px] uppercase tracking-widest">Edit</button>
                        
                        <form action="{{ route('admin.hotlines.destroy', $hotline->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Remove this service?')" class="text-gray-300 hover:text-red-600 font-black text-[10px] uppercase tracking-widest transition-colors">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <p class="text-gray-400 font-black text-[11px] uppercase tracking-[0.2em]">Zero active services found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL 1: ADD NEW HOTLINE (The one that was missing) --}}
<div id="addHotlineModal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="toggleModal('addHotlineModal')"></div>
        <div class="bg-white rounded-[2.5rem] shadow-2xl z-50 w-full max-w-md overflow-hidden relative animate-in zoom-in-95 duration-200">
            <div class="bg-[#991B1B] p-8 text-white">
                <h3 class="font-black uppercase text-sm tracking-widest">Register New Service</h3>
                <p class="text-red-200 text-[10px] uppercase font-bold mt-1">Add to county emergency directory</p>
            </div>
            <form action="{{ route('admin.hotlines.store') }}" method="POST" class="p-8 space-y-6">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2 ml-1">Service Name</label>
                    <input type="text" name="service_name" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-red-500 outline-none placeholder-gray-300" placeholder="e.g. AMBULANCE DISPATCH" required>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest block mb-2 ml-1">Phone Number</label>
                    <input type="text" name="phone_number" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-xs font-mono font-bold focus:ring-2 focus:ring-red-500 outline-none placeholder-gray-300" placeholder="e.g. 911" required>
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="toggleModal('addHotlineModal')" class="flex-1 font-black text-[11px] uppercase text-gray-400 hover:text-gray-900 transition">Cancel</button>
                    <button type="submit" class="flex-1 bg-gray-900 text-white px-6 py-4 rounded-2xl font-black text-[11px] uppercase shadow-lg hover:bg-black transition">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: EDIT HOTLINE --}}
<div id="editHotlineModal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="toggleModal('editHotlineModal')"></div>
        <div class="bg-white rounded-[2.5rem] shadow-2xl z-50 w-full max-w-md overflow-hidden relative">
            <div class="bg-[#00872E] p-8 text-white font-black uppercase text-sm tracking-widest">Update Service</div>
            <form id="editForm" method="POST" class="p-8 space-y-6">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 ml-1">Service Name</label>
                    <input type="text" id="edit_name" name="service_name" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-xs font-bold focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2 ml-1">Phone Number</label>
                    <input type="text" id="edit_phone" name="phone_number" class="w-full bg-gray-50 border-none rounded-2xl px-5 py-4 text-xs font-mono font-bold focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="toggleModal('editHotlineModal')" class="flex-1 font-black text-[11px] uppercase text-gray-400 hover:text-gray-900">Cancel</button>
                    <button type="submit" class="flex-1 bg-[#00872E] text-white px-6 py-4 rounded-2xl font-black text-[11px] uppercase shadow-lg hover:bg-green-800 transition">Update Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        const modal = document.getElementById(id);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    function editHotline(hotline) {
        const form = document.getElementById('editForm');
        form.action = `/admin/hotlines/${hotline.id}`;
        
        document.getElementById('edit_name').value = hotline.service_name;
        document.getElementById('edit_phone').value = hotline.phone_number;
        
        toggleModal('editHotlineModal');
    }
</script>
@endsection