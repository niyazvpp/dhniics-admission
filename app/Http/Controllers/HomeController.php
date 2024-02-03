<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Applicant;
use App\Models\Institution;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

use function App\Providers\settings;

class HomeController extends Controller
{

    public function index()
    {
        $applicants_results = \settings('results_starting_at') && Carbon::today()->between(\settings('results_starting_at'), \settings('results_ending_at'));
        return view('home', ['title' => 'Home', 'robots' => 'index,follow', 'results' => $applicants_results]);
    }

    public function settingsStore(Request $request)
    {
        $rules = [
            'header' => 'required|string|max:255',
            'starting_at' => 'required|date',
            'ending_at' => 'required|date|after_or_equal:starting_at',
            'results_starting_at' => 'required|date|after:ending_at',
            'results_ending_at' => 'required|date|after:results_starting_at',
            'dob_starting_at' => 'required|date',
            'dob_ending_at' => 'required|date|after:dob_starting_at',
            'site_name' => 'required|string',
            'academic_year' => 'required|string',
            'header_first_line' => 'required|string',
            'header_second_line' => 'required|string',
            'address_and_contact' => 'required|string',
            'selectable_max' => 'required|numeric|min:0|max:' . Institution::count('id'),
            'selectable_min' => 'required|numeric|min:1|max:selectable_max',
            'description' => 'required|string',
            'admission_result_selected_template' => 'required|string',
            'admission_result_not_selected_template' => 'required|string',
            'logo' => 'nullable|image|mimes:png|max:250',
            'background_image' => 'nullable|image|mimes:jpg|max:1024',
        ];
        $request->validate($rules);

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $path = public_path('img/logo.png');
            file_put_contents($path, file_get_contents($logo));
        }

        if ($request->hasFile('background_image')) {
            $background_image = $request->file('background_image');
            $path = public_path('img/campus.jpg');
            file_put_contents($path, file_get_contents($background_image));
        }

        unset($rules['logo']);
        unset($rules['background_image']);

        foreach (array_keys($rules) as $key) {
            Setting::updateOrCreate(['name' => $key], ['value' => $request->get($key)]);
        }

        return back()->with('message', 'Settings Updated Successfully!');
    }

    public function applicantStatus()
    {
        $applicationsAll = Applicant::where('remarks', '<>', 'deleted')
            ->orWhereNull('remarks')
            ->select('id', 'name', 'status')
            ->get();
        return view('admin.status', ['applicantsAll' => $applicationsAll]);
    }

    public function updateApplicantStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
        ]);
        $options = \settings('selectable_max');
        foreach ($request->ids as $id => $status) {
            if ($options > 0) {
                Applicant::where('id', $id)->update([
                    'allotment_id' => $status,
                    'status' => ($status ?? 0) > 0 ? 1 : 0,
                ]);
            } else {
                Applicant::where('id', $id)->update(['status' => $status]);
            }
        }
        return back()->with('message', count($request->ids) . ' Results Updated Successfully!');
    }

    public function results()
    {
        return view('results');
    }

    public function resultShow(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric',
            'dob' => 'required|date',
        ]);
        $id = ((int) $request->id) - 1000;
        $applicant = Applicant::where(function ($query) {
            return $query->where('remarks', '<>', 'deleted')
                ->orWhereNull('remarks');
        })
            ->where('id', $id)->where('dob', $request->dob)
            ->with(['examcentre', 'allotted_institution'])
            ->select('name', 'dob', 'id', 'status', 'exam_centre_id', 'allotment_id', 'ref_no')
            ->first();

        if ($applicant) {
            return redirect()->route('results')->with(['result' => $applicant, 'code' => $applicant->ref_no]);
        }

        return back()->with('message', 'Incorrect Entry')->with('type', 'error');
    }

    public function marksheet()
    {

        // $file = file_get_contents(Storage::path('marksheet.json'));
        // echo '<pre>'; print_r(json_decode($file)); echo '</pre>';
        // exit;

        $student = false;
        if (session('result_ad_no') && session('result_roll_no')) {
            $student = $this->getStudentByAdNoAndRoll(session('result_ad_no'), session('result_roll_no'));
        }
        return view('marksheet', compact('student'));
    }

    private function getStudentByAdNoAndRoll($ad_no, $roll_no)
    {
        $file = file_get_contents(Storage::path('marksheet.json'));
        $presentData = false;
        if ($file) {
            $students = json_decode($file);
            $student = array_filter($students, function ($student) use ($ad_no, $roll_no) {
                return $student->ad_no == $ad_no && $student->roll_no == $roll_no;
            });
            if (count($student)) {
                return array_values($student)[0];
            }
        }
        return false;
    }

    public function marksheetPost(Request $request)
    {
        $request->validate([
            'ad_no' => 'required',
            'roll_no' => 'required',
        ]);

        $request->roll_no = strtoupper($request->roll_no);

        if ($this->getStudentByAdNoAndRoll($request->ad_no, $request->roll_no)) {
            session()->flash('result_ad_no', $request->ad_no);
            session()->flash('result_roll_no', $request->roll_no);
            return redirect()->route('marksheet');
        }

        return back()->withErrors(['error' => 'Please Enter Correct Admission No and Roll No.']);
    }
}
