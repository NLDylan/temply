<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreLanguageRequest;
use App\Http\Requests\Resume\UpdateLanguageRequest;
use App\Models\Resume;
use App\Models\ResumeLanguage;
use App\Services\Resume\ResumeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class LanguageController extends Controller
{
    public function __construct(
        private readonly ResumeSectionService $service
    ) {}

    /**
     * Store a new language entry.
     */
    public function store(StoreLanguageRequest $request, Resume $resume): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $language = new ResumeLanguage;
        $language->resume_id = $resume->id;
        $language->fill($request->validated());
        $language->sort_order = $resume->languages()->max('sort_order') + 1 ?? 0;
        $language->save();

        return response()->json([
            'language' => $language->toArray(),
        ], 201);
    }

    /**
     * Update an existing language entry.
     */
    public function update(UpdateLanguageRequest $request, Resume $resume, ResumeLanguage $language): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $language->resume_id !== $resume->id) {
            abort(404);
        }

        $language->fill($request->validated());
        $language->save();

        return response()->json([
            'language' => $language->toArray(),
        ]);
    }

    /**
     * Delete a language entry.
     */
    public function destroy(Request $request, Resume $resume, ResumeLanguage $language): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $language->resume_id !== $resume->id) {
            abort(404);
        }

        $language->delete();

        return response()->json([
            'message' => 'Language entry deleted successfully.',
        ]);
    }

    /**
     * Sync all language entries for a resume.
     */
    public function sync(Request $request, Resume $resume): JsonResponse|RedirectResponse|Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $request->validate([
            'languages' => ['required', 'array'],
            'languages.*.id' => ['nullable', 'string'],
            'languages.*.language' => ['required', 'string', 'max:255'],
            'languages.*.proficiency' => ['nullable', 'string', 'max:255'],
            'languages.*.is_native' => ['nullable', 'boolean'],
            'languages.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'languages.*.metadata' => ['nullable', 'array'],
        ]);

        $languages = $this->service->syncLanguages($resume, $request->input('languages', []));

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            $languagesData = $languages->map(fn (ResumeLanguage $l) => [
                'id' => $l->id,
                'resume_id' => $l->resume_id,
                'language' => $l->language,
                'proficiency' => $l->proficiency,
                'is_native' => $l->is_native,
                'sort_order' => $l->sort_order,
                'metadata' => $l->metadata,
            ])->values()->all();

            return response()->json([
                'languages' => $languagesData,
            ]);
        }

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Languages saved.');
    }
}
