<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('admin')->updateOrInsert(
            ['email' => 'admin@example.com'], // unique key
            [
                'name'       => 'Admin User',
                'secret_id'  => 10000001,
                'password'   => Hash::make('mugdho007@'),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
