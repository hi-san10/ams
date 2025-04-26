<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
        'total_time'
    ];

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }
}
