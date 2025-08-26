<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progress';
    protected $primaryKey = 'id';

    protected $fillable = [
        'learner_secret_id',
        'course_unique_id',
        'permodule',
        'perquestion',
        'is_completed',
        'expire_at',
        'progress',
        'mark',
        'total_mark',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}