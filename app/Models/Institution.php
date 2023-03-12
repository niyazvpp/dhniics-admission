<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'code',
        'quota',
    ];

    public function applicants()
    {
        return $this->belongsToMany(Applicant::class, 'applicant_institutions', 'institution_id', 'applicant_id');
    }

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('settings');
        });
        static::deleted(function () {
            Cache::forget('settings');
        });
        static::updated(function () {
            Cache::forget('settings');
        });
        static::created(function () {
            Cache::forget('settings');
        });
    }
}
