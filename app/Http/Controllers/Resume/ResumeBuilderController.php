<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ResumeBuilderController extends Controller
{
    /**
     * Display the resume builder for the given resume.
     */
    public function __invoke(Request $request, Resume $resume): Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

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

        $profile = array_merge(
            [
                'full_name' => $request->user()->name ?? '',
                'email' => $request->user()->email ?? null,
                'phone' => null,
                'working_rights' => null,
                'contact_links' => [],
            ],
            $resume->profile ?? [],
        );

        $profile['full_name'] = $profile['full_name'] ?: ($request->user()->name ?? '');
        $profile['email'] = $profile['email'] ?: ($request->user()->email ?? null);
        $profile['contact_links'] = collect($profile['contact_links'] ?? [])
            ->map(function (array $link): array {
                return [
                    'id' => $link['id'] ?? (string) Str::uuid(),
                    'label' => $link['label'] ?? '',
                    'url' => $link['url'] ?? '',
                ];
            })
            ->values()
            ->all();

        return Inertia::render('Resume/Builder', [
            'resume' => [
                'id' => $resume->id,
                'user_id' => $resume->user_id,
                'template_id' => $resume->template_id,
                'title' => $resume->title,
                'slug' => $resume->slug,
                'headline' => $resume->headline,
                'location' => $resume->location,
                'summary' => $resume->summary,
                'profile' => $profile,
                'settings' => $resume->settings,
                'expires_at' => $resume->expires_at?->toIso8601String(),
                'locked_at' => $resume->locked_at?->toIso8601String(),
                'created_at' => $resume->created_at?->toIso8601String(),
                'updated_at' => $resume->updated_at?->toIso8601String(),
                'education' => $resume->education->map(fn ($edu) => [
                    'id' => $edu->id,
                    'resume_id' => $edu->resume_id,
                    'institution' => $edu->institution,
                    'degree' => $edu->degree,
                    'field_of_study' => $edu->field_of_study,
                    'location' => $edu->location,
                    'started_on' => $edu->started_on?->toDateString(),
                    'ended_on' => $edu->ended_on?->toDateString(),
                    'is_current' => $edu->is_current,
                    'description' => $edu->description,
                    'sort_order' => $edu->sort_order,
                ])->values()->all(),
                'experience' => $resume->experience->map(fn ($exp) => [
                    'id' => $exp->id,
                    'resume_id' => $exp->resume_id,
                    'company' => $exp->company,
                    'role' => $exp->role,
                    'employment_type' => $exp->employment_type,
                    'location' => $exp->location,
                    'started_on' => $exp->started_on?->toDateString(),
                    'ended_on' => $exp->ended_on?->toDateString(),
                    'is_current' => $exp->is_current,
                    'description' => $exp->description,
                    'sort_order' => $exp->sort_order,
                ])->values()->all(),
                'skills' => $resume->skills->map(fn ($skill) => [
                    'id' => $skill->id,
                    'resume_id' => $skill->resume_id,
                    'name' => $skill->name,
                    'category' => $skill->category,
                    'proficiency' => $skill->proficiency,
                    'is_featured' => $skill->is_featured,
                    'sort_order' => $skill->sort_order,
                    'metadata' => $skill->metadata,
                ])->values()->all(),
                'languages' => $resume->languages->map(fn ($lang) => [
                    'id' => $lang->id,
                    'resume_id' => $lang->resume_id,
                    'language' => $lang->language,
                    'proficiency' => $lang->proficiency,
                    'is_native' => $lang->is_native,
                    'sort_order' => $lang->sort_order,
                    'metadata' => $lang->metadata,
                ])->values()->all(),
                'certifications' => $resume->certifications->map(fn ($cert) => [
                    'id' => $cert->id,
                    'resume_id' => $cert->resume_id,
                    'name' => $cert->name,
                    'issuer' => $cert->issuer,
                    'issued_on' => $cert->issued_on?->toDateString(),
                    'expires_on' => $cert->expires_on?->toDateString(),
                    'credential_id' => $cert->credential_id,
                    'credential_url' => $cert->credential_url,
                    'description' => $cert->description,
                    'sort_order' => $cert->sort_order,
                    'metadata' => $cert->metadata,
                ])->values()->all(),
                'projects' => $resume->projects->map(fn ($project) => [
                    'id' => $project->id,
                    'resume_id' => $project->resume_id,
                    'name' => $project->name,
                    'role' => $project->role,
                    'organization' => $project->organization,
                    'url' => $project->url,
                    'started_on' => $project->started_on?->toDateString(),
                    'ended_on' => $project->ended_on?->toDateString(),
                    'is_current' => $project->is_current,
                    'description' => $project->description,
                    'sort_order' => $project->sort_order,
                    'metadata' => $project->metadata,
                ])->values()->all(),
                'volunteering' => $resume->volunteering->map(fn ($vol) => [
                    'id' => $vol->id,
                    'resume_id' => $vol->resume_id,
                    'organization' => $vol->organization,
                    'role' => $vol->role,
                    'location' => $vol->location,
                    'started_on' => $vol->started_on?->toDateString(),
                    'ended_on' => $vol->ended_on?->toDateString(),
                    'is_current' => $vol->is_current,
                    'description' => $vol->description,
                    'sort_order' => $vol->sort_order,
                    'metadata' => $vol->metadata,
                ])->values()->all(),
                'achievements' => $resume->achievements->map(fn ($ach) => [
                    'id' => $ach->id,
                    'resume_id' => $ach->resume_id,
                    'title' => $ach->title,
                    'issuer' => $ach->issuer,
                    'achieved_on' => $ach->achieved_on?->toDateString(),
                    'category' => $ach->category,
                    'url' => $ach->url,
                    'description' => $ach->description,
                    'sort_order' => $ach->sort_order,
                    'metadata' => $ach->metadata,
                ])->values()->all(),
            ],
        ]);
    }
}
