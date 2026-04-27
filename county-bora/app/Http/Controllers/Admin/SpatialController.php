<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class SpatialController extends Controller
{
    public function index()
    {
        // Fetch reports with spatial data and status for the map [cite: 23, 30, 95]
        $reports = Report::select('id', 'category', 'latitude', 'longitude', 'status', 'priority', 'description')
            ->get();

        // Aggregate statistics for the Incident Legend [cite: 106, 140]
        $stats = [
            'pending' => $reports->where('status', 'Pending')->count(),
            'progress' => $reports->where('status', 'In Progress')->count(),
            'resolved' => $reports->where('status', 'Resolved')->count(),
            'total' => $reports->count()
        ];

        return view('admin.spatial.index', compact('reports', 'stats'));
    }
}