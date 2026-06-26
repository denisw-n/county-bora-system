<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Department;
use App\Models\TransparencySnapshot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatsService
{
    /**
     * Generate or update performance snapshots for each department.
     * Uses updateOrCreate to ensure only one snapshot per department per day.
     */
    public function generateDepartmentalSnapshot()
    {
        try {
            // Standardize to start of day to group by date effectively
            $today = now()->startOfDay();
            $departments = Department::all();

            foreach ($departments as $dept) {
                $deptReports = Report::where('dept_id', $dept->id)->get();
                $total = $deptReports->count();
                $resolved = $deptReports->where('status', 'resolved')->count();
                $percentage = ($total > 0) ? ($resolved / $total) * 100 : 0;

                // Prevents duplication: updates existing entry for today if it exists
                TransparencySnapshot::updateOrCreate(
                    [
                        'entity_id'     => $dept->id,
                        'snapshot_date' => $today,
                    ],
                    [
                        'metric_type'    => 'department_performance',
                        'total_count'    => $total,
                        'resolved_count' => $resolved,
                        'percentage'     => $percentage,
                    ]
                );
            }
        } catch (\Exception $e) {
            Log::error("Snapshot generation failed: " . $e->getMessage());
        }
    }

    /**
     * Rebuild analytics with dynamic status grouping
     */
    public function refreshAnalytics()
    {
        try {
            DB::table('department_daily_stats')->truncate();

            // Aggregate all statuses dynamically
            $stats = Report::query()
                ->whereNotNull(['dept_id', 'ward_id'])
                ->where('dept_id', '!=', '')
                ->where('ward_id', '!=', '')
                ->select('dept_id', 'ward_id', DB::raw('DATE(updated_at) as date'), 'status', DB::raw('count(*) as count'))
                ->groupBy('dept_id', 'ward_id', 'date', 'status')
                ->get();

            // Group data into a structure: [date][dept_id][ward_id][status] = count
            $grouped = [];
            foreach ($stats as $stat) {
                $grouped[$stat->date][$stat->dept_id][$stat->ward_id][$stat->status] = $stat->count;
            }

            $insertData = [];
            foreach ($grouped as $date => $depts) {
                foreach ($depts as $dept_id => $wards) {
                    foreach ($wards as $ward_id => $statuses) {
                        $insertData[] = [
                            'id'               => (string) Str::uuid(),
                            'date'             => $date,
                            'dept_id'          => $dept_id,
                            'ward_id'          => $ward_id,
                            'status_breakdown' => json_encode($statuses),
                            'created_at'       => now(),
                            'updated_at'       => now(),
                        ];
                    }
                }
            }

            if (!empty($insertData)) {
                DB::table('department_daily_stats')->insert($insertData);
            }
        } catch (\Exception $e) {
            Log::error("Stats refresh failed: " . $e->getMessage());
        }
    }
}