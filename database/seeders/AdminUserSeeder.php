<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'              => 'Administrator',
            'username'          => 'admin',
            'email'             => 'admin@investasi.com',
            'phone'             => '081234567890',
            'password'          => Hash::make('admin123'), // Ganti di production!
            'referral_code' => strtoupper(Str::random(8)),
            'referred_by'       => null,
            'role'              => 'admin',
            'balance'           => 0,
            'status'            => 'active',
            'email_verified_at' => now(),
            'last_login'        => now(),
            'remember_token'    => Str::random(10),
        ]);

        $this->command->info('Admin users created successfully!');
    }
}
