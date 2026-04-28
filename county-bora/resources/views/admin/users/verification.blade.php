@extends('layouts.admin')

@section('title', 'User Verification')

@section('content')
<div class="p-8">
    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="mb-6 bg-[#00872E] text-white p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-6 bg-amber-500 text-white p-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg">
            ⚠ {{ session('warning') }}
        </div>
    @endif

    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Verification Queue</h2>
        <p class="text-sm text-gray-500 font-medium">Review and approve citizen identities for system access.</p>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Citizen Name</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">National ID</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">ID Document</th>
                    <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-xs font-semibold text-gray-600">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/30 transition-colors">
                    <td class="px-8 py-5">
                        <div class="font-black text-gray-800 uppercase tracking-tight">{{ $user->first_name }} {{ $user->last_name }}</div>
                        <div class="text-[10px] text-gray-400 font-mono lowercase mt-0.5">{{ $user->email }}</div>
                    </td>
                    <td class="px-8 py-5 font-bold text-gray-500 tracking-widest">{{ $user->national_id }}</td>
                    <td class="px-8 py-5">
                        @if($user->national_id_image_url)
                            <button onclick="viewDocument('{{ asset('storage/' . $user->national_id_image_url) }}')" 
                                    class="text-[#00872E] font-black text-[9px] uppercase border-b-2 border-[#00872E]/20 hover:border-[#00872E] transition-all pb-0.5">
                                Review Document
                            </button>
                        @else
                            <span class="text-gray-300 italic text-[10px]">Missing File</span>
                        @endif
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex justify-end gap-2">
                            {{-- APPROVE --}}
                            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" 
                                        class="bg-[#00872E] text-white px-5 py-2.5 rounded-xl font-black text-[9px] uppercase shadow-sm hover:shadow-md hover:bg-green-800 transition active:scale-95">
                                    Approve
                                </button>
                            </form>

                            {{-- REJECT (NOTIFY USER) --}}
                            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST" 
                                  onsubmit="return confirm('REJECT IDENTITY? This will notify the user to re-submit.')">
                                @csrf @method('PATCH')
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" 
                                        class="bg-white border-2 border-red-100 text-red-600 px-5 py-2 rounded-xl font-black text-[9px] uppercase hover:bg-red-50 hover:border-red-200 transition active:scale-95">
                                    Deny
                                </button>
                            </form>

                            {{-- DELETE (PERMANENT) --}}
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                  onsubmit="return confirm('PERMANENTLY DELETE ACCOUNT? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2.5 text-gray-300 hover:text-red-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-8 py-20 text-center">
                        <p class="text-gray-300 font-black uppercase text-[10px] tracking-[0.3em]">No pending verifications</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
        <div class="p-6 bg-gray-50/50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Document Preview Modal --}}
<div id="docModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-6 bg-black/90 backdrop-blur-sm" onclick="closeDoc()">
    <div class="relative max-w-4xl w-full bg-white rounded-[3rem] overflow-hidden shadow-2xl" onclick="event.stopPropagation()">
        <img id="docPreview" src="" class="w-full h-auto max-h-[80vh] object-contain bg-gray-100">
        <div class="p-6 flex justify-between items-center bg-white">
            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Identity Document Verification</span>
            <button onclick="closeDoc()" class="bg-gray-900 text-white px-6 py-2 rounded-full font-black text-[10px] uppercase">Close Preview</button>
        </div>
    </div>
</div>

<script>
    function viewDocument(url) {
        document.getElementById('docPreview').src = url;
        document.getElementById('docModal').classList.remove('hidden');
    }
    function closeDoc() {
        document.getElementById('docModal').classList.add('hidden');
    }
</script>
@endsection