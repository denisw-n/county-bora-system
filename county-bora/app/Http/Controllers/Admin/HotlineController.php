<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotline;
use Illuminate\Http\Request;

class HotlineController extends Controller
{
    /**
     * Display all emergency services with pagination.
     */
    public function index()
    {
        $hotlines = Hotline::latest()->paginate(10);
        return view('admin.hotlines.index', compact('hotlines'));
    }

    /**
     * Store a newly created emergency service.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        Hotline::create($validated);

        return redirect()->back()->with('success', 'Service added successfully.');
    }

    /**
     * Update the specified hotline service (Status toggle or Name/Number edit).
     */
    public function update(Request $request, Hotline $hotline)
    {
        // If the request is just a status toggle (is_active only)
        if ($request->has('is_active') && count($request->all()) <= 3) {
            $hotline->update($request->only('is_active'));
            return redirect()->back()->with('info', 'Status updated successfully.');
        }

        // Full Edit validation
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $hotline->update($validated);
        
        return redirect()->back()->with('info', 'Hotline details updated successfully.');
    }

    /**
     * Remove the specified hotline service.
     */
    public function destroy(Hotline $hotline)
    {
        $hotline->delete();
        return redirect()->back()->with('warning', 'Service has been removed.');
    }
}