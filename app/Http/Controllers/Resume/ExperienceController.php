<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreExperienceRequest;
use App\Http\Requests\Resume\UpdateExperienceRequest;
use App\Models\Resume;
use App\Models\ResumeExperience;
use App\Services\Resume\ResumeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ExperienceController extends Controller
{
    public function __construct(
        private readonly ResumeSectionService $service
    ) {}

    /**
     * Store a new experience entry.
     */
    public function store(StoreExperienceRequest $request, Resume $resume): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $experience = new ResumeExperience;
        $experience->resume_id = $resume->id;
        $experience->fill($request->validated());
        $experience->sort_order = $resume->experience()->max('sort_order') + 1 ?? 0;
        $experience->save();

        return response()->json([
            'experience' => $experience->toArray(),
        ], 201);
    }

    /**
     * Update an existing experience entry.
     */
    public function update(UpdateExperienceRequest $request, Resume $resume, ResumeExperience $experience): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $experience->resume_id !== $resume->id) {
            abort(404);
        }

        $experience->fill($request->validated());
        $experience->save();

        return response()->json([
            'experience' => $experience->toArray(),
        ]);
    }

    /**
     * Delete an experience entry.
     */
    public function destroy(Request $request, Resume $resume, ResumeExperience $experience): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $experience->resume_id !== $resume->id) {
            abort(404);
        }

        $experience->delete();

        return response()->json([
            'message' => 'Experience entry deleted successfully.',
        ]);
    }

    /**
     * Sync all experience entries for a resume.
     */
    public function sync(Request $request, Resume $resume): JsonResponse|RedirectResponse|Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $request->validate([
            'experience' => ['required', 'array'],
            'experience.*.id' => ['nullable', 'string'],
            'experience.*.company' => ['required', 'string', 'max:255'],
            'experience.*.role' => ['nullable', 'string', 'max:255'],
            'experience.*.employment_type' => ['nullable', 'string', 'max:255'],
            'experience.*.location' => ['nullable', 'string', 'max:255'],
            'experience.*.started_on' => ['nullable', 'date'],
            'experience.*.ended_on' => ['nullable', 'date'],
            'experience.*.is_current' => ['nullable', 'boolean'],
            'experience.*.description' => ['nullable', 'string', 'max:5000'],
            'experience.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $experience = $this->service->syncExperience($resume, $request->input('experience', []));

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            $experienceData = $experience->map(fn (ResumeExperience $e) => [
                'id' => $e->id,
                'resume_id' => $e->resume_id,
                'company' => $e->company,
                'role' => $e->role,
                'employment_type' => $e->employment_type,
                'location' => $e->location,
                'started_on' => $e->started_on?->toDateString(),
                'ended_on' => $e->ended_on?->toDateString(),
                'is_current' => $e->is_current,
                'description' => $e->description,
                'sort_order' => $e->sort_order,
            ])->values()->all();

            return response()->json([
                'experience' => $experienceData,
            ]);
        }

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Experience entries saved.');
    }
}
