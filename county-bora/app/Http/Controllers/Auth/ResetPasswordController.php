<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request, $token = null)
    {
        try {
            // Diagnostic check: Does the view file exist?
            if (!view()->exists('auth.passwords.reset')) {
                die("<h1>Error:</h1> View file [resources/views/auth/passwords/reset.blade.php] not found. Check your file structure.");
            }

            return view('auth.passwords.reset')->with([
                'token' => $token, 
                'email' => $request->email
            ]);
        } catch (\Exception $e) {
            // Force the error to display instead of a blank screen
            die("<h1>Controller Error:</h1> " . $e->getMessage());
        }
    }

    /**
     * Handle the password reset request.
     */
    public function reset(Request $request)
    {
        // 1. Validate the input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        // 2. Attempt to reset the password
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        // 3. Handle the result
        return $status === Password::PASSWORD_RESET
                    ? redirect('/admin/dashboard')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}