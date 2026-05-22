<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * REGISTER
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'        => 'required|string|max:255',
            'middle_name'       => 'nullable|string|max:255',
            'last_name'         => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'national_id'       => 'required|string|unique:users',
            'national_id_image' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'phone_number'      => 'required|string|unique:users',
            'password'          => 'required|string|min:8|confirmed',
            'ward_id'           => 'required|uuid|exists:wards,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $idImagePath = null;

        if ($request->hasFile('national_id_image')) {
            $idImagePath = $request->file('national_id_image')
                ->store('identity_docs', 'public');
        }

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
            'message' => 'Registration successful. Await admin verification.',
            'user' => $user
        ], 201);
    }

    /**
     * LOGIN (FIXED FOR FLUTTER)
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'required|string',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'national_id';

        $user = User::where($loginField, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (!$user->is_verified) {
            return response()->json([
                'status' => 'pending_verification',
                'message' => 'Account pending admin approval'
            ], 403);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 200);
    }

    /**
     * LOGOUT
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }
}