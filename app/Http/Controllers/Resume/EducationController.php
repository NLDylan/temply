<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreEducationRequest;
use App\Http\Requests\Resume\UpdateEducationRequest;
use App\Models\Resume;
use App\Models\ResumeEducation;
use App\Services\Resume\ResumeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class EducationController extends Controller
{
    public function __construct(
        private readonly ResumeSectionService $service
    ) {}

    /**
     * Store a new education entry.
     */
    public function store(StoreEducationRequest $request, Resume $resume): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $education = new ResumeEducation;
        $education->resume_id = $resume->id;
        $education->fill($request->validated());
        $education->sort_order = $resume->education()->max('sort_order') + 1 ?? 0;
        $education->save();

        return response()->json([
            'education' => $education->toArray(),
        ], 201);
    }

    /**
     * Update an existing education entry.
     */
    public function update(UpdateEducationRequest $request, Resume $resume, ResumeEducation $education): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $education->resume_id !== $resume->id) {
            abort(404);
        }

        $education->fill($request->validated());
        $education->save();

        return response()->json([
            'education' => $education->toArray(),
        ]);
    }

    /**
     * Delete an education entry.
     */
    public function destroy(Request $request, Resume $resume, ResumeEducation $education): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $education->resume_id !== $resume->id) {
            abort(404);
        }

        $education->delete();

        return response()->json([
            'message' => 'Education entry deleted successfully.',
        ]);
    }

    /**
     * Sync all education entries for a resume.
     */
    public function sync(Request $request, Resume $resume): JsonResponse|RedirectResponse|Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $request->validate([
            'education' => ['required', 'array'],
            'education.*.id' => ['nullable', 'string'],
            'education.*.institution' => ['required', 'string', 'max:255'],
            'education.*.degree' => ['nullable', 'string', 'max:255'],
            'education.*.field_of_study' => ['nullable', 'string', 'max:255'],
            'education.*.location' => ['nullable', 'string', 'max:255'],
            'education.*.started_on' => ['nullable', 'date'],
            'education.*.ended_on' => ['nullable', 'date'],
            'education.*.is_current' => ['nullable', 'boolean'],
            'education.*.description' => ['nullable', 'string', 'max:5000'],
            'education.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $education = $this->service->syncEducation($resume, $request->input('education', []));

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            $educationData = $education->map(fn (ResumeEducation $e) => [
                'id' => $e->id,
                'resume_id' => $e->resume_id,
                'institution' => $e->institution,
                'degree' => $e->degree,
                'field_of_study' => $e->field_of_study,
                'location' => $e->location,
                'started_on' => $e->started_on?->toDateString(),
                'ended_on' => $e->ended_on?->toDateString(),
                'is_current' => $e->is_current,
                'description' => $e->description,
                'sort_order' => $e->sort_order,
            ])->values()->all();

            return response()->json([
                'education' => $educationData,
            ]);
        }

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Education entries saved.');
    }
}
