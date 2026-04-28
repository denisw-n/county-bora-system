@extends('layouts.admin')

@section('title', 'Public Communication')

@section('content')
    <header class="h-16 bg-white/90 backdrop-blur-md sticky top-0 z-20 px-8 flex items-center justify-between border-b border-gray-100">
        <div class="flex items-center gap-8">
            <span class="text-[#00872E] font-black text-xs tracking-widest uppercase">Communication Portal</span>
            <nav class="flex gap-6 text-[10px] font-black uppercase tracking-widest text-gray-400">
                <a href="{{ route('admin.dashboard') }}" class="py-5 hover:text-gray-900 transition">Overview</a>
                <a href="{{ route('admin.communication.index') }}" class="text-[#00872E] border-b-2 border-[#00872E] py-5">Broadcast Console</a>
            </nav>
        </div>
        <div class="w-8 h-8 rounded-lg bg-[#00872E] flex items-center justify-center text-white font-black text-xs shadow-sm uppercase">
            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
        </div>
    </header>

    <div class="p-8 max-w-[1400px] mx-auto space-y-12">
        @if(session('success'))
            <div class="bg-[#00872E] text-white p-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-lg">
                ✓ {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-12 gap-8">
            {{-- Dispatch Form --}}
            <div class="col-span-12 lg:col-span-7 bg-white p-10 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex items-center gap-3 mb-8">
                    <div class="bg-green-50 p-3 rounded-2xl text-[#00872E] font-bold">🔔</div>
                    <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest">Citizen Awareness Console</h3>
                </div>

                <form action="{{ route('admin.communication.broadcast') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="type" value="General">

                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <label class="text-[10px] font-black text-gray-400 uppercase mb-4 block tracking-widest">Audience Scope</label>
                        <div class="flex gap-4">
                            <label class="flex-1">
                                <input type="radio" name="scope" value="public" checked class="hidden peer" onclick="toggleUserSelection(false)">
                                <div class="text-center py-3 rounded-xl border-2 border-white bg-white shadow-sm text-[10px] font-black uppercase cursor-pointer peer-checked:border-[#00872E] peer-checked:text-[#00872E] transition">Public</div>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="scope" value="personal" class="hidden peer" onclick="toggleUserSelection(true)">
                                <div class="text-center py-3 rounded-xl border-2 border-white bg-white shadow-sm text-[10px] font-black uppercase cursor-pointer peer-checked:border-blue-500 peer-checked:text-blue-500 transition">Personalized</div>
                            </label>
                        </div>
                    </div>

                    <div id="userSelectionArea" class="hidden relative">
                        <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block tracking-widest">Search Citizen</label>
                        <input type="text" id="userSearchInput" class="w-full bg-[#F3F4F6] border-none rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500" placeholder="Type name, email, or ID..." autocomplete="off">
                        <div id="searchResults" class="absolute z-30 w-full bg-white mt-1 rounded-xl shadow-2xl border border-gray-100 hidden max-h-60 overflow-y-auto"></div>
                        <input type="hidden" name="user_id" id="selectedUserId">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block tracking-widest">Headline</label>
                        <input type="text" name="title" id="titleInput" required class="w-full bg-[#F3F4F6] border-none rounded-xl px-4 py-3 text-sm" placeholder="Subject">
                    </div>

                    <div>
                        <label class="text-[10px] font-black text-gray-400 uppercase mb-2 block tracking-widest">Message</label>
                        <textarea name="content" id="bodyInput" rows="4" required class="w-full bg-[#F3F4F6] border-none rounded-xl px-4 py-3 text-sm" placeholder="Enter message..."></textarea>
                    </div>

                    <button type="submit" class="w-full bg-[#FEDF0E] text-[#716200] font-black py-4 rounded-xl shadow-lg uppercase text-[10px] active:scale-[0.98] transition">▶ Dispatch Message</button>
                </form>
            </div>

            {{-- Device Preview --}}
            <div class="col-span-12 lg:col-span-5 flex flex-col items-center pt-10">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Device Preview</span>
                <div class="w-72 h-[500px] bg-black rounded-[3rem] border-[8px] border-gray-800 relative shadow-2xl overflow-hidden">
                    <div class="w-32 h-6 bg-gray-800 absolute top-0 left-1/2 -translate-x-1/2 rounded-b-3xl z-10"></div>
                    <div class="mt-16 px-4">
                        <div class="bg-white/90 backdrop-blur-xl rounded-2xl p-4 shadow-2xl">
                            <h4 id="previewTitle" class="text-xs font-black text-gray-900">Subject Line</h4>
                            <p id="previewBody" class="text-[10px] text-gray-600 mt-1">Admin is typing...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- UPDATED: Archive with Live JavaScript Filtering --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden mt-12">
            <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h3 class="font-black text-gray-800 text-sm uppercase tracking-widest">Communication Archive</h3>
                
                {{-- Live Search Input (No Form) --}}
                <div class="relative w-full md:w-80">
                    <input type="text" id="archiveSearchInput" 
                        placeholder="Live filter history..." 
                        class="w-full bg-gray-50 border-none rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-wider focus:ring-2 focus:ring-[#00872E]">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300">🔍</div>
                </div>
            </div>

            <table class="w-full text-left" id="communicationTable">
                <thead>
                    <tr class="bg-gray-50/50 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">
                        <th class="px-8 py-5">Scope</th>
                        <th class="px-8 py-5">Recipient</th>
                        <th class="px-8 py-5">Message Content</th>
                        <th class="px-8 py-5 text-right">Sent Date</th>
                    </tr>
                </thead>
                <tbody class="text-[11px] font-semibold text-gray-600" id="archiveTableBody">
                    @forelse($allCommunications as $comm)
                    <tr class="archive-row border-b border-gray-50 hover:bg-gray-50/30 transition">
                        <td class="px-8 py-5">
                            <span class="px-3 py-1 rounded-full text-[8px] font-black uppercase 
                                {{ $comm->display_scope == 'Public' ? 'bg-[#FEDF0E] text-[#716200]' : 'bg-blue-100 text-blue-600' }}">
                                {{ $comm->display_scope }}
                            </span>
                        </td>
                        <td class="px-8 py-5 font-black text-gray-800 uppercase tracking-tighter recipient-data">
                            {{ $comm->recipient }}
                        </td>
                        <td class="px-8 py-5 message-data">
                            <div class="flex flex-col">
                                <span class="text-gray-900 font-bold msg-title">{{ $comm->title }}</span>
                                <span class="text-gray-400 italic text-[10px]">{{ Str::limit($comm->display_message, 60) }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right text-gray-400 font-mono">
                            {{ $comm->created_at->format('d M, H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-8 py-20 text-center text-gray-400 uppercase tracking-widest text-[10px] font-black">
                            No dispatch history available.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // --- Existing Composer Logic ---
    const titleInput = document.getElementById('titleInput');
    const bodyInput = document.getElementById('bodyInput');
    const previewTitle = document.getElementById('previewTitle');
    const previewBody = document.getElementById('previewBody');
    const userSearchInput = document.getElementById('userSearchInput');
    const searchResults = document.getElementById('searchResults');
    const selectedUserId = document.getElementById('selectedUserId');

    titleInput.addEventListener('input', (e) => previewTitle.innerText = e.target.value || "Subject Line");
    bodyInput.addEventListener('input', (e) => previewBody.innerText = e.target.value || "Admin is typing...");
    
    function toggleUserSelection(show) {
        const area = document.getElementById('userSelectionArea');
        area.classList.toggle('hidden', !show);
        userSearchInput.required = show;
        if (!show) {
            userSearchInput.value = '';
            selectedUserId.value = '';
            searchResults.classList.add('hidden');
        }
    }

    userSearchInput.addEventListener('input', function(e) {
        let query = e.target.value;
        if (query.length < 2) {
            searchResults.classList.add('hidden');
            return;
        }
        fetch(`/admin/communication/search-users?q=${query}`)
            .then(response => response.json())
            .then(data => {
                searchResults.innerHTML = '';
                if (data.length > 0) {
                    searchResults.classList.remove('hidden');
                    data.forEach(user => {
                        let div = document.createElement('div');
                        div.className = "p-4 hover:bg-gray-50 cursor-pointer border-b border-gray-50 last:border-none transition";
                        div.innerHTML = `
                            <div class="font-black text-gray-800 text-[10px] uppercase tracking-wider">${user.first_name} ${user.last_name}</div>
                            <div class="text-[9px] text-gray-400 font-bold uppercase tracking-widest">${user.email}</div>
                        `;
                        div.onclick = function() {
                            userSearchInput.value = `${user.first_name} ${user.last_name}`;
                            selectedUserId.value = user.id;
                            searchResults.classList.add('hidden');
                        };
                        searchResults.appendChild(div);
                    });
                }
            });
    });

    // --- NEW: Live Archive Filtering Logic ---
    const archiveSearchInput = document.getElementById('archiveSearchInput');
    const archiveRows = document.querySelectorAll('.archive-row');

    archiveSearchInput.addEventListener('input', function(e) {
        const term = e.target.value.toLowerCase().trim();

        archiveRows.forEach(row => {
            const recipient = row.querySelector('.recipient-data').textContent.toLowerCase();
            const message = row.querySelector('.message-data').textContent.toLowerCase();

            // If the row matches the search term, show it, otherwise hide it
            if (recipient.includes(term) || message.includes(term)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (!userSearchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
</script>
@endpush