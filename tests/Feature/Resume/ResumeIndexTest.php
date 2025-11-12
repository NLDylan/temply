<?php

use App\Models\Resume;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('authenticated users can view the resume index', function (): void {
    $user = User::factory()->create();

    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Lead Product Manager',
        'slug' => 'lead-product-manager',
        'headline' => 'Connecting product vision with customer value.',
    ]);

    $response = $this->actingAs($user)->get(route('resumes.index'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Resume/Index')
            ->has('resumes', 1)
            ->where('resumes.0.id', $resume->id)
            ->where('resumes.0.title', $resume->title)
            ->where('resumes.0.slug', $resume->slug)
        );
});

test('guests are redirected from the resume index', function (): void {
    $response = $this->get(route('resumes.index'));

    $response->assertRedirect(route('login'));
});
