<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CorrectionAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'stamp_correction_request_id',
        'start_time',
        'end_time',
    ];
}
