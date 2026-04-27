<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        // Check if the user is searching for a specific department
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where('dept_name', 'LIKE', "%{$searchTerm}%");
        }

        // Simple list of departments with search persistence in pagination
        $departments = $query->latest()->paginate(10)->withQueryString();
        
        return view('admin.departments.index', compact('departments'));
    }

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

    public function update(Request $request, $id)
    {
        $request->validate([
            'dept_name' => 'required|string|max:255|unique:departments,dept_name,' . $id,
        ]);

        $dept = Department::findOrFail($id);
        $dept->update(['dept_name' => $request->dept_name]);

        return back()->with('success', 'Department details updated.');
    }

    public function destroy($id)
    {
        $dept = Department::findOrFail($id);
        $dept->delete();

        return back()->with('success', 'Department removed from registry.');
    }
}