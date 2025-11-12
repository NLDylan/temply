<?php

use App\Models\User;
use Illuminate\Support\Str;

test('a resume owner can update basic information', function () {
    $user = User::factory()->create();
    $resume = $user->resumes()->create([
        'title' => 'Product Designer CV',
        'slug' => Str::uuid()->toString(),
        'profile' => [
            'full_name' => 'Original Name',
            'contact_links' => [
                [
                    'id' => 'existing-link-id',
                    'label' => 'Dribbble',
                    'url' => 'https://dribbble.com/original',
                ],
            ],
        ],
    ]);

    $payload = [
        'headline' => 'Senior Product Designer',
        'location' => 'Cape Town, ZA',
        'profile' => [
            'full_name' => 'Jordan Walker',
            'email' => 'jordan@example.com',
            'phone' => '+27 82 000 0000',
            'working_rights' => 'Eligible to work in South Africa & EU',
            'contact_links' => [
                [
                    'id' => 'existing-link-id',
                    'label' => 'Dribbble',
                    'url' => 'https://dribbble.com/jordan',
                ],
                [
                    'label' => 'Portfolio',
                    'url' => 'https://jordanwalker.com',
                ],
                [
                    'label' => '',
                    'url' => '',
                ],
            ],
        ],
    ];

    $response = $this
        ->actingAs($user)
        ->patch(route('resumes.update', $resume), $payload);

    $response->assertRedirect(route('resumes.edit', $resume));

    $resume->refresh();

    expect($resume->headline)->toBe('Senior Product Designer')
        ->and($resume->location)->toBe('Cape Town, ZA')
        ->and($resume->profile['full_name'])->toBe('Jordan Walker')
        ->and($resume->profile['email'])->toBe('jordan@example.com')
        ->and($resume->profile['contact_links'])->toHaveCount(2)
        ->and($resume->profile['contact_links'][0])->toMatchArray([
            'id' => 'existing-link-id',
            'label' => 'Dribbble',
            'url' => 'https://dribbble.com/jordan',
        ])
        ->and($resume->profile['contact_links'][1]['id'])->not->toBeEmpty()
        ->and($resume->profile['contact_links'][1]['label'])->toBe('Portfolio')
        ->and($resume->profile['contact_links'][1]['url'])->toBe('https://jordanwalker.com');
});

test('users cannot update resumes they do not own', function () {
    $owner = User::factory()->create();
    $resume = $owner->resumes()->create([
        'title' => 'Marketing Manager CV',
        'slug' => Str::uuid()->toString(),
    ]);

    $response = $this
        ->actingAs(User::factory()->create())
        ->patch(route('resumes.update', $resume), [
            'headline' => 'Updated',
            'profile' => [
                'full_name' => 'Unauthorized User',
            ],
        ]);

    $response->assertNotFound();
});

test('basic information update validates input', function () {
    $user = User::factory()->create();
    $resume = $user->resumes()->create([
        'title' => 'QA Resume',
        'slug' => Str::uuid()->toString(),
    ]);

    $response = $this
        ->actingAs($user)
        ->from(route('resumes.edit', $resume))
        ->patch(route('resumes.update', $resume), [
            'headline' => str_repeat('A', 260),
            'profile' => [
                'full_name' => '',
                'email' => 'not-an-email',
                'contact_links' => [
                    [
                        'label' => 'LinkedIn',
                        'url' => 'invalid-url',
                    ],
                ],
            ],
        ]);

    $response
        ->assertRedirect(route('resumes.edit', $resume))
        ->assertSessionHasErrors([
            'headline',
            'profile.full_name',
            'profile.email',
            'profile.contact_links.0.url',
        ]);
});
