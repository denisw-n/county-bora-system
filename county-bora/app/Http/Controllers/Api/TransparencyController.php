<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransparencySnapshot;

class TransparencyController extends Controller
{
    public function index()
    {
        // Simply return the latest stats as JSON for your mobile app
        $stats = TransparencySnapshot::latest('snapshot_date')->take(20)->get();
        
        return response()->json([
            'status' => 'success',
            'data' => $stats
        ]);
    }
}