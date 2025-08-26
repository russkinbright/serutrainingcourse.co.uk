<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'section';
    protected $fillable = ['unique_id', 'name', 'sequence'];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function courseSections()
    {
        return $this->hasMany(CourseSection::class, 'section_unique_id', 'unique_id');
    }
}