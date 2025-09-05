<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if admin user already exists
        $user = User::where('email', 'admin@modermblog.com')->first();
        
        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'email' => 'admin@modermblog.com',
                'password' => Hash::make('password'),
            ]);
        }
        
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }
    }
}