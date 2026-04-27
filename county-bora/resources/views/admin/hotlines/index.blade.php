@extends('layouts.admin')

@section('title', 'Emergency Hotlines')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Emergency hotlines</h2>
            <p class="text-sm text-gray-500 font-medium">Manage and update critical county emergency contact services.</p>
        </div>
        <button onclick="toggleModal('addHotlineModal')" 
                class="bg-[#991B1B] text-white px-6 py-2.5 rounded-xl font-black text-[11px] uppercase shadow-lg hover:bg-red-800 transition-all active:scale-95 flex items-center gap-2">
            <span class="text-lg">+</span> Add New Service
        </button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">Service Name</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">Phone Number</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($hotlines as $hotline)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-bold text-gray-700">{{ $hotline->service_name }}</td>
                    <td class="px-6 py-4">
                        <span class="bg-gray-100 px-3 py-1 rounded-lg font-mono text-gray-600 font-bold">{{ $hotline->phone_number }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.hotlines.update', $hotline->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $hotline->is_active ? 0 : 1 }}">
                            <button type="submit" class="flex items-center gap-2">
                                <div class="w-10 h-5 rounded-full relative transition-colors {{ $hotline->is_active ? 'bg-green-500' : 'bg-gray-300' }}">
                                    <div class="absolute top-1 {{ $hotline->is_active ? 'right-1' : 'left-1' }} w-3 h-3 bg-white rounded-full transition-all"></div>
                                </div>
                                <span class="text-[10px] font-black uppercase {{ $hotline->is_active ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $hotline->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4 text-right space-x-4">
                        <button onclick="editHotline({{ json_encode($hotline) }})" class="text-blue-600 hover:text-blue-800 font-black text-[10px] uppercase">Edit</button>
                        
                        <form action="{{ route('admin.hotlines.destroy', $hotline->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Remove this service?')" class="text-gray-400 hover:text-red-600 font-black text-[10px] uppercase">Delete</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold text-[11px] uppercase">No services found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="editHotlineModal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="toggleModal('editHotlineModal')"></div>
        <div class="bg-white rounded-3xl shadow-2xl z-50 w-full max-w-md overflow-hidden relative">
            <div class="bg-[#00872E] p-6 text-white font-black uppercase text-sm">Edit Service</div>
            <form id="editForm" method="POST" class="p-8 space-y-6">
                @csrf @method('PUT')
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2">Service Name</label>
                    <input type="text" id="edit_name" name="service_name" class="w-full bg-gray-50 border rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div>
                    <label class="text-[10px] font-black text-gray-400 uppercase block mb-2">Phone Number</label>
                    <input type="text" id="edit_phone" name="phone_number" class="w-full bg-gray-50 border rounded-xl px-4 py-3 text-sm font-mono focus:ring-2 focus:ring-green-500 outline-none" required>
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="toggleModal('editHotlineModal')" class="flex-1 font-black text-[11px] uppercase text-gray-400">Cancel</button>
                    <button type="submit" class="flex-1 bg-[#00872E] text-white px-6 py-3 rounded-xl font-black text-[11px] uppercase shadow-lg hover:bg-green-800 transition">Update Service</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }

    function editHotline(hotline) {
        // Set the form action URL dynamically
        const form = document.getElementById('editForm');
        form.action = `/admin/hotlines/${hotline.id}`;
        
        // Populate inputs
        document.getElementById('edit_name').value = hotline.service_name;
        document.getElementById('edit_phone').value = hotline.phone_number;
        
        // Show modal
        toggleModal('editHotlineModal');
    }
</script>
@endsection