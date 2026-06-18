<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | County Bora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center p-8">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-2xl font-black text-gray-800 mb-2">Set New Password</h2>
            <p class="text-gray-500 text-sm mb-8">Enter your email and your new password below.</p>

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-xs font-bold border border-red-100">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                </div>

                <div class="mb-4">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">New Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                </div>

                <div class="mb-8">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                </div>

                <button type="submit" class="w-full bg-[#FEDF0E] text-gray-900 font-black py-4 rounded-xl hover:bg-[#d4bc0c] transition uppercase text-xs tracking-widest">
                    Update Password →
                </button>
            </form>
        </div>
    </div>
</body>
</html>