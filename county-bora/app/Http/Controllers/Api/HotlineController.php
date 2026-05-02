<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class HotlineController extends Controller
{
    /**
     * Fetch all active hotlines for the mobile user dashboard.
     * 
     * This matches the "Hotlines" card requirements in the 2x2 grid.
     */
    public function index(): JsonResponse
    {
        try {
            // 1. Filter by is_active (bool) as defined in your Model cast
            // 2. Order alphabetically by service_name for a clean mobile UI list
            $hotlines = Hotline::where('is_active', true)
                ->orderBy('service_name', 'asc')
                ->get(['id', 'service_name', 'phone_number']); // Select only needed fields for speed

            return response()->json([
                'status' => 'success',
                'message' => 'Hotlines retrieved successfully',
                'count' => $hotlines->count(),
                'data' => $hotlines
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve hotlines',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}