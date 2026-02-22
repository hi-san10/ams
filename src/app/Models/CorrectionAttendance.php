<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorrectionAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'stamp_correction_request_id',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];


    public function rests()
    {
        return $this->hasMany(CorrectionRest::class);
    }
}
