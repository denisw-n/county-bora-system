<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | County Bora</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">
    <div class="min-h-screen flex items-center justify-center p-8">
        <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <h2 class="text-2xl font-black text-gray-800 mb-2">Reset Password</h2>
            <p class="text-gray-500 text-sm mb-8">Enter your email address to receive a password reset link.</p>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl text-xs font-bold border border-green-100">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-xs font-bold border border-red-100">
                    <ul class="list-disc pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-[#FEDF0E] outline-none">
                </div>

                <button type="submit" class="w-full bg-[#FEDF0E] text-gray-900 font-black py-4 rounded-xl hover:bg-[#d4bc0c] transition uppercase text-xs tracking-widest">
                    Send Reset Link →
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-xs font-bold text-gray-400 hover:text-gray-800 uppercase tracking-widest">Back to Login</a>
            </div>
        </div>
    </div>
</body>
</html>