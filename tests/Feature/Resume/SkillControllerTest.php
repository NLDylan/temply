<?php

use App\Models\Resume;
use App\Models\ResumeSkill;
use App\Models\User;

test('authenticated users can store skill entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.skills.store', $resume), [
        'name' => 'PHP',
        'category' => 'Programming',
        'proficiency' => 'Expert',
    ]);

    $response->assertCreated();
    $response->assertJsonStructure(['skill']);

    $this->assertDatabaseHas('resume_skills', [
        'resume_id' => $resume->id,
        'name' => 'PHP',
        'category' => 'Programming',
    ]);
});

test('authenticated users can update skill entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $skill = ResumeSkill::query()->create([
        'resume_id' => $resume->id,
        'name' => 'PHP',
    ]);

    $response = $this->actingAs($user)->patchJson(
        route('resumes.skills.update', [$resume, $skill]),
        [
            'name' => 'Laravel',
            'proficiency' => 'Advanced',
        ]
    );

    $response->assertOk();

    $this->assertDatabaseHas('resume_skills', [
        'id' => $skill->id,
        'name' => 'Laravel',
        'proficiency' => 'Advanced',
    ]);
});

test('authenticated users can delete skill entries', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);
    $skill = ResumeSkill::query()->create([
        'resume_id' => $resume->id,
        'name' => 'PHP',
    ]);

    $response = $this->actingAs($user)->deleteJson(
        route('resumes.skills.destroy', [$resume, $skill])
    );

    $response->assertOk();

    $this->assertDatabaseMissing('resume_skills', [
        'id' => $skill->id,
    ]);
});

test('users cannot modify skills for other users resumes', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $otherUser->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->postJson(route('resumes.skills.store', $resume), [
        'name' => 'PHP',
    ]);

    $response->assertNotFound();
});
