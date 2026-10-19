<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@nafasdarurat.test');
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            $password = Str::password(16);
            $this->command?->warn("ADMIN_PASSWORD belum diisi di .env. Kata sandi sementara: {$password}");
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Admin Nafas Darurat', 'password' => Hash::make($password)]
        );

        $user->forceFill(['is_admin' => true])->save();
    }
}
