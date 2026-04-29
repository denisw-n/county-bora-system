@extends('layouts.admin')

@section('title', 'Wards | County Bora Registry')

@section('content')
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
            <div class="bg-[#00872E] text-white p-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg animate-fade-in-down">
                ✓ {{ session('success') }}
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
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
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
                    @forelse($wards as $ward)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition">
                        <td class="px-8 py-5 font-bold text-gray-800 uppercase tracking-tight">
                            <a href="{{ route('admin.wards.show', $ward->id) }}" class="hover:text-[#00872E] transition-colors">
                                {{ $ward->name }}
                            </a>
                        </td>
                        <td class="px-8 py-5 text-gray-400 font-bold uppercase">{{ $ward->sub_county }}</td>
                        <td class="px-8 py-5 text-center">
                            <span class="bg-[#FEDF0E] text-[#716200] px-3 py-1 rounded-full font-black text-[10px] shadow-sm">
                                {{ $ward->reports_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right flex justify-end gap-6 items-center">
                            <button onclick="editWard('{{ $ward->id }}', '{{ $ward->name }}', '{{ $ward->sub_county }}')" 
                                    class="text-[#00872E] hover:text-green-800 font-black uppercase text-[10px] tracking-widest transition">
                                Edit
                            </button>

                            <form action="{{ route('admin.wards.destroy', $ward->id) }}" method="POST" onsubmit="return confirm('Archive this ward? This cannot be undone.');" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 font-black uppercase text-[10px] tracking-widest transition">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-gray-400 font-bold uppercase tracking-widest text-[10px]">
                            No wards registered in the registry.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-6 bg-gray-50/30">
                {{ $wards->links() }}
            </div>
        </div>
    </div>

    <form id="update-form" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="name" id="edit-name">
        <input type="hidden" name="sub_county" id="edit-sub_county">
    </form>
@endsection

@push('scripts')
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
@endpush