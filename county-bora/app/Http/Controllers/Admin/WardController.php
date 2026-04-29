<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Models\Report;
use App\Models\Department;
use Illuminate\Http\Request;

class WardController extends Controller
{
    public function index()
    {
        // Using withCount is much more efficient than the previous transform loop
        $wards = Ward::withCount('reports')->latest()->paginate(10);

        return view('admin.wards.index', compact('wards'));
    }

    /**
     * THE SIFTER: Displays ward details and filters reports by department
     */
    public function show(Request $request, $id)
    {
        $ward = Ward::findOrFail($id);
        
        // Fetch departments for the dropdown menu
        $departments = Department::orderBy('dept_name', 'asc')->get();

        // Start query for reports linked to this ward
        $query = $ward->reports()->with('department');

        // Apply department filter if selected in dropdown
        if ($request->filled('dept_id')) {
            $query->where('dept_id', $request->dept_id);
        }

        $reports = $query->latest()->paginate(10);

        return view('admin.wards.show', compact('ward', 'reports', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:wards,name',
            'sub_county' => 'required|string|max:255',
        ]);

        Ward::create([
            'name' => $request->name,
            'sub_county' => $request->sub_county,
        ]);

        return back()->with('success', 'Ward registered successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:wards,name,' . $id,
            'sub_county' => 'required|string|max:255',
        ]);

        $ward = Ward::findOrFail($id);
        $ward->update([
            'name' => $request->name,
            'sub_county' => $request->sub_county,
        ]);

        return back()->with('success', 'Ward updated successfully.');
    }

    public function destroy($id)
    {
        $ward = Ward::findOrFail($id);
        $ward->delete();

        return back()->with('success', 'Ward removed from system.');
    }
}