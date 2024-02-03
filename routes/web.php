<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\ExamCentreController;
use App\Http\Controllers\InstitutionController;
// use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('home');

// Route::get('/artisan/dev/dfdfdfdf/Gbcnxdf', function () {
//     if (!defined('STDIN')) {
//         define('STDIN', fopen('php://stdin', 'r'));
//     }
//     Artisan::call('config:cache');
//     Artisan::call('route:cache');
//     Artisan::call('view:cache');
//     Artisan::call('optimize');
//     return 'done';
// });

Route::middleware("adminOrAdmissionDate")->group(function () {
    Route::get('/hallticket/{slug}/print', [ApplicantController::class, 'hallTicket'])->name('hallticket');
    Route::get('/application/{slug}/print', [ApplicantController::class, 'applicationPrint'])->name('applicationPrint');
    Route::get('/documents/{slug}/print', [ApplicantController::class, 'documents'])->name('documents');
});

Route::middleware("admissionBetween")->group(function () {
    Route::get('/admission/apply', [ApplicantController::class, 'create'])->name('apply');
    Route::post('/admission/apply', [ApplicantController::class, 'store']);

    Route::get('/applications', [ApplicantController::class, 'applications'])->name('applications');
    Route::post('/applications', [ApplicantController::class, 'search']);

    Route::get('/admission/success', [ApplicantController::class, 'success'])->name('applied');
});

Route::middleware("admissionNotBetween")->group(function () {
    Route::get('/admission', [ApplicantController::class, 'ended'])->name('admission-ended');
});

Route::get('/admission/results', [HomeController::class, 'results'])->middleware(['resultPublished'])->name('results');
Route::post('/admission/results', [HomeController::class, 'resultShow'])->middleware(['resultPublished']);

Route::get('/admin/dashboard', [ApplicantController::class, 'index'])->middleware(['auth'])->name('dashboard');
// Route::get('/admin/applicants/status', [HomeController::class, 'applicantStatus'])->middleware(['auth'])->name('status');
Route::post('/admin/applicants/status', [HomeController::class, 'updateApplicantStatus'])->middleware(['auth'])->name('status');
Route::post('/admin/settings', [HomeController::class, 'settingsStore'])->middleware(['auth'])->name('settings');
Route::post('/admin/truncate', [ApplicantController::class, 'destroy'])->middleware(['auth'])->name('destroy');
Route::post('/admin/delete/{id}', [ApplicantController::class, 'delete'])->middleware(['auth'])->name('delete');
Route::get('/dashboard', function () {
    return redirect('/admin/dashboard');
});

Route::post('/admin/exam_centres/create', [ExamCentreController::class, 'store'])->middleware(['auth'])->name('exam_centres.create');
Route::post('/admin/exam_centres/update/{examCentre}', [ExamCentreController::class, 'update'])->middleware(['auth'])->name('exam_centres.update');
Route::post('/admin/exam_centres/delete/{examCentre}', [ExamCentreController::class, 'delete'])->middleware(['auth'])->name('exam_centres.delete');

Route::post('/admin/institutions/create', [InstitutionController::class, 'store'])->middleware(['auth'])->name('institutions.create');
Route::post('/admin/institutions/update/{institution}', [InstitutionController::class, 'update'])->middleware(['auth'])->name('institutions.update');
Route::post('/admin/institutions/delete/{institution}', [InstitutionController::class, 'delete'])->middleware(['auth'])->name('institutions.delete');

Route::post('/admin/results/import', [ApplicantController::class, 'import'])->middleware(['auth'])->name('results.import');

// Route::get('/exam-results-2022', [HomeController::class, 'marksheet'])->name('marksheet');
// Route::post('/exam-results-2022', [HomeController::class, 'marksheetPost']);

require __DIR__ . '/auth.php';
