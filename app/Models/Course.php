<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'course';

    protected $fillable = [
        'unique_id',
        'title',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'slug',
        'canonical_url',
        'robots_meta',
        'schema_markup',
        'description',
        'duration',
        'enroll',
        'price',
        'image',
        'rating',
        'footer_price'
    ];

    protected $casts = [
        'unique_id' => 'string',
        'price' => 'decimal:2',
        'footer_price' => 'decimal:2',
        'enroll' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function sections()
    {
        return $this->belongsToMany(Section::class, 'course_section', 'course_id', 'section_id');
    }
}