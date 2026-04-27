<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alert;        // Your public broadcasts
use App\Models\Notification; // Your personalized messages
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PublicCommController extends Controller
{
    /**
     * Display a unified list of all communications.
     */
    public function index()
    {
        // 1. Fetch public alerts
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

        // 3. Merge and sort
        $allCommunications = $broadcasts->concat($personals)->sortByDesc('created_at');

        return view('admin.communication.index', compact('allCommunications'));
    }

    /**
     * Dispatch the communication based on the selected scope.
     */
    public function broadcast(Request $request)
    {
        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'scope'   => 'required|in:public,personal',
            'user_id' => 'required_if:scope,personal',
            'type'    => 'required'
        ]);

        if ($request->input('scope') === 'personal') {
            
            Notification::create([
                'user_id' => $request->input('user_id'),
                'title'   => $request->input('title'),
                'message' => $request->input('content'), 
                'type'    => $request->input('type'),
                'is_read' => false
            ]);
            
            $msg = "Personalized notification sent successfully!";
            
        } else {
            
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
     * AJAX Search for verified citizens.
     * Fixed to handle 'q' or 'query' parameters and scoped logic.
     */
    public function searchUsers(Request $request)
    {
        // Capture search term from 'q' (Select2 default) or 'query'
        $term = $request->input('q') ?? $request->input('query') ?? $request->input('search');

        if (empty($term)) {
            return response()->json([]);
        }

        // Search logic optimized for verified citizens only
        $users = User::where('role', 'citizen')
            ->where('is_verified', true)
            ->where(function($q) use ($term) {
                $q->where('first_name', 'LIKE', "%{$term}%")
                  ->orWhere('last_name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%")
                  ->orWhere('national_id', 'LIKE', "%{$term}%");
            })
            ->limit(15)
            ->get(['id', 'first_name', 'last_name', 'email', 'national_id']);

        return response()->json($users);
    }
}