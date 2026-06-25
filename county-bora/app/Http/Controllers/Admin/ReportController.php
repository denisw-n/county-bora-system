<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\AuditLog;
use App\Models\Department;
use App\Models\ReportMedia;
use App\Models\ReportRating; 
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
            'report:id,title,category', 
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
     * Self-Predicting Search logic (Hardened to prevent 500 errors)
     */
    public function search(Request $request)
    {
        $input = trim($request->get('q'));

        // Return empty array instead of crashing if query is too short or empty
        if (!$input || strlen($input) < 2) {
            return response()->json([]);
        }

        $searchTerm = str_replace('NCC-', '', strtoupper($input));

        // Use get() which returns an empty collection instead of throwing an error
        $reports = Report::where('id', 'LIKE', "{$searchTerm}%")
                    ->orWhere('title', 'LIKE', "%{$input}%")
                    ->orWhere('category', 'LIKE', "%{$input}%")
                    ->select('id', 'title', 'category', 'status')
                    ->limit(5)
                    ->get();

        // Safe check to ensure we don't try to append to a null object
        if ($reports->isNotEmpty()) {
            $reports->each(function ($report) {
                // Ensure the model has the attribute before appending
                $report->append('tracking_number');
            });
        }

        return response()->json($reports);
    }

    /**
     * ACTION 1: Initial Dispatch & Subsequent Progress Updates
     */
    public function update(Request $request, $id)
    {
        // Find or Fail is fine here as it's a direct resource update
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