<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    /**
     * Display the Departmental Registry with search and report counts.
     */
    public function index(Request $request)
    {
        $query = Department::query();

        // Search by Department Name
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('dept_name', 'LIKE', "%{$searchTerm}%");
        }

        /**
         * Uses the corrected 'reports' relationship in the Model
         * to fetch workload stats without a SQL error.
         */
        $departments = $query->withCount('reports')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * View specific Departmental Tasks and Performance.
     */
    public function show($id)
    {
        // Load the department with all its assigned reports sorted by newest
        $department = Department::with(['reports' => function($query) {
            $query->latest(); 
        }])->findOrFail($id);

        return view('admin.departments.show', compact('department'));
    }

    /**
     * Register a new Department Unit.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:departments,dept_name',
        ]);

        Department::create([
            'id' => (string) Str::uuid(),
            'dept_name' => $request->dept_name,
        ]);

        return back()->with('success', 'Department established successfully.');
    }

    /**
     * Update Department Registry Details.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:departments,dept_name,' . $id,
        ]);

        $dept = Department::findOrFail($id);
        $dept->update(['dept_name' => $request->dept_name]);

        return back()->with('success', 'Department details updated.');
    }

    /**
     * Remove Department from active service.
     */
    public function destroy($id)
    {
        $dept = Department::findOrFail($id);
        $dept->delete();

        return back()->with('success', 'Department removed from registry.');
    }
}