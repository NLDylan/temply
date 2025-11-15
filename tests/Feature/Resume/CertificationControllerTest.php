<?php

use App\Models\Resume;
use App\Models\ResumeCertification;
use App\Models\User;

test('authenticated users can store certification entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.certifications.store', $resume), [
        'name' => 'AWS Certified',
        'issuer' => 'Amazon',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure(['certification']);

    $this->assertDatabaseHas('resume_certifications', [
        'resume_id' => $resume->id,
        'name' => 'AWS Certified',
        'issuer' => 'Amazon',
    ]);
});

test('authenticated users can update certification entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $certification = ResumeCertification::query()->create([
        'resume_id' => $resume->id,
        'name' => 'Old Certification',
    ]);

    $response = $this->actingAs($user)->patchJson(
        route('resumes.certifications.update', [$resume, $certification]),
        [
            'name' => 'New Certification',
            'issuer' => 'New Issuer',
        ]
    );

    $response->assertOk();

    $this->assertDatabaseHas('resume_certifications', [
        'id' => $certification->id,
        'name' => 'New Certification',
        'issuer' => 'New Issuer',
    ]);
});

test('authenticated users can delete certification entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $certification = ResumeCertification::query()->create([
        'resume_id' => $resume->id,
        'name' => 'Test Certification',
    ]);

    $response = $this->actingAs($user)->deleteJson(
        route('resumes.certifications.destroy', [$resume, $certification])
    );

    $response->assertOk();

    $this->assertDatabaseMissing('resume_certifications', [
        'id' => $certification->id,
    ]);
});

test('users cannot modify certifications for other users resumes', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $otherUser->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.certifications.store', $resume), [
        'name' => 'Test Certification',
    ]);

    $response->assertNotFound();
});
