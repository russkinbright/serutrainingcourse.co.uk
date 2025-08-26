<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payment';

    protected $fillable = [
        'payment_unique_id',
        'learner_secret_id',
        'name',
        'email',
        'phone',
        'city',
        'address',
        'postal_code',
        'payment_type',
        'country',
        'course_unique_id',
        'whom',
        'quantity',
        'course_title',
        'price',
        'media',
        'message',
        'status',
        'transaction_id',
        'account_id',
        'paypal_email',
        'stripe_email',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
