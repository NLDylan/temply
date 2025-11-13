<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreVolunteeringRequest;
use App\Http\Requests\Resume\UpdateVolunteeringRequest;
use App\Models\Resume;
use App\Models\ResumeVolunteering;
use App\Services\Resume\ResumeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VolunteeringController extends Controller
{
    public function __construct(
        private readonly ResumeSectionService $service
    ) {}

    /**
     * Store a new volunteering entry.
     */
    public function store(StoreVolunteeringRequest $request, Resume $resume): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $volunteering = new ResumeVolunteering;
        $volunteering->resume_id = $resume->id;
        $volunteering->fill($request->validated());
        $volunteering->sort_order = $resume->volunteering()->max('sort_order') + 1 ?? 0;
        $volunteering->save();

        return response()->json([
            'volunteering' => $volunteering->toArray(),
        ], 201);
    }

    /**
     * Update an existing volunteering entry.
     */
    public function update(UpdateVolunteeringRequest $request, Resume $resume, ResumeVolunteering $volunteering): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $volunteering->resume_id !== $resume->id) {
            abort(404);
        }

        $volunteering->fill($request->validated());
        $volunteering->save();

        return response()->json([
            'volunteering' => $volunteering->toArray(),
        ]);
    }

    /**
     * Delete a volunteering entry.
     */
    public function destroy(Request $request, Resume $resume, ResumeVolunteering $volunteering): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $volunteering->resume_id !== $resume->id) {
            abort(404);
        }

        $volunteering->delete();

        return response()->json([
            'message' => 'Volunteering entry deleted successfully.',
        ]);
    }

    /**
     * Sync all volunteering entries for a resume.
     */
    public function sync(Request $request, Resume $resume): JsonResponse|RedirectResponse|Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $request->validate([
            'volunteering' => ['required', 'array'],
            'volunteering.*.id' => ['nullable', 'string'],
            'volunteering.*.organization' => ['required', 'string', 'max:255'],
            'volunteering.*.role' => ['nullable', 'string', 'max:255'],
            'volunteering.*.location' => ['nullable', 'string', 'max:255'],
            'volunteering.*.started_on' => ['nullable', 'date'],
            'volunteering.*.ended_on' => ['nullable', 'date'],
            'volunteering.*.is_current' => ['nullable', 'boolean'],
            'volunteering.*.description' => ['nullable', 'string', 'max:5000'],
            'volunteering.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'volunteering.*.metadata' => ['nullable', 'array'],
        ]);

        $volunteering = $this->service->syncVolunteering($resume, $request->input('volunteering', []));

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            $volunteeringData = $volunteering->map(fn (ResumeVolunteering $v) => [
                'id' => $v->id,
                'resume_id' => $v->resume_id,
                'organization' => $v->organization,
                'role' => $v->role,
                'location' => $v->location,
                'started_on' => $v->started_on?->toDateString(),
                'ended_on' => $v->ended_on?->toDateString(),
                'is_current' => $v->is_current,
                'description' => $v->description,
                'sort_order' => $v->sort_order,
                'metadata' => $v->metadata,
            ])->values()->all();

            return response()->json([
                'volunteering' => $volunteeringData,
            ]);
        }

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Volunteering entries saved.');
    }
}
