<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    /**
     * Fetch all public alerts (broadcasts) for the citizen app.
     * These are general updates sent to the entire county.
     */
    public function index()
    {
        // We pull all alerts, ordering by most recent first
        // Optional: We include the author (admin) details if you want to show "Posted by: Admin Name"
        $alerts = Alert::with('author:id,first_name,last_name')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'alerts' => $alerts
        ], 200);
    }

    /**
     * Fetch the details of a single alert.
     * Useful if the user taps a specific alert in the feed to read the full content.
     */
    public function show($id)
    {
        try {
            $alert = Alert::with('author:id,first_name,last_name')->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'alert' => $alert
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Alert not found.'
            ], 404);
        }
    }
}