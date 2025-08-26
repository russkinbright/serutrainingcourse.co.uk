<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PQuestion extends Model
{
    protected $table = 'p_question';

    protected $fillable = [
        'unique_id',
        'practice_unique_id',
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