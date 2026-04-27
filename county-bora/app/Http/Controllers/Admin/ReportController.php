<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\AuditLog;
use App\Models\Department; // Added to fetch departments if needed
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display all reports on the Admin Dashboard
     */
    public function index()
    {
        $reports = Report::with('user')->latest()->paginate(10);
        return view('admin.reports.index', compact('reports'));
    }

    /**
     * Show a specific report for review
     */
    public function show($id)
    {
        $report = Report::findOrFail($id);
        // Fetch departments so the dispatch dropdown has valid UUIDs to choose from
        $departments = \App\Models\Department::all(); 
        return view('admin.reports.show', compact('report', 'departments'));
    }

    /**
     * Update the report (Assigning priority/status/comments/dispatch)
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Updated Validation: Added dept_id check to prevent foreign key crashes
        $request->validate([
            'status' => 'required|string',
            'priority' => 'required|string',
            'dept_id' => 'nullable|exists:departments,id', // CRITICAL: Ensures the UUID exists
        ]);

        $report->update([
            'status' => $request->status,
            'priority' => $request->priority,
            'admin_comment' => $request->admin_comment,
            'audit_remarks' => $request->audit_remarks,
            'dept_id' => $request->dept_id, // Now safely receives a valid UUID or null
        ]);

        // AUTOMATIC AUDIT LOG
        $shortId = strtoupper(substr($report->id, 0, 8));
        
        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => "Updated status to {$request->status} and dispatched to Dept: {$request->dept_id} for report #{$shortId}",
            'target_id' => $report->id, 
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.reports.index')->with('success', "Report #{$shortId} updated and dispatched successfully.");
    }
}