<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Alert; 
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Handle the Unified Communication Dispatch
     * This matches your form action: admin.communication.broadcast
     */
    public function broadcast(Request $request)
    {
        // 1. Validation
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'scope'   => 'required|in:public,personal',
            'user_id' => 'required_if:scope,personal|exists:users,id',
            'type'    => 'required|string'
        ]);

        // Using input() instead of dynamic properties removes the "red lines" in IDEs
        if ($request->input('scope') === 'personal') {
            
            // 2. Save to personalized notifications
            Notification::create([
                'user_id' => $request->input('user_id'),
                'title'   => $request->input('title'),
                'message' => $request->input('content'), // Maps form 'content' to DB 'message'
                'type'    => $request->input('type'),
                'is_read' => false
            ]);
            
            $msg = 'Personalized message sent successfully!';
            
        } else {
            
            // 3. Save to public alerts (broadcast)
            // Including author_id to match your Alert Model requirements
            Alert::create([
                'author_id' => Auth::id(), 
                'title'     => $request->input('title'),
                'content'   => $request->input('content'),
                'type'      => $request->input('type'),
            ]);
            
            $msg = 'Public broadcast dispatched successfully!';
        }

        return back()->with('success', $msg);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['is_read' => true]);
        
        return response()->json(['message' => 'Marked as read']);
    }

    /**
     * Optional: Add the search method if it was in this controller
     */
    public function searchUsers(Request $request)
    {
        $query = $request->input('q');
        return User::where('first_name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%")
                    ->orWhere('national_id', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get();
    }
}