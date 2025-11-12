<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\UpdateBasicInformationRequest;
use App\Models\Resume;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ResumeUpdateController extends Controller
{
    public function __invoke(UpdateBasicInformationRequest $request, Resume $resume): RedirectResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $validated = $request->validated();

        $profileInput = Arr::get($validated, 'profile', []);

        $contactLinks = collect($profileInput['contact_links'] ?? [])
            ->map(function (array $link): ?array {
                $label = trim($link['label'] ?? '');
                $url = trim($link['url'] ?? '');

                if ($label === '' && $url === '') {
                    return null;
                }

                return [
                    'id' => $link['id'] ?? (string) Str::uuid(),
                    'label' => $label,
                    'url' => $url,
                ];
            })
            ->filter()
            ->values()
            ->all();

        $profile = array_merge(
            [
                'full_name' => null,
                'email' => null,
                'phone' => null,
                'working_rights' => null,
                'contact_links' => [],
            ],
            $resume->profile ?? [],
        );

        $profile = array_merge($profile, Arr::except($profileInput, ['contact_links']));

        $profile['full_name'] = trim((string) ($profile['full_name'] ?? ''));
        $profile['email'] = $profile['email'] !== null && $profile['email'] !== '' ? $profile['email'] : null;
        $profile['phone'] = $profile['phone'] !== null && $profile['phone'] !== '' ? $profile['phone'] : null;
        $profile['working_rights'] = $profile['working_rights'] !== null && $profile['working_rights'] !== '' ? $profile['working_rights'] : null;
        $profile['contact_links'] = $contactLinks;

        $resume->fill([
            'headline' => $validated['headline'] ?? null,
            'location' => $validated['location'] ?? null,
            'profile' => $profile,
        ]);

        $resume->save();

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Resume saved.');
    }
}
