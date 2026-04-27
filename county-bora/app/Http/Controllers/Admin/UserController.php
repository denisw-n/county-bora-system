<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * List all citizens with pagination.
     * Maps to: GET /admin/users
     */
    public function index()
    {
        $users = User::where('role', 'citizen')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the ID verification queue.
     * Maps to: GET /admin/users/verification
     */
    public function verificationIndex()
    {
        $users = User::where('role', 'citizen')
                     ->where('is_verified', false)
                     ->latest()
                     ->paginate(10);

        return view('admin.users.verification', compact('users'));
    }

    /**
     * Toggle user verification status (Approve or Revoke).
     * Includes automatic notification for successful verification.
     * Maps to: PATCH /admin/users/{user}/verify
     */
    public function toggleVerification(User $user)
    {
        $user->update([
            'is_verified' => !$user->is_verified
        ]);

        // If the user was just verified, create a notification entry
        if ($user->is_verified) {
            DB::table('notifications')->insert([
                'user_id'    => $user->id,
                'title'      => 'Account Verified',
                'message'    => 'Your account has been successfully verified. You now have full access to County Bora services.',
                'is_read'    => 0,
                'type'       => 'General',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $status = $user->is_verified ? 'verified and notified' : 'verification revoked';
        
        return redirect()->back()->with('success', "Citizen {$status} successfully.");
    }

    /**
     * Permanently remove a user account.
     * Maps to: DELETE /admin/users/{user}
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('warning', 'User account permanently removed.');
    }
}