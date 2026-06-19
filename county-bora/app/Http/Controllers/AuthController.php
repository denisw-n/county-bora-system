<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invite;
use App\Models\Ward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    // --- REGISTER (Citizen App) ---
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'national_id' => 'required|string|unique:users',
            'phone_number' => 'required|string',
            'ward_id' => 'required|exists:wards,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'national_id' => $request->national_id,
            'phone_number' => $request->phone_number,
            'ward_id' => $request->ward_id,
            'role' => 'citizen', 
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    // --- ADMIN REGISTER (Consumes Invite Token) ---
    public function adminRegister(Request $request)
    {
        // 1. Validate fields including ward_id
        $request->validate([
            'token'        => 'required|exists:invites,token',
            'first_name'   => 'required|string|max:255',
            'last_name'    => 'required|string|max:255',
            'password'     => 'required|min:8|confirmed',
            'national_id'  => 'required|string|unique:users,national_id',
            'phone_number' => 'required|string|max:20',
            'ward_id'      => 'required|exists:wards,id', // Admin must now pick a ward
        ]);

        // 2. Find the valid invite
        $invite = Invite::where('token', $request->token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // 3. Create the user with the selected ward_id
        $user = User::create([
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $invite->email,
            'password'     => Hash::make($request->password),
            'role'         => $invite->role,
            'is_verified'  => true,
            'national_id'  => $request->national_id,
            'phone_number' => $request->phone_number,
            'ward_id'      => $request->ward_id, 
        ]);

        // 4. Cleanup
        $invite->delete();

        Auth::login($user);
        return redirect('/admin/dashboard')->with('success', 'Welcome, Admin!');
    }

    // --- LOGIN ---
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json($validator->errors(), 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
        }

        // Clear login attempts upon successful authentication
        RateLimiter::clear('login_attempts:' . $request->ip());

        if (!$request->expectsJson()) {
            if ($user->role !== 'admin') {
                return back()->withErrors(['email' => 'Access Denied: Only admins can log in here.']);
            }
            
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard');
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    // --- LOGOUT ---
    public function logout(Request $request)
    {
        if ($request->expectsJson()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}