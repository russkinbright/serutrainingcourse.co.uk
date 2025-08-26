<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Learner extends Authenticatable
{
    use HasFactory;
    
    protected $table = 'learner';

    protected $fillable = [
        'secret_id',
        'name',
        'email',
        'payment_type',
        'whom',
        'card',
        'card_expiry',
        'card_code',
        'phone',
        'message',
        'password',
        'country',
        'city',
        'address',
        'postal_code',
        'question',
        'answer',
    ];

    protected $visible = [
    'id', 'secret_id', 'name', 'email', 'created_at', 'updated_at',
    'payment_type', 'whom', 'card', 'card_expiry', 'card_code', 'phone', 'message',
    'country', 'city', 'address', 'postal_code', 'question', 'answer'
];


    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    public function getAuthIdentifierName()
    {
        return 'id'; // or whatever your primary key column is
    }
}
