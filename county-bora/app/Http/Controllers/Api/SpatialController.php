<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SpatialController extends Controller
{
    /**
     * Fetch pins ONLY for the authenticated user's reports.
     * Includes active reports + resolved reports from the last 7 days.
     */
    public function getMyMapMarkers(): JsonResponse
    {
        try {
            $user = auth()->user();
            $retentionLimit = Carbon::now()->subDays(7);

            $reports = Report::select([
                    'id', 
                    'title', 
                    'category', 
                    'latitude', 
                    'longitude', 
                    'status', 
                    'priority', 
                    'created_at'
                ])
                ->where('user_id', $user->id) // 1. Filter by specific user
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where(function ($query) use ($retentionLimit) {
                    // 2. Filter by the same logic as Admin: Active items OR Recent Resolved
                    $query->whereIn(DB::raw('LOWER(status)'), ['pending', 'in progress', 'in_progress', 'dispatched', 'assigned'])
                          ->orWhere(function ($subQuery) use ($retentionLimit) {
                              $subQuery->where(DB::raw('LOWER(status)'), 'resolved')
                                       ->where('updated_at', '>=', $retentionLimit);
                          });
                })
                ->latest()
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Personal map markers retrieved successfully',
                'count' => $reports->count(),
                'data' => $reports
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load map data',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}