<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'      => 'Admin User',
                'password'  => Hash::make('password'),
                'latitude'  => 23.8103,
                'longitude' => 90.4125,
                'is_admin'  => true,
            ]
        );

        DB::statement(
            'UPDATE users SET location = POINT(?, ?) WHERE id = ?',
            [$admin->longitude, $admin->latitude, $admin->id]
        );
    }
}
