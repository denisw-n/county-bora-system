<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Handle Citizen Registration with ID Photo
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'        => 'required|string|max:255',
            'middle_name'       => 'nullable|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'national_id'       => 'required|string|unique:users',
            'national_id_image' => 'required|image|mimes:jpeg,png,jpg|max:10240', // 10MB Limit
            'phone_number'      => 'required|string|unique:users',
            'password'          => 'required|string|min:8|confirmed',
            'ward_id'           => 'required|uuid|exists:wards,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // 1. Handle the ID image storage
        $idImagePath = null;
        if ($request->hasFile('national_id_image')) {
            // Stores in storage/app/public/identity_docs
            $idImagePath = $request->file('national_id_image')->store('identity_docs', 'public');
        }

        // 2. Create the User (UUID is handled by User Model boot method)
        $user = User::create([
            'first_name'            => $request->first_name,
            'middle_name'           => $request->middle_name,
            'last_name'             => $request->last_name,
            'email'                 => $request->email,
            'national_id'           => $request->national_id,
            'national_id_image_url' => $idImagePath, 
            'phone_number'          => $request->phone_number,
            'ward_id'               => $request->ward_id,
            'password'              => Hash::make($request->password),
            'role'                  => 'citizen',
            'is_verified'           => false, 
        ]);

        return response()->json([
            'message' => 'Registration successful. Please wait for admin verification before logging in.',
            'user' => $user
        ], 201);
    }

    /**
     * Handle Citizen Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string', // Email or National ID
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'national_id';

        $user = User::where($loginField, $request->login)->first();

        // 1. Validate Credentials
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        // 2. Verification Gate
        if (!$user->is_verified) {
            return response()->json([
                'status' => 'pending_verification',
                'message' => 'Your account is pending approval by Nairobi County Admins. Please try again once verified.'
            ], 403);
        }

        // 3. Issue Token
        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    /**
     * Handle Citizen Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}