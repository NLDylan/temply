<?php

use App\Models\Resume;
use App\Models\User;
use Illuminate\Support\Str;

test('authenticated users can create a resume', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('resumes.store'), [
        'title' => 'Software Engineer Resume',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Resume created successfully.');

    $this->assertDatabaseHas('resumes', [
        'user_id' => $user->id,
        'title' => 'Software Engineer Resume',
        'slug' => Str::slug('Software Engineer Resume'),
    ]);
});

test('resume creation generates unique slug when duplicate exists', function (): void {
    $user = User::factory()->create();

    Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Software Engineer Resume',
        'slug' => 'software-engineer-resume',
    ]);

    $response = $this->actingAs($user)->post(route('resumes.store'), [
        'title' => 'Software Engineer Resume',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('resumes', [
        'user_id' => $user->id,
        'title' => 'Software Engineer Resume',
        'slug' => 'software-engineer-resume-1',
    ]);
});

test('resume creation requires title', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('resumes.store'), []);

    $response->assertSessionHasErrors('title');
});

test('guests cannot create resumes', function (): void {
    $response = $this->post(route('resumes.store'), [
        'title' => 'Software Engineer Resume',
    ]);

    $response->assertRedirect(route('login'));
});

test('users cannot create resumes for other users', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $response = $this->actingAs($user)->post(route('resumes.store'), [
        'title' => 'Software Engineer Resume',
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('resumes', [
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseMissing('resumes', [
        'user_id' => $otherUser->id,
        'title' => 'Software Engineer Resume',
    ]);
});
