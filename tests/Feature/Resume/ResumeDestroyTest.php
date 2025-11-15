<?php

use App\Models\Resume;
use App\Models\User;

test('authenticated users can delete their own resumes', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->delete(route('resumes.destroy', $resume));

    $response->assertRedirect(route('resumes.index'));
    $response->assertSessionHas('success', 'Resume deleted successfully.');

    $this->assertDatabaseMissing('resumes', [
        'id' => $resume->id,
    ]);
});

test('users cannot delete other users resumes', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $otherUser->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->actingAs($user)->delete(route('resumes.destroy', $resume));

    $response->assertNotFound();

    $this->assertDatabaseHas('resumes', [
        'id' => $resume->id,
    ]);
});

test('guests cannot delete resumes', function (): void {
    $user = User::factory()->create();
    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'title' => 'Test Resume',
        'slug' => 'test-resume',
    ]);

    $response = $this->delete(route('resumes.destroy', $resume));

    $response->assertRedirect(route('login'));
});
