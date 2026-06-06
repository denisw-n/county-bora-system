<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReportRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportRatingController extends Controller
{
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Prevent duplicate ratings using the $id from the URL
        $existing = ReportRating::where('report_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already rated this report.'], 403);
        }

        $rating = ReportRating::create([
            'report_id' => $id,
            'user_id' => Auth::id(),
            'stars' => $validated['stars'],
            'comment' => $validated['comment'],
        ]);

        return response()->json(['message' => 'Rating submitted successfully!', 'data' => $rating], 201);
    }
}