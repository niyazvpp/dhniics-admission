<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function store(Request $request)
    {
        // default browser datetime format
        $request->validate([
            'code' => 'required|string|min:2|max:4|unique:institutions,code',
            'name' => 'required|string',
            'address' => 'nullable|string',
            'quota' => 'required|numeric|min:1',
        ]);

        $institution = Institution::create($request->all());
        return response()->json(['success' => true, 'data' => $institution], 201);
    }

    public function update(Request $request, Institution $institution)
    {
        $request->validate([
            'code' => 'required|string|min:2|max:4|unique:exam_centres,code,' . $institution->id,
            'name' => 'required|string',
            'address' => 'nullable|string',
            'quota' => 'required|numeric|min:1',
        ]);

        $institution->update($request->all());
        return response()->json(['success' => true, 'data' => $institution], 200);
    }

    public function delete(Institution $institution)
    {
        // if any applicant has this exam centre, then don't delete
        if ($institution->applicants()->count('applicants.id') > 0) {
            return response()->json(['success' => false, 'message' => 'Institution has been registered already! Please change the date for exam centre or delete the application!'], 422);
        }
        $institution->delete();
        return response()->json(['success' => true], 200);
    }
}
