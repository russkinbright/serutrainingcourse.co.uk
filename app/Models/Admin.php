<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // <-- this
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Hash;

class Admin extends Authenticatable   // <-- extend Authenticatable
{
    use HasFactory;

    protected $table = 'admin';

    protected $fillable = ['name','secret_id','email','password'];

    protected $hidden = ['password'];

    public $incrementing = true;
    protected $keyType = 'int';

    protected $casts = [
        'secret_id'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // optional: auto-hash password
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) =>
                blank($value) ? null : (Hash::needsRehash($value) ? Hash::make($value) : $value)
        );
    }
}
