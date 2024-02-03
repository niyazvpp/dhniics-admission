<?php

namespace App\Http\Controllers;

use App\Models\Applicant;
use App\Http\Requests\StoreApplicantRequest;
use App\Http\Requests\UpdateApplicantRequest;
use App\Imports\ApplicantsImport;
use App\Models\ApplicantInstitution;
use App\Models\ExamCentre;
use App\Models\Institution;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use setasign\Fpdi\Fpdi;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ApplicantController extends Controller
{

    public function index()
    {
        $applications = Applicant::where('remarks', '<>', 'deleted')
            ->orWhereNull('remarks')
            ->paginate(100);
        $applicationsAll = Applicant::with(['institutions', 'allotted_institution'])->where('remarks', '<>', 'deleted')
            ->orWhereNull('remarks')
            ->get();
        $centres = ExamCentre::all();
        $institutions = Institution::all();
        return view('admin.dashboard', ['applications' => $applications, 'institutions' => $institutions, 'applicationsAll' => $applicationsAll, 'examcentres' => $centres]);
    }

    public function applications(StoreApplicantRequest $request)
    {
        $cookie = $request->cookie('applied_applications_list');

        try {
            $cookie = $cookie ? json_decode($cookie) : [];
        } catch (Exception $e) {
            $cookie = [];
        }

        $applications = Applicant::where('remarks', '<>', 'deleted')
            ->orWhereNull('remarks')
            ->whereIn('slug', $cookie)
            ->paginate(25);
        return view('applications', ['applications' => $applications]);
    }

    public function search(StoreApplicantRequest $request)
    {
        $applicant = Applicant::where([
            'mobile' => $request->mobile,
            'dob' => $request->dob
        ])->whereNull('remarks')
            ->first();

        if (!$applicant) {
            return back()->with([
                'type' => 'error',
                'message' => 'No application found with the given details.'
            ]);
        }

        $cookie = $request->cookie('applied_applications_list');

        try {
            $cookie = $cookie ? json_decode($cookie) : [];
        } catch (Exception $e) {
            $cookie = [];
        }

        $cookie[] = $applicant->slug;

        Cookie::queue('applied_applications_list', json_encode($cookie), (24 * 7 * 60 * 365));

        return $request->wantsJson() ? response()->json([
            'status' => 'success',
            'redirect' => route('applications')
        ])
            : redirect()->route('applications')->with([
                'type' => 'success',
                'message' => 'Application added to Your Applications List. Please View Below.'
            ]);
    }

    public function success(StoreApplicantRequest $request)
    {
        if (!$request->cookie('hallticket_slug') && !$request->cookie('application_slug')) {
            abort(404);
        }
        $slug = $request->cookie('application_slug') ?: $request->cookie('hallticket_slug');
        if (!($data = Applicant::where('slug', $slug)->first())) {
            abort(404);
        }
        return view('success', ['data' => $data, 'title' => 'Admission | ' . $data->name]);
    }

    public function applicationPrint(UpdateApplicantRequest $request, $slug)
    {
        $data = Applicant::where('slug', $slug)->first();
        $cookie = $request->cookie('applied_applications_list');

        try {
            $cookie = $cookie ? json_decode($cookie) : [];
        } catch (Exception $e) {
            $cookie = [];
        }

        if (!$data || (!Auth::check() && $request->cookie('hallticket_slug') != $slug && !in_array($slug, $cookie))) {
            abort(404);
            return false;
        }

        // initiate FPDI
        $pdf = new Fpdi(/*'P','mm','A4'*/);
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetMargins(0, 0, 0);
        // add a page
        $pdf->AddPage();
        // set the source file
        $pdf->setSourceFile(storage_path('app/Niics Form.pdf'));
        // import page 1
        $tplIdx = $pdf->importPage(1);
        $pdf->SetMargins(0, 0, 0);
        // use the imported page and place it at position 10,10 with a width of 100 mm

        $sizes = $pdf->getImportedPageSize($tplIdx);
        $pdf->useImportedPage($tplIdx, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());

        // now write some text above the imported page
        $pdf->SetFont('Helvetica');
        $pdf->setFontSize(10.5);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetXY(28, 15);
        $pdf->Write(0, $data->roll_no);

        $pdf->SetXY(168.5, 15);
        $pdf->Write(0, $data->ref_no);

        //insert image
        $filename = $data->slug . '-image.' . $data->image;
        $image = storage_path('app/uploads/image/' . $filename);
        $pdf->Image($image, 161, 63, 36.5, 40.5, $data->image);

        $pdf->setFontSize(9);

        $pdf->SetXY(110, 119.5);
        $pdf->Write(0, strtoupper($data->name));

        $pdf->SetXY(110, 129);
        $pdf->Write(0, strtoupper($data->guardian));

        $pdf->SetXY(110, 138.15);
        $pdf->Write(0, Carbon::createFromFormat('Y-m-d', $data->dob)->format('d/m/Y'));

        $place = explode(',', $data->address)[0];
        $pdf->SetXY(110, 147.5);
        $pdf->Write(0, strtoupper($place));

        $district = $data->city . ', ' . $data->state;
        $pdf->SetXY(110, 157);
        $pdf->Write(0, strtoupper($district));

        $pdf->SetXY(110, 166);
        $pdf->Write(0, substr(strtoupper($data->address), 0, 48));

        if (strlen($data->address) > 50) {
            $pdf->SetXY(110, 172.95);
            $pdf->Write(0, substr(strtoupper($data->address), 49, 48));
        }

        $pdf->SetXY(110, 180);
        $pdf->Write(0, $data->mobile . ' , ' . $data->email);

        $admission_options = $data->institutions->pluck('name')->toArray();

        $pdf->SetXY(110, 208.65);
        $pdf->Write(0, strtoupper($admission_options[0]));

        if (isset($admission_options[1])) {
            $pdf->SetXY(110, 216.15);
            $pdf->Write(0, strtoupper($admission_options[1]));
        }

        if (isset($admission_options[2])) {
            $pdf->SetXY(110, 224);
            $pdf->Write(0, strtoupper($admission_options[2]));
        }

        if (isset($admission_options[3])) {
            $pdf->SetXY(110, 231.5);
            $pdf->Write(0, strtoupper($admission_options[3]));
        }

        $pdf->Output("I", 'application-form-' . $data->name . '.pdf');
        exit;
    }

    public function hallTicket(UpdateApplicantRequest $request, $slug)
    {
        $data = Applicant::where('slug', $slug)->first();
        $cookie = $request->cookie('applied_applications_list');

        try {
            $cookie = $cookie ? json_decode($cookie) : [];
        } catch (\Exception $e) {
            $cookie = [];
        }

        if (!$data || (!Auth::check() && $request->cookie('hallticket_slug') != $slug && !in_array($slug, $cookie))) {
            abort(404);
            return false;
        }

        // initiate FPDI
        $pdf = new Fpdi('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false, 0);
        $pdf->SetMargins(0, 0, 0);
        // add a page
        $pdf->AddPage();
        $pdf->SetMargins(0, 0, 0);
        // set the source file
        $pdf->setSourceFile(storage_path('app/Niics Hall Ticket.pdf'));
        // import page 1
        $tplIdx = $pdf->importPage(1);
        $pdf->SetMargins(0, 0, 0);
        // use the imported page and place it at position 10,10 with a width of 100 mm

        $sizes = $pdf->getImportedPageSize($tplIdx);
        $pdf->useImportedPage($tplIdx, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
        $pdf->SetMargins(0, 0, 0);

        // now write some text above the imported page
        $pdf->SetFont('Helvetica');
        $pdf->setFontSize(10.5);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetXY(28, 15);
        $pdf->Write(0, $data->roll_no);

        $pdf->SetXY(169, 15);
        $pdf->Write(0, $data->ref_no);

        //insert image
        $filename = $data->slug . '-image.' . $data->image;
        $image = storage_path('app/uploads/image/' . $filename);
        $pdf->Image($image, 161, 72.5, 36.5, 46.5, $data->image);

        $pdf->setFontSize(10.5);

        $pdf->SetXY(68.5, 85);
        $pdf->Write(0, strtoupper($data->name));

        $pdf->SetXY(68.5, 92.5);
        $pdf->Write(0, strtoupper($data->guardian));

        $pdf->SetXY(68.5, 100.5);
        $pdf->Write(0, Carbon::createFromFormat('Y-m-d', $data->dob)->format('d/m/Y'));

        $pdf->SetXY(68.5, 108.25);
        $pdf->Write(0, strtoupper(explode(',', $data->address)[0]));

        $pdf->SetXY(68.25, 116);
        $pdf->Write(0, strtoupper($data->city . ', ' . $data->state));

        $pdf->Output("I", 'hall-ticket-' . $data->name . '.pdf');
        exit;
    }

    public function create()
    {
        $year = settings('academic_year');
        $centres = ExamCentre::all();
        $institutions = Institution::all();
        return view('apply', ['title' => "Application Form - Admission $year ", 'robots' => 'index,follow', 'description' => settings('description'), 'examcentres' => $centres, 'institutions' => $institutions]);
    }

    public function documents(UpdateApplicantRequest $request, $slug)
    {
        $data = Applicant::where('slug', $slug)->first();
        $cookie = $request->cookie('applied_applications_list');

        try {
            $cookie = $cookie ? json_decode($cookie) : [];
        } catch (Exception $e) {
            $cookie = [];
        }

        if (!$data || (!Auth::check() && $request->cookie('hallticket_slug') != $slug && !in_array($slug, $cookie))) {
            abort(404);
            return false;
        }

        $pdf = new Fpdi('P', 'mm', 'A4');

        $pdf->SetAutoPageBreak(false, 0);

        foreach (['bc', 'tc'] as $doc) {

            if ($data->$doc == 'pdf') {

                // set the source file
                $filename = $data->slug . "-{$doc}." . $data->$doc;
                $sourcePath = storage_path("app/uploads/{$doc}/" . $filename);
                $pages = $pdf->setSourceFile($sourcePath);

                for ($i = 1; $i <= $pages; $i++) {
                    // add a page
                    $pdf->AddPage();
                    // import page 1
                    $tplIdx = $pdf->importPage($i);

                    $sizesI = $pdf->getImportedPageSize($tplIdx);
                    $sizes = [$pdf->GetPageWidth(), $pdf->GetPageHeight()];
                    $sizes[] = $sizesI['width'];
                    $sizes[] = $sizesI['height'];
                    $bestSize = $this->bestSize($sizes);
                    $x = ($sizes[0] - $bestSize[0]) / 2;
                    $y = ($sizes[1] - $bestSize[1]) / 2;

                    $pdf->useImportedPage($tplIdx, $x, $y, $bestSize[0], $bestSize[1]);
                }
            } else if ($data->$doc) {

                // add a page
                $pdf->AddPage();
                //insert image
                $filename = $data->slug . "-{$doc}." . $data->$doc;
                $image = storage_path("app/uploads/{$doc}/" . $filename);
                list($width, $height) = getimagesize($image);
                $sizes = [$pdf->GetPageWidth() - 2, $pdf->GetPageHeight() - 2];
                $sizes[] = $width;
                $sizes[] = $height;
                $bestSize = $this->bestSize($sizes);
                $x = ($sizes[0] - $bestSize[0]) / 2 + 1;
                $y = ($sizes[1] - $bestSize[1]) / 2 + 1;
                $pdf->Image($image, $x, $y, $bestSize[0], $bestSize[1], $data->$doc);
            }
        }
        $pdf->Output("I", 'submitted-docs-' . $data->name . '.pdf');
        exit;
    }

    public function bestSize($array)
    {
        list($lWidth, $lHeight, $width, $height) = $array;
        $widthRatio = $lWidth / $width;
        $heightRatio = $lHeight / $height;
        $bestRatio = min($widthRatio, $heightRatio);
        $newWidth = $width * $bestRatio;
        $newHeight = $height * $bestRatio;
        return [$newWidth, $newHeight];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreApplicantRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreApplicantRequest $request)
    {
        $dob_start_date = settings('dob_starting_at');
        $dob_end_date = settings('dob_ending_at');
        $min = settings('selectable_min');
        $max = settings('selectable_max');
        $rules = [
            'address' => 'bail|required|string|max:255|min:4',
            'bc' => 'bail|required|file|mimes:jpg,jpeg,png,pdf|max:1024',
            'city' => 'bail|required|max:255',
            'declare' => 'bail|required|accepted',
            'dob' => 'bail|required|date|before_or_equal:' . $dob_end_date . '|after_or_equal:' . $dob_start_date,
            'email' => 'bail|required|email|max:255',
            'exam_centre' => 'bail|required|numeric',
            'guardian' => 'bail|required|max:255|min:4',
            'image' => 'bail|required|file|mimes:jpg,jpeg,png,pdf|max:512',
            'makthab' => 'bail|required|in:Yes,No',
            'makthab_years' => 'bail|nullable|required_if:makthab,Yes|numeric|min:1|max:10',
            'mobile' => 'bail|required|numeric|digits:10',
            'mobile2' => 'bail|required|numeric|digits:10',
            'name' => 'bail|required|string|max:255|min:5',
            'postalcode' => 'bail|required|numeric|digits:6',
            'state' => 'bail|required|max:255|min:3',
            'tc' => 'bail|nullable|file|mimes:jpg,jpeg,png,pdf|max:1024',
            'admission_options' => ['bail', $max > 0 ? 'required' : 'nullable', 'array', 'exists:institutions,id', 'min:' . $min, 'max:' . $max],
        ];
        $request->validate($rules);

        $slug = Str::slug($request->name);

        while (count(Applicant::where('slug', $slug)->get())) {
            $slug .= '-' . Str::random(4);
        }

        $applicant = new Applicant;

        foreach (['bc', 'tc', 'image'] as $photo) {

            if (!$request->$photo) {
                continue;
            }

            $file = $request->file($photo);

            $extension = $file->extension();

            $applicant->$photo = $extension;

            $filename = $slug . '-' . $photo . '.' . $extension;
            $file->storeAs('uploads/' . $photo . '/', $filename);
        }

        if (!is_numeric($request->makthab_years) || strtolower($request->makthab) != 'yes') {
            $request->makthab_years = null;
        }

        foreach ($rules as $key => $value) {
            if (!in_array($key, ['tc', 'bc', 'image', 'declare', 'exam_centre', 'admission_options'])) {
                $applicant->$key = $request->$key;
            }
        }

        $exam_centre_code = ExamCentre::findOrFail($request->exam_centre, ['code'])->code;
        $centre_applications_count = (Applicant::where('exam_centre_id', $request->exam_centre)->count()) + 100;

        $applicant->ref_no = $exam_centre_code . '/' . ($centre_applications_count + 1) . '/' . date('Y');
        $applicant->slug = $slug;
        $applicant->exam_centre_id = $request->exam_centre;

        $applicant->save();
        if ($max > 0) {
            $applicant->institutions()->sync($request->admission_options);
        }

        $cookie = $request->cookie('applied_applications_list');

        try {
            $cookie = $cookie ? json_decode($cookie) : [];
        } catch (Exception $e) {
            $cookie = [];
        }

        $cookie[] = $slug;
        $request->session()->flash('message', 'Application Form Submitted Successfully!');

        $cookiesNeeded = [
            'applied_applications_list' => json_encode($cookie),
            'hallticket_slug' => $slug,
            'application_slug' => $slug
        ];

        foreach ($cookiesNeeded as $name => $value) {
            Cookie::queue($name, $value, (24 * 7 * 60 * 365));
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $request->wantsJson() ? response()->json([
            'status' => 'success',
            'redirect' => route('applied')
        ])
            : redirect()->route('applied');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Applicant  $applicant
     * @return \Illuminate\Http\Response
     */
    public function show(Applicant $applicant)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Applicant  $applicant
     * @return \Illuminate\Http\Response
     */
    public function edit(Applicant $applicant)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateApplicantRequest  $request
     * @param  \App\Models\Applicant  $applicant
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateApplicantRequest $request, Applicant $applicant)
    {
        //
    }

    public function delete($id)
    {

        $applicant = Applicant::findOrFail($id);
        $applicant->remarks = 'deleted';
        $applicant->save();

        return redirect()->route('dashboard')->with('message', 'Application Deleted Successfully!');
    }

    public function destroy(UpdateApplicantRequest $request, Applicant $applicant)
    {
        if (!Hash::check($request->password, $request->user()->password)) {
            return back()->withErrors([
                'password' => ['Incorrect Password!']
            ])->with([
                'message' => 'Incorrect Password. Please Retry!',
                'type' => 'error'
            ]);
        }
        $applicant = Applicant::with('institutions')->get();
        if (!($count = count($applicant))) {
            return redirect()->route('dashboard')->with([
                'message' => 'No Data Found!',
                'type' => 'error'
            ]);
        }
        $json = $applicant->toJson();

        $json_new = 1;

        while (file_exists(storage_path('app/deleted/database/' . $json_new . '.json'))) {
            $json_new++;;
        }

        $json_new .= '.json';
        Storage::put('deleted/database/' . $json_new, $json);

        $new_uploads = 1;

        while (file_exists(storage_path('app/deleted/uploads/' . $new_uploads))) {
            $new_uploads++;
        }

        if (Storage::exists('uploads'))
            Storage::move('uploads', 'deleted/uploads/' . $new_uploads);

        // disable foreign key checks first
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        ApplicantInstitution::truncate();
        Applicant::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $count .=  'Application' . ($count > 1 ? 's' : '');
        return redirect()->route('dashboard')->with('message', $count . ' Deleted!');
    }

    public function ended()
    {
        return view('admission-ended');
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx'
        ]);

        $file = $request->file('excel_file');
        Excel::import(new ApplicantsImport, $file);

        return redirect()->route('dashboard')->with('message', 'Data Imported Successfully!');
    }
}
