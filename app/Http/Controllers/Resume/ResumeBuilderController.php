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
                'title' => $resume->title,
                'headline' => $resume->headline,
                'location' => $resume->location,
                'profile' => $profile,
                'updated_at' => $resume->updated_at?->toIso8601String(),
            ],
        ]);
    }
}
