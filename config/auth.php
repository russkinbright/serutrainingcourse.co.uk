<?php

return [

    'defaults' => [
        // keep your current app default; you can switch to 'admin' if you prefer
        'guard' => 'learner',
        'passwords' => 'learners',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'learner' => [
            'driver' => 'session',
            'provider' => 'learners',
        ],

        // ✅ Admin guard
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'learners' => [
            'driver' => 'eloquent',
            'model' => App\Models\Learner::class,
        ],

        // ✅ Admin provider
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class, // <- the model you created
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        'learners' => [
            'provider' => 'learners',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // ✅ Optional: admin password broker (if you need resets for admins)
        'admins' => [
            'provider' => 'admins',
            'table' => 'password_reset_tokens', // or a dedicated table if you prefer
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
