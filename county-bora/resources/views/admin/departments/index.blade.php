@extends('layouts.admin')

@section('title', 'Departments')

@section('content')
    <header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
        <div class="flex items-center gap-8">
            <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">Nairobi County Admin</span>
            <nav class="flex gap-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                <a href="{{ route('admin.dashboard') }}" class="py-5 hover:text-gray-900 transition">Global View</a>
                <a href="{{ route('admin.departments.index') }}" class="text-[#00872E] border-b-2 border-[#00872E] py-5">Departments</a>
            </nav>
        </div>
        
        <div class="flex items-center gap-4">
            <div class="relative">
                <form action="{{ route('admin.departments.index') }}" method="GET">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search registry..." 
                           class="bg-[#F3F4F6] border-none rounded-xl pl-4 pr-10 py-2 text-[11px] w-64 focus:ring-2 focus:ring-[#00872E]">
                    <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs hover:text-[#00872E] transition">
                        🔍
                    </button>
                </form>
            </div>
            <div class="w-8 h-8 rounded-lg bg-[#00872E] flex items-center justify-center text-white font-black text-xs shadow-sm uppercase">
                {{ substr(Auth::user()->name ?? 'D', 0, 1) }}
            </div>
        </div>
    </header>

    <div class="p-8 max-w-[1400px] mx-auto space-y-6">
        
        @if(session('success'))
            <div class="bg-[#00872E] text-white p-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg animate-bounce">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-[2.5rem] p-8 border border-gray-100 shadow-sm">
            <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest mb-6">Initialize Department</h3>
            <form action="{{ route('admin.departments.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @csrf
                <div class="md:col-span-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block tracking-widest">Department Name</label>
                    <input type="text" name="dept_name" required class="w-full bg-[#F3F4F6] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#00872E]" placeholder="e.g. Health & Sanitation">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-[#00872E] text-white font-black py-3 rounded-xl hover:bg-[#006D24] transition uppercase text-[10px] tracking-widest shadow-lg">
                        Create Department
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest">Departmental Registry</h3>
                @if(request('search'))
                    <a href="{{ route('admin.departments.index') }}" class="text-[9px] font-black text-red-500 uppercase tracking-widest bg-red-50 px-3 py-1 rounded-full">Clear Search: "{{ request('search') }}" ✕</a>
                @endif
            </div>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                        <th class="px-8 py-5">Organization Unit</th>
                        <th class="px-8 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Operational Actions</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-semibold text-gray-600">
                    @forelse($departments as $dept)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/30 transition">
                        <td class="px-8 py-5 font-bold text-gray-800 uppercase tracking-tight">{{ $dept->dept_name }}</td>
                        <td class="px-8 py-5">
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest">Active</span>
                        </td>
                        <td class="px-8 py-5 text-right flex justify-end gap-6">
                            <button onclick="editDept('{{ $dept->id }}', '{{ $dept->dept_name }}')" class="text-[#00872E] hover:text-green-800 font-black uppercase text-[10px] tracking-widest transition">Edit</button>
                            <form action="{{ route('admin.departments.destroy', $dept->id) }}" method="POST" onsubmit="return confirm('Confirm Decommissioning?');" class="inline">
                                @csrf @method('DELETE')
                                <button class="text-red-400 hover:text-red-600 font-black uppercase text-[10px] tracking-widest transition">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-10 text-center text-gray-400 font-bold uppercase tracking-widest text-[10px]">No departments found matching your criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-6 bg-gray-50/30">
                {{ $departments->links() }}
            </div>
        </div>
    </div>

    <form id="update-form" method="POST" style="display: none;">
        @csrf @method('PUT')
        <input type="hidden" name="dept_name" id="edit-name">
    </form>
@endsection

@push('scripts')
<script>
    function editDept(id, currentName) {
        const newName = prompt("Update Department Identity:", currentName);
        if (newName && newName !== currentName) {
            const form = document.getElementById('update-form');
            form.action = `/admin/departments/${id}`;
            document.getElementById('edit-name').value = newName;
            form.submit();
        }
    }
</script>
@endpush