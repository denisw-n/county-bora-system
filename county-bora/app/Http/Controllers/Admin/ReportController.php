<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\ReportMedia;
use App\Models\ReportRating; // Added for ratings
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * Display all reports with necessary data for the dispatch modals.
     */
    public function index()
    {
        $reports = Report::with(['user', 'department', 'media', 'rating'])->latest()->paginate(10);
        $departments = Department::orderBy('dept_name', 'asc')->get(); 

        return view('admin.reports.index', compact('reports', 'departments'));
    }

    /**
     * NEW: Fetch all ratings for the Admin API (Mobile/JS)
     */
    public function getRatings()
    {
        $ratings = ReportRating::with([
            'report:id,title,tracking_number,category', 
            'user:id,name'
        ])->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $ratings
        ]);
    }

    /**
     * NEW: View Ratings for Web Dashboard (Blade)
     */
    public function viewRatings()
    {
        $ratings = ReportRating::with(['report', 'user'])->latest()->paginate(15);
        return view('admin.reports.ratings', compact('ratings'));
    }

    /**
     * Self-Predicting Search logic
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $reports = Report::whereNotIn('status', ['resolved', 'rejected']) 
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
     * ACTION 2: Quick Status-Only Update
     */
    public function quickStatusUpdate(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'status' => 'required|string',
        ]);

        $report = Report::findOrFail($request->report_id);
        
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
     * Show a specific report with rating data included.
     */
    public function show($id)
    {
        // Added 'rating' to the with() array
        $report = Report::with(['user', 'department', 'ward', 'media', 'rating'])->findOrFail($id);
        $departments = Department::all(); 
        
        return view('admin.reports.show', compact('report', 'departments'));
    }

    /**
     * Media Moderation
     */
    public function deleteMedia($mediaId)
    {
        $media = ReportMedia::findOrFail($mediaId);
        
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }
        
        $media->delete();

        return back()->with('success', "Image reference removed successfully.");
    }
}