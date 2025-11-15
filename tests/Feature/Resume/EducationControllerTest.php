<?php

use App\Models\Resume;
use App\Models\ResumeEducation;
use App\Models\User;

test('authenticated users can store education entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.education.store', $resume), [
        'institution' => 'Test University',
        'degree' => 'Bachelor of Science',
        'field_of_study' => 'Computer Science',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure(['education']);

    $this->assertDatabaseHas('resume_educations', [
        'resume_id' => $resume->id,
        'institution' => 'Test University',
        'degree' => 'Bachelor of Science',
    ]);
});

test('authenticated users can update education entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $education = ResumeEducation::query()->create([
        'resume_id' => $resume->id,
        'institution' => 'Old University',
        'degree' => 'Bachelor',
    ]);

    $response = $this->actingAs($user)->patchJson(
        route('resumes.education.update', [$resume, $education]),
        [
            'institution' => 'New University',
            'degree' => 'Master',
        ]
    );

    $response->assertOk();
    $response->assertJsonStructure(['education']);

    $this->assertDatabaseHas('resume_educations', [
        'id' => $education->id,
        'institution' => 'New University',
        'degree' => 'Master',
    ]);
});

test('authenticated users can delete education entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $education = ResumeEducation::query()->create([
        'resume_id' => $resume->id,
        'institution' => 'Test University',
    ]);

    $response = $this->actingAs($user)->deleteJson(
        route('resumes.education.destroy', [$resume, $education])
    );

    $response->assertOk();
    $response->assertJson(['message' => 'Education entry deleted successfully.']);

    $this->assertDatabaseMissing('resume_educations', [
        'id' => $education->id,
    ]);
});

test('users cannot modify education for other users resumes', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $otherUser->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.education.store', $resume), [
        'institution' => 'Test University',
    ]);

    $response->assertNotFound();
});

test('education sync updates entries correctly', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $existing = ResumeEducation::query()->create([
        'resume_id' => $resume->id,
        'institution' => 'Old University',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.education.sync', $resume), [
        'education' => [
            [
                'id' => $existing->id,
                'institution' => 'Updated University',
            ],
            [
                'institution' => 'New University',
            ],
        ],
    ]);

    $response->assertRedirect();

    $this->assertDatabaseHas('resume_educations', [
        'id' => $existing->id,
        'institution' => 'Updated University',
    ]);
    $this->assertDatabaseHas('resume_educations', [
        'institution' => 'New University',
    ]);
    expect($resume->education()->count())->toBe(2);
});
