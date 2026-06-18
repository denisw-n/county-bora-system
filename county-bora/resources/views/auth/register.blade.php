<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Registration | County Bora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 bg-gray-900 p-12 flex-col justify-between" style="background-image: url('{{ asset('images/nairobi-bg.jpg') }}'); background-size: cover; background-position: center;">
            <div class="text-white">
                <img src="{{ asset('images/logo.png') }}" class="w-20 mb-8" alt="Logo">
                <h1 class="text-4xl font-black mb-4">County Bora</h1>
                <p class="text-gray-300 italic">"Join the team working to build a city of choice to invest, work and live in."</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                <h2 class="text-2xl font-black text-gray-800 mb-2">Complete Registration</h2>
                <p class="text-gray-500 text-sm mb-8">Set your details and password to activate your admin account.</p>

                {{-- Display Validation Errors --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-xs font-bold border border-red-100">
                        <ul class="list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.submit') }}" method="POST">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    
                    <div class="mb-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Email Address</label>
                        <input type="email" value="{{ $email }}" disabled class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">National ID</label>
                        <input type="text" name="national_id" value="{{ old('national_id') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                    </div>

                    {{-- Ward Selection Dropdown --}}
                    <div class="mb-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Assign Ward</label>
                        <select name="ward_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none bg-white">
                            <option value="">Select a Ward</option>
                            @foreach(\App\Models\Ward::all() as $ward)
                                <option value="{{ $ward->id }}" {{ old('ward_id') == $ward->id ? 'selected' : '' }}>
                                    {{ $ward->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                    </div>

                    <div class="mb-8">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                    </div>

                    <button type="submit" class="w-full bg-[#FEDF0E] text-gray-900 font-black py-4 rounded-xl hover:bg-[#d4bc0c] transition uppercase text-xs tracking-widest">
                        Complete Registration →
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>