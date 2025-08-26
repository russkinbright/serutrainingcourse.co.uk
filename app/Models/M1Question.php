<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class M1Question extends Model
{
    use HasFactory;

    protected $table = 'm1_question';

    protected $fillable = [
        'unique_id',
        'mock_unique_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'option_f',
        'answer_1',
        'answer_2',
        'answer_3',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
