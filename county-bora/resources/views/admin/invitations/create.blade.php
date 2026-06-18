@extends('layouts.admin')

@section('title', 'Invite New Admin')

@section('content')
<div class="p-8 max-w-2xl mx-auto">
    <h1 class="text-xl font-black uppercase text-gray-800 tracking-tight mb-6">Invite New Admin</h1>
    
    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl text-sm font-bold mb-6">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.invitations.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Admin Email Address</label>
                <input type="email" name="email" required 
                    class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-gray-800 outline-none transition">
            </div>
            
            <button type="submit" class="w-full bg-gray-800 text-white font-black py-3 rounded-xl hover:bg-black transition uppercase text-xs tracking-widest">
                Generate Invite Link
            </button>
        </form>
    </div>
</div>
@endsection