<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    /**
     * CITIZEN: Submit a new report with photo references
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'category'    => 'required|string',
            'description' => 'required|string',
            'ward_id'     => 'required|exists:wards,id',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'images'      => 'nullable|array',
            'images.*'    => 'image|mimes:jpeg,png,jpg|max:10240', 
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $report = Report::create([
            'user_id'     => Auth::id(), 
            'ward_id'     => $request->ward_id,
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
            'message' => 'Report submitted successfully',
            'tracking_number' => $report->tracking_number,
            'report' => $report->load('media')
        ], 201);
    }

    /**
     * CITIZEN: Fetch only the logged-in user's reports with media
     */
    public function myReports()
    {
        $reports = Report::with('media')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $reports->count(),
            'data' => $reports
        ]);
    }
}