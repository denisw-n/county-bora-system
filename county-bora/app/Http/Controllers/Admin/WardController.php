<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ward;
use App\Models\Report;
use Illuminate\Http\Request;

class WardController extends Controller
{
    public function index()
    {
        $wards = Ward::latest()->paginate(10);

        // Calculate reports per ward for the dashboard badges
        $wards->getCollection()->transform(function ($ward) {
            $ward->reports_count = Report::where('location', 'LIKE', '%' . $ward->name . '%')->count();
            return $ward;
        });

        return view('admin.wards.index', compact('wards'));
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