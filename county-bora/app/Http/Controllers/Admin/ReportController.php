<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\AuditLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display all reports with necessary data for the dispatch modals.
     */
    public function index()
    {
        $reports = Report::with(['user', 'department'])->latest()->paginate(10);
        $departments = Department::orderBy('dept_name', 'asc')->get(); 

        return view('admin.reports.index', compact('reports', 'departments'));
    }

    /**
     * Self-Predicting Search logic
     * UPDATED: Filters out finalized reports so they don't appear in the Quick Update console.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $reports = Report::whereNotIn('status', ['resolved', 'rejected']) // Filter out locked reports
                    ->where(function($q) use ($query) {
                        $q->where('tracking_number', 'LIKE', "%{$query}%")
                          ->orWhere('title', 'LIKE', "%{$query}%")
                          ->orWhere('category', 'LIKE', "%{$query}%");
                    })
                    ->select('id', 'tracking_number', 'title', 'category', 'status')
                    ->limit(5) 
                    ->get();

        return response()->json($reports);
    }

    /**
     * ACTION 1: Initial Dispatch & Subsequent Progress Updates
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // GATEKEEPER: Block updates to finalized records
        if ($report->status === 'resolved' || $report->status === 'rejected') {
            return redirect()->back()->withErrors(['error' => 'Action Denied: This report is finalized and locked.']);
        }

        $request->validate([
            'status' => 'required|string',
            'priority' => 'sometimes|string',
            'dept_id' => 'sometimes|exists:departments,id',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->filled('priority')) {
            $updateData['priority'] = $request->priority;
        }

        if ($request->filled('dept_id')) {
            $updateData['dept_id'] = $request->dept_id;
        }

        $report->update($updateData);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => "Update on [{$report->tracking_number}]: Status set to " . strtoupper($request->status),
            'target_id' => $report->id, 
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', "Report {$report->tracking_number} has been updated successfully.");
    }

    /**
     * ACTION 2: Quick Status-Only Update (From Search Console)
     */
    public function quickStatusUpdate(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'status' => 'required|string',
        ]);

        $report = Report::findOrFail($request->report_id);
        
        // GATEKEEPER: Block updates to finalized records
        if ($report->status === 'resolved' || $report->status === 'rejected') {
            return back()->withErrors(['error' => 'This report is finalized and cannot be updated via the Quick Console.']);
        }

        $report->update([
            'status' => $request->status,
        ]);

        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => "Quick Status Change for [{$report->tracking_number}] to: " . strtoupper($request->status),
            'target_id' => $report->id, 
            'timestamp' => now(),
        ]);

        return back()->with('success', "Report {$report->tracking_number} status updated to " . strtoupper($request->status));
    }

    /**
     * Show a specific report for review
     */
    public function show($id)
    {
        $report = Report::with(['user', 'department', 'ward'])->findOrFail($id);
        $departments = Department::all(); 
        
        return view('admin.reports.show', compact('report', 'departments'));
    }
}