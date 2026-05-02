<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * List all users pending verification
     */
    public function getPendingUsers()
    {
        $users = User::where('is_verified', false)
            ->where('role', 'citizen')
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $users->count(),
            'data' => $users
        ]);
    }

    /**
     * Verify a specific user
     */
    public function verifyUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update([
            'is_verified' => true
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "User {$user->first_name} has been verified successfully.",
            'user' => $user
        ]);
    }
}