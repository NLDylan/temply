<?php

namespace Database\Seeders;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResumeSeeder extends Seeder
{
    /**
     * Seed example resumes for the demo user.
     */
    public function run(): void
    {
        $user = User::query()
            ->where('email', 'test@example.com')
            ->first();

        if (! $user) {
            return;
        }

        $examples = [
            [
                'title' => 'Product Designer Resume',
                'headline' => 'Crafting human-centered experiences across platforms.',
            ],
            [
                'title' => 'Senior Backend Engineer',
                'headline' => 'Building resilient APIs and scalable systems.',
            ],
            [
                'title' => 'Marketing Strategist',
                'headline' => 'Driving growth through data-informed storytelling.',
            ],
        ];

        foreach ($examples as $example) {
            $slug = Str::slug($example['title']);

            Resume::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'slug' => $slug,
                ],
                [
                    'user_id' => $user->id,
                    'title' => $example['title'],
                    'slug' => $slug,
                    'headline' => $example['headline'],
                ],
            );
        }
    }
}
