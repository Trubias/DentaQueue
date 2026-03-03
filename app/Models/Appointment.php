<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fullname',
        'age',
        'sex',
        'type',
        'status',
        'scheduled_at',
        'note',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
}
