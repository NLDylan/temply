<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::query()->firstOrNew([
            'email' => 'test@example.com',
        ]);

        if (! $user->exists) {
            $user->id = (string) Str::uuid();
        }

        $user->forceFill([
            'name' => 'Test User',
            'password' => 'password',
            'email_verified_at' => now(),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->call([
            ResumeSeeder::class,
        ]);
    }
}
