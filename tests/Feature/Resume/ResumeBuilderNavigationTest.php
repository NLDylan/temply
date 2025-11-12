<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('authenticated users can view the resume builder', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('resume.builder'));

    $response->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Resume/Builder')
        );
});

test('guests are redirected from the resume builder', function () {
    $response = $this->get(route('resume.builder'));

    $response->assertRedirect(route('login'));
});
