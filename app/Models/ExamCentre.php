<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamCentre extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'date_time',
        'start_date',
        'end_date'
    ];
}
