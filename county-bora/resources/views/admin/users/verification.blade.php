@extends('layouts.admin')

@section('title', 'User Verification')

@section('content')
<div class="p-8">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 uppercase tracking-tight">Verification Queue</h2>
        <p class="text-sm text-gray-500 font-medium">Review and approve citizen identities for system access.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">Citizen Name</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">National ID</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider">ID Document</th>
                    <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-wider text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-700">{{ $user->first_name }} {{ $user->last_name }}</div>
                        <div class="text-[10px] text-gray-400 font-mono italic">{{ $user->email }}</div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-600">{{ $user->national_id }}</td>
                    <td class="px-6 py-4">
                        @if($user->national_id_image_url)
                            <button onclick="viewDocument('{{ asset('storage/' . $user->national_id_image_url) }}')" 
                                    class="text-[#00872E] font-black text-[10px] uppercase underline underline-offset-4 hover:text-green-800">
                                Review ID Document
                            </button>
                        @else
                            <span class="text-gray-300 italic text-[10px]">No Document Uploaded</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-end gap-3">
                            <form action="{{ route('admin.users.verify', $user->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" 
                                        class="bg-[#00872E] text-white px-4 py-2 rounded-lg font-black text-[10px] uppercase shadow-md hover:bg-green-800 transition active:scale-95">
                                    Accept & Verify
                                </button>
                            </form>

                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                  onsubmit="return confirm('REJECT APPLICATION? This will permanently delete this user account.')">
                                @csrf @method('DELETE')
                                <button type="submit" 
                                        class="bg-white border-2 border-red-600 text-red-600 px-4 py-2 rounded-lg font-black text-[10px] uppercase hover:bg-red-50 transition active:scale-95">
                                    Deny & Reject
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <p class="text-gray-400 font-bold uppercase text-[11px] tracking-widest">No pending verifications found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        @if($users->hasPages())
        <div class="p-6 bg-gray-50 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Document Preview Modal --}}
<div id="docModal" class="fixed inset-0 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/80 transition-opacity" onclick="closeDoc()"></div>
        <div class="bg-white rounded-3xl shadow-2xl z-50 max-w-3xl w-full overflow-hidden relative">
            <img id="docPreview" src="" class="w-full h-auto">
            <button onclick="closeDoc()" class="absolute top-4 right-4 bg-black/50 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold">✕</button>
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