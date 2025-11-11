<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

test('database seeder always ensures the test user exists with known credentials', function () {
    $this->seed(DatabaseSeeder::class);

    $user = User::query()->where('email', 'test@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Test User');
    expect(Str::isUuid($user->id))->toBeTrue();
    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
    expect(Hash::check('password', $user->password))->toBeTrue();

    $this->seed(DatabaseSeeder::class);

    $users = User::query()->where('email', 'test@example.com')->get();
    expect($users)->toHaveCount(1);

    $user->refresh();
    expect($user->two_factor_secret)->toBeNull();
    expect($user->two_factor_recovery_codes)->toBeNull();
    expect($user->two_factor_confirmed_at)->toBeNull();
});
