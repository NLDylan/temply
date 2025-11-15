<?php

use App\Models\Resume;
use App\Models\ResumeAchievement;
use App\Models\ResumeEducation;
use App\Models\ResumeExperience;
use App\Models\User;

test('authenticated users can duplicate their own resumes', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Original Resume',
        'slug' => 'original-resume',
        'headline' => 'Test headline',
    ]);

    $response = $this->actingAs($user)->post(route('resumes.duplicate', $resume));

    $response->assertRedirect();
    $response->assertSessionHas('success', 'Resume duplicated successfully.');

    $this->assertDatabaseHas('resumes', [
        'user_id' => $user->id,
        'title' => 'Original Resume Copy',
        'slug' => 'original-resume-copy',
    ]);
});

test('duplicated resume includes all sections', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Original Resume',
        'slug' => 'original-resume',
    ]);

    ResumeEducation::query()->create([
        'resume_id' => $resume->id,
        'institution' => 'Test University',
        'degree' => 'Bachelor',
    ]);

    ResumeExperience::query()->create([
        'resume_id' => $resume->id,
        'company' => 'Test Company',
        'role' => 'Developer',
    ]);

    ResumeAchievement::query()->create([
        'resume_id' => $resume->id,
        'title' => 'Test Achievement',
    ]);

    $response = $this->actingAs($user)->post(route('resumes.duplicate', $resume));

    $response->assertRedirect();

    $duplicatedResume = Resume::query()
        ->where('title', 'Original Resume Copy')
        ->first();

    expect($duplicatedResume)->not->toBeNull();
    expect($duplicatedResume->education)->toHaveCount(1);
    expect($duplicatedResume->experience)->toHaveCount(1);
    expect($duplicatedResume->achievements)->toHaveCount(1);
});

test('users cannot duplicate other users resumes', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $otherUser->id,
        'title' => 'Original Resume',
        'slug' => 'original-resume',
    ]);

    $response = $this->actingAs($user)->post(route('resumes.duplicate', $resume));

    $response->assertNotFound();
});

test('guests cannot duplicate resumes', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Original Resume',
        'slug' => 'original-resume',
    ]);

    $response = $this->post(route('resumes.duplicate', $resume));

    $response->assertRedirect(route('login'));
});
