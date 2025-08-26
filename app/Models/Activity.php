<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activity';

    protected $fillable = ['message'];

    /** tell Eloquent there is NO updated_at column */
    const UPDATED_AT = null;

    protected $casts = [
        'created_at' => 'datetime',
    ];
}

