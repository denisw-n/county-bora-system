<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile data.
     * Aligned with Screenshot (1153).jpg.
     */
    public function show(Request $request): JsonResponse
    {
        // Load the ward relationship to get the ward name string
        $user = $request->user()->load('ward');

        return response()->json([
            'status' => 'success',
            'data' => [
                'full_name'   => trim($user->first_name . ' ' . ($user->middle_name ?? '') . ' ' . $user->last_name),
                'national_id' => $user->national_id,
                'phone'       => $user->phone_number,
                'email'       => $user->email,
                'ward'        => $user->ward ? $user->ward->name : 'Not Assigned',
                'sub_county'  => $user->ward ? $user->ward->sub_county : 'N/A',
                'is_verified' => (bool)$user->is_verified,
            ]
        ]);
    }

    /**
     * Allow users to update their phone and ward as they move.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'phone_number' => 'sometimes|string|max:20',
            'ward_id'      => 'sometimes|exists:wards,id',
        ]);

        $user->update($validated);

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully',
            'data'    => $user->load('ward')
        ]);
    }
}