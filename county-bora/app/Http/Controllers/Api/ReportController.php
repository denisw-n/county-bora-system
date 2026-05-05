<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportMedia;
use App\Models\ReportRating; // Import the Rating model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * CITIZEN: Submit a new report.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'ward_id'     => 'nullable|exists:wards,id',
            'category'    => 'required|string',
            'description' => 'required|string',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg|max:10240', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        $report = Report::create([
            'user_id'     => $user->id, 
            'ward_id'     => $request->ward_id ?? $user->ward_id,
            'title'       => $request->title,
            'location'    => $request->location,
            'category'    => $request->category,
            'description' => $request->description,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'status'      => 'pending',
            'priority'    => 'medium',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reports', 'public');

                ReportMedia::create([
                    'report_id' => $report->id,
                    'file_path' => $path,
                    'file_type' => 'image',
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Report submitted successfully',
            'tracking_number' => $report->tracking_number,
            'report' => $report->load('media', 'ward')
        ], 201);
    }

    /**
     * CITIZEN: Fetch reports for the logged-in user.
     * Includes the 'rating' so the UI knows if it has been rated.
     */
    public function myReports(Request $request)
    {
        $query = Report::with(['media', 'ward', 'rating']) // Added 'rating'
            ->where('user_id', Auth::id())
            ->latest();

        if ($request->has('limit')) {
            $reports = $query->limit((int)$request->limit)->get();
        } else {
            $reports = $query->get();
        }

        return response()->json([
            'status' => 'success',
            'count' => $reports->count(),
            'data' => $reports
        ]);
    }

    /**
     * CITIZEN: Rate a resolved report.
     */
    public function rateReport(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'stars'   => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find the report and ensure it belongs to the authenticated user
        $report = Report::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Check if report is resolved
        if ($report->status !== 'resolved') {
            return response()->json([
                'status' => 'error',
                'message' => 'Feedback can only be provided for resolved reports.'
            ], 403);
        }

        // Check if already rated
        if ($report->rating()->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You have already provided feedback for this report.'
            ], 400);
        }

        $rating = ReportRating::create([
            'report_id' => $report->id,
            'user_id'   => Auth::id(),
            'stars'     => $request->stars,
            'comment'   => $request->comment,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you for your feedback!',
            'data' => $rating
        ], 201);
    }
}