<?php

use App\Models\Resume;
use App\Models\ResumeExperience;
use App\Models\User;

test('authenticated users can store experience entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.experience.store', $resume), [
        'company' => 'Test Company',
        'role' => 'Software Engineer',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure(['experience']);

    $this->assertDatabaseHas('resume_experiences', [
        'resume_id' => $resume->id,
        'company' => 'Test Company',
        'role' => 'Software Engineer',
    ]);
});

test('authenticated users can update experience entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $experience = ResumeExperience::query()->create([
        'resume_id' => $resume->id,
        'company' => 'Old Company',
        'role' => 'Developer',
    ]);

    $response = $this->actingAs($user)->patchJson(
        route('resumes.experience.update', [$resume, $experience]),
        [
            'company' => 'New Company',
            'role' => 'Senior Developer',
        ]
    );

    $response->assertOk();

    $this->assertDatabaseHas('resume_experiences', [
        'id' => $experience->id,
        'company' => 'New Company',
        'role' => 'Senior Developer',
    ]);
});

test('authenticated users can delete experience entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $experience = ResumeExperience::query()->create([
        'resume_id' => $resume->id,
        'company' => 'Test Company',
    ]);

    $response = $this->actingAs($user)->deleteJson(
        route('resumes.experience.destroy', [$resume, $experience])
    );

    $response->assertOk();

    $this->assertDatabaseMissing('resume_experiences', [
        'id' => $experience->id,
    ]);
});

test('users cannot modify experience for other users resumes', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $otherUser->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.experience.store', $resume), [
        'company' => 'Test Company',
    ]);

    $response->assertNotFound();
});
