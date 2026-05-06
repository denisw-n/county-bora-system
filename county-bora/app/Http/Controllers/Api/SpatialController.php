<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Ward;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SpatialController extends Controller
{
    /**
     * Fetch all wards for the registration dropdown.
     */
    public function getWards(): JsonResponse
    {
        try {
            $wards = Ward::select(['id', 'name'])->orderBy('name', 'asc')->get();

            return response()->json([
                'status' => 'success',
                'data' => $wards
            ], Response::HTTP_OK);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load wards',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Fetch pins ONLY for the authenticated user's reports.
     */
    public function getMyMapMarkers(): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ], Response::HTTP_UNAUTHORIZED);
            }
            
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
                ->where('user_id', $user->getAuthIdentifier()) // Using the method instead of property 'id' often clears P1013
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->where(function ($query) use ($retentionLimit) {
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