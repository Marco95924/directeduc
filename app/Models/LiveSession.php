<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'age',
        'niveau',
        'sujet',
        'status',
        'peer_id_student',
        'peer_id_teacher',
    ];
}