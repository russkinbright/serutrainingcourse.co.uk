<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';
    protected $fillable = [
        'title',
        'description',
        'card_image_url',
        'background_image_url',
        'slug',
        'card_title',
        'card_image',
        'course_details_link',
    ];
}