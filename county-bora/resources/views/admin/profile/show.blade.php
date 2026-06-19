@extends('layouts.admin')

@section('title', 'Admin Profile | County Bora')

@section('content')
    <div class="p-8 max-w-2xl mx-auto">
        <h1 class="text-2xl font-black text-gray-800 uppercase tracking-widest mb-8">Admin Profile</h1>
        
        <div class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
            <div class="flex items-center gap-6 mb-8">
                <!-- Hardcoded initial 'A' -->
                <div class="w-20 h-20 rounded-2xl bg-[#00872E] flex items-center justify-center text-white font-black text-3xl shadow-lg uppercase">
                    A
                </div>
                <div>
                    <!-- Hardcoded name 'Admin' -->
                    <h2 class="text-xl font-black text-gray-800">Admin</h2>
                    <p class="text-sm text-gray-500 font-bold uppercase tracking-wider">{{ $user->email }}</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase">Account Role</p>
                    <p class="text-sm font-bold text-gray-700">System Administrator</p>
                </div>
                <div class="border-t border-gray-100 pt-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase">Registered On</p>
                    <p class="text-sm font-bold text-gray-700">{{ $user->created_at->format('F d, Y') }}</p>
                </div>
            </div>

            <div class="mt-10">
                <a href="{{ route('admin.dashboard') }}" class="text-[#00872E] font-black text-[10px] uppercase tracking-widest hover:underline">
                    &larr; Back to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection