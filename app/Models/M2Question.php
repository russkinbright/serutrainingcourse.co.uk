<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class M2Question extends Model
{
    protected $table = 'm2_question';

    protected $fillable = [
        'unique_id',
        'mock_unique_id',
        'type',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'answer_1',
        'answer_2',
        'incorrect',
    ];
}
?>