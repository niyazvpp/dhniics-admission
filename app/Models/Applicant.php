<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'address',
        'ref_no',
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

    protected $appends = ['roll_no'];

    public function examCentre()
    {
        return $this->belongsTo(ExamCentre::class);
    }

    public function institutions()
    {
        // latest applied institution will be at the end of the collection
        return $this->belongsToMany(Institution::class, 'applicant_institutions', 'applicant_id', 'institution_id')
            ->withPivot('id', 'applicant_id', 'institution_id', 'created_at')
            ->orderBy('applicant_institutions.id', 'asc');
    }

    public function allotted_institution()
    {
        return $this->belongsTo(Institution::class, 'allotment_id');
    }

    public function getRollNoAttribute()
    {
        return ((int) $this->id) + 1000;
    }
}
