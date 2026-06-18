<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DepartmentDailyStat;
use App\Models\TransparencySnapshot;
use App\Models\Department;

class TransparencyController extends Controller
{
    public function index()
    {
        // 1. Calculate Status Breakdown
        $allStats = DepartmentDailyStat::all()->groupBy('dept_id');
        
        $fullStats = $allStats->map(function ($rows) {
            $totals = ['Dispatched' => 0, 'Resolved' => 0, 'Pending' => 0, 'In-progress' => 0];
            foreach ($rows as $row) {
                $breakdown = json_decode($row->status_breakdown, true) ?? [];
                foreach ($breakdown as $status => $count) {
                    $key = str_replace('_', '-', strtolower($status));
                    $key = ($key === 'in-progress') ? 'In-progress' : ucfirst($key);
                    if (array_key_exists($key, $totals)) $totals[$key] += $count;
                }
            }
            return $totals;
        });

        // 2. Prepare Radar Data (Efficiency Index)
        $radarDataCollection = Department::with('dailyStats')->get()->map(function ($dept) {
            $stats = $dept->dailyStats;
            $resolved = 0; $total = 0;
            foreach ($stats as $stat) {
                $breakdown = json_decode($stat->status_breakdown, true) ?? [];
                foreach ($breakdown as $status => $count) {
                    if (strtolower($status) === 'resolved') $resolved += $count;
                    $total += $count;
                }
            }
            return [
                'name' => $dept->dept_name, 
                'efficiency' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0
            ];
        })->sortByDesc('efficiency'); // Removed take(5) to get all depts

        // 3. Match Admin Departmental Performance Logic
        $latestSnapshots = TransparencySnapshot::with('department')
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')->from('transparency_snapshots')->groupBy('entity_id');
            })->latest('snapshot_date')->get();

        // 4. Return JSON
        return response()->json([
            'total_issues' => $fullStats->sum(fn($s) => array_sum($s)),
            'status' => [
                'Resolved'    => $fullStats->sum('Resolved'),
                'Pending'     => $fullStats->sum('Pending'),
                'In-progress' => $fullStats->sum('In-progress'),
                'Dispatched'  => $fullStats->sum('Dispatched'),
            ],
            // Now passing Department Performance data to mirror Admin
            'performance' => $latestSnapshots->pluck('percentage'), 
            'labels'      => $latestSnapshots->map(fn($s) => $s->department->dept_name ?? 'Unknown'),
            'trends'      => TransparencySnapshot::latest()->take(6)->pluck('percentage')->reverse()->values(),
            'radarLabels' => $radarDataCollection->pluck('name'),
            'radarData'   => $radarDataCollection->pluck('efficiency'),
            'message'     => 'Transparency data retrieved successfully'
        ], 200);
    }
}