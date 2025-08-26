<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    protected $table = 'course_section';

    protected $fillable = [
        'course_unique_id',
        'section_unique_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_unique_id', 'unique_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_unique_id', 'unique_id');
    }
}