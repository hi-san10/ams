<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'start_time',
        'end_time',
        'total_time'
    ];

    public function attendances()
        {
            return $this->hasMany(Attendance::class);
        }
}
