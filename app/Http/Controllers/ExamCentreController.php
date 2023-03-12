<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Models\ExamCentre;
use Illuminate\Http\Request;

class ExamCentreController extends Controller
{
    public function store(Request $request)
    {
        // default browser datetime format
        $request->validate([
            'code' => 'required|string|min:2|max:4|unique:exam_centres,code',
            'name' => 'required|string',
            'address' => 'required|string',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $centre = ExamCentre::create($request->all());
        return response()->json(['success' => true, 'data' => $centre], 201);
    }

    public function update(Request $request, ExamCentre $examCentre)
    {
        $request->validate([
            'code' => 'required|string|min:2|max:4|unique:exam_centres,code,' . $examCentre->id,
            'name' => 'required|string',
            'address' => 'required|string',
            'date_time' => 'required|date_format:Y-m-d H:i:s',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $examCentre->update($request->all());
        return response()->json(['success' => true, 'data' => $examCentre], 200);
    }

    public function delete(ExamCentre $examCentre)
    {
        // if any applicant has this exam centre, then don't delete
        if (Applicant::where('exam_centre_id', $examCentre->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Exam centre has been registered already! Please change the date for exam centre or delete the application!'], 422);
        }
        $examCentre->delete();
        return response()->json(['success' => true], 200);
    }
}
