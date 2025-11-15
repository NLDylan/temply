<?php

use App\Models\Resume;
use App\Models\ResumeLanguage;
use App\Models\User;

test('authenticated users can store language entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.languages.store', $resume), [
        'language' => 'English',
        'proficiency' => 'Native',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure(['language']);

    $this->assertDatabaseHas('resume_languages', [
        'resume_id' => $resume->id,
        'language' => 'English',
        'proficiency' => 'Native',
    ]);
});

test('authenticated users can update language entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $language = ResumeLanguage::query()->create([
        'resume_id' => $resume->id,
        'language' => 'English',
    ]);

    $response = $this->actingAs($user)->patchJson(
        route('resumes.languages.update', [$resume, $language]),
        [
            'language' => 'Spanish',
            'proficiency' => 'Fluent',
        ]
    );

    $response->assertOk();

    $this->assertDatabaseHas('resume_languages', [
        'id' => $language->id,
        'language' => 'Spanish',
        'proficiency' => 'Fluent',
    ]);
});

test('authenticated users can delete language entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $language = ResumeLanguage::query()->create([
        'resume_id' => $resume->id,
        'language' => 'English',
    ]);

    $response = $this->actingAs($user)->deleteJson(
        route('resumes.languages.destroy', [$resume, $language])
    );

    $response->assertOk();

    $this->assertDatabaseMissing('resume_languages', [
        'id' => $language->id,
    ]);
});

test('users cannot modify languages for other users resumes', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $otherUser->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.languages.store', $resume), [
        'language' => 'English',
    ]);

    $response->assertNotFound();
});
