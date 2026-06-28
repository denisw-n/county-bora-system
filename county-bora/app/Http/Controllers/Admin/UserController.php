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
     */
    public function index()
    {
        $users = User::where('role', 'citizen')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Display the ID verification queue.
     * Only shows users who are 'citizen' and NOT verified.
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
     * Handle user verification approval or rejection.
     */
    public function toggleVerification(Request $request, User $user)
    {
        $action = $request->input('action'); 

        if ($action === 'approve') {
            $user->update(['is_verified' => true]);

            DB::table('notifications')->insert([
                'user_id'    => $user->id,
                'title'      => 'Account Verified',
                'message'    => 'Congratulations! Your account has been verified. You now have full access to County Bora services.',
                'is_read'    => 0,
                'type'       => 'Verification',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', "Citizen verified and notified.");
        } 

        if ($action === 'reject') {
            /** * THE FIX: Change role to 'rejected_citizen'. 
             * This removes them from verificationIndex because the query 
             * filters for role == 'citizen'.
             */
            $user->update(['role' => 'rejected_citizen']);

            DB::table('notifications')->insert([
                'user_id'    => $user->id,
                'title'      => 'Verification Declined',
                'message'    => 'Your ID verification was unsuccessful. Please ensure your ID photo is clear and matches your profile details.',
                'is_read'    => 0,
                'type'       => 'Verification',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            return redirect()->back()->with('warning', "Verification rejected. User removed from queue.");
        }

        $user->update(['is_verified' => !$user->is_verified]);
        return redirect()->back()->with('success', "Verification status updated.");
    }

    /**
     * Permanently remove a user account.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('warning', 'User account permanently removed.');
    }
}