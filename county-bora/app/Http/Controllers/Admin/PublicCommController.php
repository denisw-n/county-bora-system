<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alert;        // Your public broadcasts
use App\Models\Notification; // Your personalized messages
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PublicCommController extends Controller
{
    /**
     * Display a unified list of all communications.
     */
    public function index()
    {
        // 1. Fetch public alerts and format them for the unified view
        $broadcasts = Alert::latest()->get()->map(function($item) {
            $item->display_scope = 'Public';
            $item->recipient = 'All Citizens';
            $item->display_message = $item->content;
            return $item;
        });

        // 2. Fetch personalized notifications with user relationship
        $personals = Notification::with('user')->latest()->get()->map(function($item) {
            $item->display_scope = 'Personal';
            $item->recipient = $item->user ? $item->user->first_name . ' ' . $item->user->last_name : 'Unknown Citizen';
            $item->display_message = $item->message;
            return $item;
        });

        // 3. Merge both collections and sort by date (newest first)
        $allCommunications = $broadcasts->concat($personals)->sortByDesc('created_at');

        return view('admin.communication.index', compact('allCommunications'));
    }

    /**
     * Dispatch the communication based on the selected scope.
     */
    public function broadcast(Request $request)
    {
        // 1. Validate the input
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'scope'   => 'required|in:public,personal',
            'user_id' => 'required_if:scope,personal',
            'type'    => 'required'
        ]);

        // Using input() removes the "red" IDE error for 'scope' and 'content'
        if ($request->input('scope') === 'personal') {
            
            // 2. Save to the notifications table
            Notification::create([
                'user_id' => $request->input('user_id'),
                'title'   => $request->input('title'),
                'message' => $request->input('content'), 
                'type'    => $request->input('type'),
                'is_read' => false
            ]);
            
            $msg = "Personalized notification sent successfully!";
            
        } else {
            
            // 3. Save to the alerts table (Public)
            Alert::create([
                'author_id' => Auth::id(), 
                'title'     => $request->input('title'),
                'content'   => $request->input('content'),
                'type'      => $request->input('type'),
            ]);
            
            $msg = "Public broadcast dispatched to all citizens!";
        }

        return back()->with('success', $msg);
    }

    /**
     * AJAX Search for citizens (National ID or Name).
     */
    public function searchUsers(Request $request)
    {
        // Using get() or input() here also satisfies the IDE
        $query = $request->input('q');
        
        return User::where('first_name', 'LIKE', "%{$query}%")
                    ->orWhere('last_name', 'LIKE', "%{$query}%")
                    ->orWhere('national_id', 'LIKE', "%{$query}%")
                    ->limit(10)
                    ->get();
    }
}