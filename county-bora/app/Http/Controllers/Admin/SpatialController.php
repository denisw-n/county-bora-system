<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SpatialController extends Controller
{
    public function index()
    {
        // Define the retention period ONLY for resolved reports
        $retentionLimit = Carbon::now()->subDays(7);

        /**
         * Fetch reports: 
         * 1. Active statuses (Pending, In Progress, Dispatched, Assigned) - No time limit.
         * 2. Resolved status - Only if updated within the last 7 days.
         */
        $reports = Report::select('id', 'category', 'latitude', 'longitude', 'status', 'priority', 'description', 'updated_at')
            ->where(function ($query) use ($retentionLimit) {
                // ACTIVE INCIDENTS: These will stay "for life" until moved to resolved
                $query->whereIn(DB::raw('LOWER(status)'), ['pending', 'in progress', 'in_progress', 'dispatched', 'assigned'])
                      
                      // RESOLVED INCIDENTS: These are subject to the 7-day cleanup
                      ->orWhere(function ($subQuery) use ($retentionLimit) {
                          $subQuery->where(DB::raw('LOWER(status)'), 'resolved')
                                   ->where('updated_at', '>=', $retentionLimit);
                      });
            })
            ->get();

        /**
         * Aggregate statistics for the Incident Legend
         * Normalizing the counts so that 'in_progress' and 'in progress' are counted together.
         */
        $stats = [
            'pending'    => $reports->filter(fn($r) => strtolower($r->status) === 'pending')->count(),
            
            // Group 'assigned' and 'dispatched'
            'dispatched' => $reports->filter(fn($r) => in_array(strtolower($r->status), ['dispatched', 'assigned']))->count(),
            
            // Handles both underscore and space versions found in your database
            'progress'   => $reports->filter(fn($r) => in_array(strtolower($r->status), ['in progress', 'in_progress']))->count(),
            
            'resolved'   => $reports->filter(fn($r) => strtolower($r->status) === 'resolved')->count(),
            
            'total'      => $reports->count()
        ];

        return view('admin.spatial.index', compact('reports', 'stats'));
    }
}