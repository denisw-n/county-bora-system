<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class InvitationController extends Controller
{
    /**
     * Show the form to generate an invite.
     */
    public function create()
    {
        return view('admin.invitations.create');
    }

    /**
     * Generate a new invite record.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email|unique:users,email|unique:invites,email',
            ]);

            // Attempt to create the invite
            $invite = Invite::create([
                'email' => $validated['email'],
                'role'  => 'admin',
            ]);

            // Verify if it actually saved
            if (!$invite || !$invite->token) {
                throw new Exception("Model failed to boot or token was not generated.");
            }

            $url = route('admin.invitations.accept', ['token' => $invite->token]);

            return back()->with('success', "Invite generated! Copy this link to share: " . $url);
            
        } catch (Exception $e) {
            // This will display the error on your screen instead of refreshing silently
            return "Database Error: " . $e->getMessage();
        }
    }

    /**
     * Validate the token and present the registration form.
     */
    public function accept($token)
    {
        // Find the active, unexpired invite
        $invite = Invite::where('token', $token)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        // Pass both email and token to the registration view
        return view('auth.register', [
            'email' => $invite->email, 
            'token' => $token
        ]);
    }
}