<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'city',
        'dob',
        'email',
        'exam_centre_id',
        'guardian',
        'makthab',
        'makthab_years',
        'mobile',
        'mobile2',
        'name',
        'bc',
        'tc',
        'image',
        'postalcode',
        'state',
        'slug',
        'status',
        'remarks',
        'allotment_id'
    ];

    public function examCentre()
    {
        return $this->belongsTo(ExamCentre::class);
    }

    public function institutions()
    {
        return $this->belongsToMany(Institution::class, 'applicant_institutions', 'applicant_id', 'institution_id');
    }

    public function allotted_institution()
    {
        return $this->belongsTo(Institution::class, 'allotment_id');
    }
}
