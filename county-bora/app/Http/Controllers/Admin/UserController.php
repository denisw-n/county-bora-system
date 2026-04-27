<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all citizens with pagination.
     * Maps to: GET /admin/users
     */
    public function index()
    {
        $users = User::where('role', 'citizen')->paginate(10); // [cite: 93, 147]
        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the ID verification queue.
     * Maps to: GET /admin/users/verification
     */
    public function verificationIndex()
    {
        // Filter for unverified citizens who have submitted an ID image [cite: 22, 124]
        $users = User::where('role', 'citizen')
                     ->where('is_verified', false)
                     ->whereNotNull('national_id_image_url')
                     ->paginate(10);

        return view('admin.users.verification', compact('users'));
    }

    /**
     * Toggle user verification status.
     * Maps to: PATCH /admin/users/{user}/verify
     */
    public function toggleVerification(User $user)
    {
        $user->update([
            'is_verified' => !$user->is_verified // [cite: 22, 94]
        ]);

        $message = $user->is_verified ? 'User verified successfully.' : 'User verification revoked.';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Permanently remove a user account.
     * Maps to: DELETE /admin/users/{id}
     */
    public function destroy(User $user)
    {
        $user->delete(); // [cite: 123, 154]
        return redirect()->back()->with('warning', 'User account permanently removed.');
    }
}