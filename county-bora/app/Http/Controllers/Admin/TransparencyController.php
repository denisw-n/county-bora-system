<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use App\Models\TransparencySnapshot;
use App\Models\DepartmentDailyStat;
use App\Models\Ward;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransparencyController extends Controller
{
    protected $statsService;

    public function __construct(StatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    public function refreshStats()
    {
        $this->statsService->generateDepartmentalSnapshot();
        $this->statsService->refreshAnalytics();
        return back()->with('success', 'All transparency and operational stats have been updated successfully!');
    }

    public function index(Request $request)
    {
        $dailyQuery = DepartmentDailyStat::with(['department', 'ward']);

        if ($request->filled('ward_id')) {
            $dailyQuery->where('ward_id', $request->ward_id);
        }
        
        if ($request->filled('dept_id')) {
            $dailyQuery->where('dept_id', $request->dept_id);
        }

        $allStats = $dailyQuery->latest('date')->get()->groupBy('dept_id');

        // Helper to normalize JSON keys to the expected ['Dispatched', 'Resolved', 'Pending', 'In-progress']
        $normalizeStatus = function($status) {
            $key = str_replace('_', '-', strtolower($status));
            return ($key === 'in-progress') ? 'In-progress' : ucfirst($key);
        };

        // Paginated stats for the list view
        $perPage = 5;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedItems = $allStats->slice($offset, $perPage);
        
        $processedStats = $paginatedItems->map(function ($rows) use ($normalizeStatus) {
            $totals = ['Dispatched' => 0, 'Resolved' => 0, 'Pending' => 0, 'In-progress' => 0];
            foreach ($rows as $row) {
                $breakdown = json_decode($row->status_breakdown, true) ?? [];
                foreach ($breakdown as $status => $count) {
                    $key = $normalizeStatus($status);
                    if (array_key_exists($key, $totals)) {
                        $totals[$key] += $count;
                    }
                }
            }
            return [
                'dept_name' => $rows->first()->department->dept_name ?? 'Unknown',
                'wards'     => $rows->pluck('ward.name')->filter()->unique()->join(', '),
                'totals'    => $totals
            ];
        });

        $paginatedResults = new LengthAwarePaginator(
            $processedStats, 
            $allStats->count(), 
            $perPage, 
            $page, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $fullStats = $allStats->map(function ($rows) use ($normalizeStatus) {
            $totals = ['Dispatched' => 0, 'Resolved' => 0, 'Pending' => 0, 'In-progress' => 0];
            foreach ($rows as $row) {
                $breakdown = json_decode($row->status_breakdown, true) ?? [];
                foreach ($breakdown as $status => $count) {
                    $key = $normalizeStatus($status);
                    if (array_key_exists($key, $totals)) {
                        $totals[$key] += $count;
                    }
                }
            }
            return $totals;
        });

        $latestSnapshots = TransparencySnapshot::with('department')
            ->whereIn('id', function($query) {
                $query->selectRaw('MAX(id)')
                      ->from('transparency_snapshots')
                      ->groupBy('entity_id');
            })
            ->latest('snapshot_date')
            ->get();

        // 1. Calculate performance for all wards
        $allWardPerformance = DepartmentDailyStat::get()->groupBy('ward_id')->map(function ($rows) {
            $totalResolved = 0;
            $totalItems = 0;
            foreach ($rows as $row) {
                $breakdown = json_decode($row->status_breakdown, true) ?? [];
                $totalItems += array_sum($breakdown);
                foreach($breakdown as $status => $count) {
                    if (strtolower($status) === 'resolved') {
                        $totalResolved += $count;
                    }
                }
            }
            return $totalItems > 0 ? ($totalResolved / $totalItems) * 100 : 0;
        });

        $topWards = $allWardPerformance->sortByDesc(fn($score) => $score)->take(5);
        $bottomWards = $allWardPerformance->sortBy(fn($score) => $score)->take(5);

        // 2. Radar Data
        $radarDataCollection = Department::with('dailyStats')->get()->map(function ($dept) {
            $stats = $dept->dailyStats;
            $resolved = 0;
            $total = 0;
            foreach ($stats as $stat) {
                $breakdown = json_decode($stat->status_breakdown, true) ?? [];
                foreach ($breakdown as $status => $count) {
                    if (strtolower($status) === 'resolved') {
                        $resolved += $count;
                    }
                    $total += $count;
                }
            }
            return [
                'name' => $dept->dept_name,
                'efficiency' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0
            ];
        })->sortByDesc('efficiency')->take(5);

        $chartData = [
            'labels'      => $latestSnapshots->map(fn($s) => $s->department->dept_name ?? 'Unknown'),
            'performance' => $latestSnapshots->pluck('percentage'),
            'trends'      => TransparencySnapshot::latest()->take(6)->pluck('percentage')->reverse()->values(),
            'status'      => [
                'Resolved'    => $fullStats->sum('Resolved'),
                'Pending'     => $fullStats->sum('Pending'),
                'In-progress' => $fullStats->sum('In-progress'),
                'Dispatched'  => $fullStats->sum('Dispatched'),
            ],
            'radarLabels' => $radarDataCollection->pluck('name'),
            'radarData'   => $radarDataCollection->pluck('efficiency'),
            'topWardsLabels'    => $topWards->keys()->map(fn($id) => Ward::find($id)->name ?? 'Unknown'),
            'topWardsScores'    => $topWards->values(),
            'bottomWardsLabels' => $bottomWards->keys()->map(fn($id) => Ward::find($id)->name ?? 'Unknown'),
            'bottomWardsScores' => $bottomWards->values(),
        ];

        return view('admin.transparency.index', [
            'processedStats' => $paginatedResults,
            'snapshots'      => $latestSnapshots,
            'chartData'      => $chartData,
            'wards'          => Ward::orderBy('name')->get(),
            'departments'    => Department::orderBy('dept_name')->get()
        ]);
    }
}