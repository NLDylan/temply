<?php

namespace App\Services\Resume;

use App\Models\Resume;
use App\Models\ResumeAchievement;
use App\Models\ResumeVolunteering;
use Illuminate\Support\Collection;

class ResumeSectionService
{
    /**
     * Sync volunteering entries for a resume.
     *
     * @param  array<int, array<string, mixed>>  $volunteeringData
     */
    public function syncVolunteering(Resume $resume, array $volunteeringData): Collection
    {
        $existingIds = collect($volunteeringData)->pluck('id')->filter()->toArray();
        $resume->volunteering()->whereNotIn('id', $existingIds)->delete();

        $volunteering = collect($volunteeringData)->map(function (array $data, int $index) use ($resume): ResumeVolunteering {
            $volunteering = ResumeVolunteering::firstOrNew(['id' => $data['id'] ?? null]);
            $volunteering->resume_id = $resume->id;
            $volunteering->organization = $data['organization'] ?? '';
            $volunteering->role = $data['role'] ?? null;
            $volunteering->location = $data['location'] ?? null;
            $volunteering->started_on = $data['started_on'] ?? null;
            $volunteering->ended_on = $data['ended_on'] ?? null;
            $volunteering->is_current = $data['is_current'] ?? false;
            $volunteering->description = $data['description'] ?? null;
            $volunteering->sort_order = $data['sort_order'] ?? $index;
            $volunteering->metadata = $data['metadata'] ?? null;
            $volunteering->save();

            return $volunteering;
        });

        return $volunteering;
    }

    /**
     * Sync achievement entries for a resume.
     *
     * @param  array<int, array<string, mixed>>  $achievementsData
     */
    public function syncAchievements(Resume $resume, array $achievementsData): Collection
    {
        $existingIds = collect($achievementsData)->pluck('id')->filter()->toArray();
        $resume->achievements()->whereNotIn('id', $existingIds)->delete();

        $achievements = collect($achievementsData)->map(function (array $data, int $index) use ($resume): ResumeAchievement {
            $achievement = ResumeAchievement::firstOrNew(['id' => $data['id'] ?? null]);
            $achievement->resume_id = $resume->id;
            $achievement->title = $data['title'] ?? '';
            $achievement->issuer = $data['issuer'] ?? null;
            $achievement->achieved_on = $data['achieved_on'] ?? null;
            $achievement->category = $data['category'] ?? null;
            $achievement->url = $data['url'] ?? null;
            $achievement->description = $data['description'] ?? null;
            $achievement->sort_order = $data['sort_order'] ?? $index;
            $achievement->metadata = $data['metadata'] ?? null;
            $achievement->save();

            return $achievement;
        });

        return $achievements;
    }
}
