<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ResumeDuplicateController extends Controller
{
    /**
     * Duplicate the specified resume with all its sections.
     */
    public function __invoke(Request $request, Resume $resume): \Illuminate\Http\RedirectResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        // Load all related sections
        $resume->load([
            'education',
            'experience',
            'skills',
            'languages',
            'certifications',
            'projects',
            'volunteering',
            'achievements',
        ]);

        // Generate new title and slug
        $newTitle = $resume->title.' Copy';
        $baseSlug = Str::slug($newTitle);
        $slug = $baseSlug;

        // Ensure slug is unique for this user
        $counter = 1;
        while ($request->user()->resumes()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        // Create new resume
        $newResume = Resume::create([
            'user_id' => $resume->user_id,
            'template_id' => $resume->template_id,
            'title' => $newTitle,
            'slug' => $slug,
            'headline' => $resume->headline,
            'location' => $resume->location,
            'summary' => $resume->summary,
            'profile' => $resume->profile,
            'settings' => $resume->settings,
            'expires_at' => $resume->expires_at,
            'locked_at' => null, // Don't copy locked status
        ]);

        // Clone all related sections
        foreach ($resume->education as $education) {
            $newResume->education()->create($education->only([
                'institution',
                'degree',
                'field_of_study',
                'location',
                'started_on',
                'ended_on',
                'is_current',
                'description',
                'sort_order',
            ]));
        }

        foreach ($resume->experience as $experience) {
            $newResume->experience()->create($experience->only([
                'company',
                'role',
                'employment_type',
                'location',
                'started_on',
                'ended_on',
                'is_current',
                'description',
                'sort_order',
            ]));
        }

        foreach ($resume->skills as $skill) {
            $newResume->skills()->create($skill->only([
                'name',
                'category',
                'proficiency',
                'is_featured',
                'sort_order',
                'metadata',
            ]));
        }

        foreach ($resume->languages as $language) {
            $newResume->languages()->create($language->only([
                'language',
                'proficiency',
                'is_native',
                'sort_order',
                'metadata',
            ]));
        }

        foreach ($resume->certifications as $certification) {
            $newResume->certifications()->create($certification->only([
                'name',
                'issuer',
                'issued_on',
                'expires_on',
                'credential_id',
                'credential_url',
                'description',
                'sort_order',
                'metadata',
            ]));
        }

        foreach ($resume->projects as $project) {
            $newResume->projects()->create($project->only([
                'name',
                'role',
                'organization',
                'url',
                'started_on',
                'ended_on',
                'is_current',
                'description',
                'sort_order',
                'metadata',
            ]));
        }

        foreach ($resume->volunteering as $volunteering) {
            $newResume->volunteering()->create($volunteering->only([
                'organization',
                'role',
                'location',
                'started_on',
                'ended_on',
                'is_current',
                'description',
                'sort_order',
                'metadata',
            ]));
        }

        foreach ($resume->achievements as $achievement) {
            $newResume->achievements()->create($achievement->only([
                'title',
                'issuer',
                'achieved_on',
                'category',
                'url',
                'description',
                'sort_order',
                'metadata',
            ]));
        }

        return redirect()
            ->route('resumes.edit', $newResume)
            ->with('success', 'Resume duplicated successfully.');
    }
}
