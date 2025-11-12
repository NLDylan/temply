<?php

use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;

test('authenticated users can view the resume builder', function () {
    $user = User::factory()->create();
    $resume = $user->resumes()->create([
        'title' => 'Product Designer CV',
        'slug' => Str::uuid()->toString(),
    ]);

    $response = $this->actingAs($user)->get(route('resumes.edit', $resume));

    $response->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Resume/Builder')
            ->where('resume.id', $resume->id)
            ->where('resume.title', $resume->title)
        );
});

test('guests are redirected from the resume builder', function () {
    $owner = User::factory()->create();
    $resume = $owner->resumes()->create([
        'title' => 'Product Designer CV',
        'slug' => Str::uuid()->toString(),
    ]);

    $response = $this->get(route('resumes.edit', $resume));

    $response->assertRedirect(route('login'));
});

test('users cannot access resumes they do not own', function () {
    $owner = User::factory()->create();
    $resume = $owner->resumes()->create([
        'title' => 'Product Designer CV',
        'slug' => Str::uuid()->toString(),
    ]);

    $response = $this
        ->actingAs(User::factory()->create())
        ->get(route('resumes.edit', $resume));

    $response->assertNotFound();
});
