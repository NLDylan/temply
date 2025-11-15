<?php

namespace App\Services\Resume;

use App\Models\Resume;
use App\Models\ResumeAchievement;
use App\Models\ResumeCertification;
use App\Models\ResumeEducation;
use App\Models\ResumeExperience;
use App\Models\ResumeLanguage;
use App\Models\ResumeProject;
use App\Models\ResumeSkill;
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
            $id = $data['id'] ?? null;
            $volunteering = $id
                ? ResumeVolunteering::firstOrNew(['id' => $id])
                : new ResumeVolunteering;

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
            $id = $data['id'] ?? null;
            $achievement = $id
                ? ResumeAchievement::firstOrNew(['id' => $id])
                : new ResumeAchievement;

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

    /**
     * Sync education entries for a resume.
     *
     * @param  array<int, array<string, mixed>>  $educationData
     */
    public function syncEducation(Resume $resume, array $educationData): Collection
    {
        $existingIds = collect($educationData)->pluck('id')->filter()->toArray();
        $resume->education()->whereNotIn('id', $existingIds)->delete();

        $education = collect($educationData)->map(function (array $data, int $index) use ($resume): ResumeEducation {
            $id = $data['id'] ?? null;
            $education = $id
                ? ResumeEducation::firstOrNew(['id' => $id])
                : new ResumeEducation;

            $education->resume_id = $resume->id;
            $education->institution = $data['institution'] ?? '';
            $education->degree = $data['degree'] ?? null;
            $education->field_of_study = $data['field_of_study'] ?? null;
            $education->location = $data['location'] ?? null;
            $education->started_on = $data['started_on'] ?? null;
            $education->ended_on = $data['ended_on'] ?? null;
            $education->is_current = $data['is_current'] ?? false;
            $education->description = $data['description'] ?? null;
            $education->sort_order = $data['sort_order'] ?? $index;
            $education->save();

            return $education;
        });

        return $education;
    }

    /**
     * Sync experience entries for a resume.
     *
     * @param  array<int, array<string, mixed>>  $experienceData
     */
    public function syncExperience(Resume $resume, array $experienceData): Collection
    {
        $existingIds = collect($experienceData)->pluck('id')->filter()->toArray();
        $resume->experience()->whereNotIn('id', $existingIds)->delete();

        $experience = collect($experienceData)->map(function (array $data, int $index) use ($resume): ResumeExperience {
            $id = $data['id'] ?? null;
            $experience = $id
                ? ResumeExperience::firstOrNew(['id' => $id])
                : new ResumeExperience;

            $experience->resume_id = $resume->id;
            $experience->company = $data['company'] ?? '';
            $experience->role = $data['role'] ?? null;
            $experience->employment_type = $data['employment_type'] ?? null;
            $experience->location = $data['location'] ?? null;
            $experience->started_on = $data['started_on'] ?? null;
            $experience->ended_on = $data['ended_on'] ?? null;
            $experience->is_current = $data['is_current'] ?? false;
            $experience->description = $data['description'] ?? null;
            $experience->sort_order = $data['sort_order'] ?? $index;
            $experience->save();

            return $experience;
        });

        return $experience;
    }

    /**
     * Sync skill entries for a resume.
     *
     * @param  array<int, array<string, mixed>>  $skillsData
     */
    public function syncSkills(Resume $resume, array $skillsData): Collection
    {
        $existingIds = collect($skillsData)->pluck('id')->filter()->toArray();
        $resume->skills()->whereNotIn('id', $existingIds)->delete();

        $skills = collect($skillsData)->map(function (array $data, int $index) use ($resume): ResumeSkill {
            $id = $data['id'] ?? null;
            $skill = $id
                ? ResumeSkill::firstOrNew(['id' => $id])
                : new ResumeSkill;

            $skill->resume_id = $resume->id;
            $skill->name = $data['name'] ?? '';
            $skill->category = $data['category'] ?? null;
            $skill->proficiency = $data['proficiency'] ?? null;
            $skill->is_featured = $data['is_featured'] ?? false;
            $skill->sort_order = $data['sort_order'] ?? $index;
            $skill->metadata = $data['metadata'] ?? null;
            $skill->save();

            return $skill;
        });

        return $skills;
    }

    /**
     * Sync language entries for a resume.
     *
     * @param  array<int, array<string, mixed>>  $languagesData
     */
    public function syncLanguages(Resume $resume, array $languagesData): Collection
    {
        $existingIds = collect($languagesData)->pluck('id')->filter()->toArray();
        $resume->languages()->whereNotIn('id', $existingIds)->delete();

        $languages = collect($languagesData)->map(function (array $data, int $index) use ($resume): ResumeLanguage {
            $id = $data['id'] ?? null;
            $language = $id
                ? ResumeLanguage::firstOrNew(['id' => $id])
                : new ResumeLanguage;

            $language->resume_id = $resume->id;
            $language->language = $data['language'] ?? '';
            $language->proficiency = $data['proficiency'] ?? null;
            $language->is_native = $data['is_native'] ?? false;
            $language->sort_order = $data['sort_order'] ?? $index;
            $language->metadata = $data['metadata'] ?? null;
            $language->save();

            return $language;
        });

        return $languages;
    }

    /**
     * Sync certification entries for a resume.
     *
     * @param  array<int, array<string, mixed>>  $certificationsData
     */
    public function syncCertifications(Resume $resume, array $certificationsData): Collection
    {
        $existingIds = collect($certificationsData)->pluck('id')->filter()->toArray();
        $resume->certifications()->whereNotIn('id', $existingIds)->delete();

        $certifications = collect($certificationsData)->map(function (array $data, int $index) use ($resume): ResumeCertification {
            $id = $data['id'] ?? null;
            $certification = $id
                ? ResumeCertification::firstOrNew(['id' => $id])
                : new ResumeCertification;

            $certification->resume_id = $resume->id;
            $certification->name = $data['name'] ?? '';
            $certification->issuer = $data['issuer'] ?? null;
            $certification->issued_on = $data['issued_on'] ?? null;
            $certification->expires_on = $data['expires_on'] ?? null;
            $certification->credential_id = $data['credential_id'] ?? null;
            $certification->credential_url = $data['credential_url'] ?? null;
            $certification->description = $data['description'] ?? null;
            $certification->sort_order = $data['sort_order'] ?? $index;
            $certification->metadata = $data['metadata'] ?? null;
            $certification->save();

            return $certification;
        });

        return $certifications;
    }

    /**
     * Sync project entries for a resume.
     *
     * @param  array<int, array<string, mixed>>  $projectsData
     */
    public function syncProjects(Resume $resume, array $projectsData): Collection
    {
        $existingIds = collect($projectsData)->pluck('id')->filter()->toArray();
        $resume->projects()->whereNotIn('id', $existingIds)->delete();

        $projects = collect($projectsData)->map(function (array $data, int $index) use ($resume): ResumeProject {
            $id = $data['id'] ?? null;
            $project = $id
                ? ResumeProject::firstOrNew(['id' => $id])
                : new ResumeProject;

            $project->resume_id = $resume->id;
            $project->name = $data['name'] ?? '';
            $project->role = $data['role'] ?? null;
            $project->organization = $data['organization'] ?? null;
            $project->url = $data['url'] ?? null;
            $project->started_on = $data['started_on'] ?? null;
            $project->ended_on = $data['ended_on'] ?? null;
            $project->is_current = $data['is_current'] ?? false;
            $project->description = $data['description'] ?? null;
            $project->sort_order = $data['sort_order'] ?? $index;
            $project->metadata = $data['metadata'] ?? null;
            $project->save();

            return $project;
        });

        return $projects;
    }
}
