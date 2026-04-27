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
        
        // Fix: Fetching departments so the modal dropdowns don't crash the view
        $departments = Department::orderBy('dept_name', 'asc')->get(); 

        return view('admin.reports.index', compact('reports', 'departments'));
    }

    /**
     * Show a specific report for review
     */
    public function show($id)
    {
        $report = Report::with(['user', 'department'])->findOrFail($id);
        $departments = Department::all(); 
        
        return view('admin.reports.show', compact('report', 'departments'));
    }

    /**
     * Update the report (Assigning priority/status/comments/dispatch)
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        $request->validate([
            'status' => 'required|string',
            'priority' => 'required|string',
            'dept_id' => 'nullable|exists:departments,id', 
        ]);

        $report->update([
            'status' => $request->status,
            'priority' => $request->priority,
            'admin_comment' => $request->admin_comment,
            'audit_remarks' => $request->audit_remarks,
            'dept_id' => $request->dept_id,
        ]);

        // AUTOMATIC AUDIT LOG
        $shortId = strtoupper(substr($report->id, 0, 8));
        
        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => "Dispatched report #{$shortId} to Department ID: {$request->dept_id} with status: {$request->status}",
            'target_id' => $report->id, 
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', "Report #{$shortId} has been successfully updated and dispatched.");
    }
}