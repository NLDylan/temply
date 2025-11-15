<?php

use App\Models\Resume;
use App\Models\ResumeProject;
use App\Models\User;

test('authenticated users can store project entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.projects.store', $resume), [
        'name' => 'Test Project',
        'role' => 'Lead Developer',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure(['project']);

    $this->assertDatabaseHas('resume_projects', [
        'resume_id' => $resume->id,
        'name' => 'Test Project',
        'role' => 'Lead Developer',
    ]);
});

test('authenticated users can update project entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $project = ResumeProject::query()->create([
        'resume_id' => $resume->id,
        'name' => 'Old Project',
    ]);

    $response = $this->actingAs($user)->patchJson(
        route('resumes.projects.update', [$resume, $project]),
        [
            'name' => 'New Project',
            'organization' => 'New Org',
        ]
    );

    $response->assertOk();

    $this->assertDatabaseHas('resume_projects', [
        'id' => $project->id,
        'name' => 'New Project',
        'organization' => 'New Org',
    ]);
});

test('authenticated users can delete project entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $project = ResumeProject::query()->create([
        'resume_id' => $resume->id,
        'name' => 'Test Project',
    ]);

    $response = $this->actingAs($user)->deleteJson(
        route('resumes.projects.destroy', [$resume, $project])
    );

    $response->assertOk();

    $this->assertDatabaseMissing('resume_projects', [
        'id' => $project->id,
    ]);
});

test('users cannot modify projects for other users resumes', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $otherUser->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.projects.store', $resume), [
        'name' => 'Test Project',
    ]);

    $response->assertNotFound();
});
