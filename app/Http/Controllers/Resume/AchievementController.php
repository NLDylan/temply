<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreAchievementRequest;
use App\Http\Requests\Resume\UpdateAchievementRequest;
use App\Models\Resume;
use App\Models\ResumeAchievement;
use App\Services\Resume\ResumeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    public function __construct(
        private readonly ResumeSectionService $service
    ) {}

    /**
     * Store a new achievement entry.
     */
    public function store(StoreAchievementRequest $request, Resume $resume): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $achievement = new ResumeAchievement;
        $achievement->resume_id = $resume->id;
        $achievement->fill($request->validated());
        $achievement->sort_order = $resume->achievements()->max('sort_order') + 1 ?? 0;
        $achievement->save();

        return response()->json([
            'achievement' => $achievement->toArray(),
        ], 201);
    }

    /**
     * Update an existing achievement entry.
     */
    public function update(UpdateAchievementRequest $request, Resume $resume, ResumeAchievement $achievement): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $achievement->resume_id !== $resume->id) {
            abort(404);
        }

        $achievement->fill($request->validated());
        $achievement->save();

        return response()->json([
            'achievement' => $achievement->toArray(),
        ]);
    }

    /**
     * Delete an achievement entry.
     */
    public function destroy(Request $request, Resume $resume, ResumeAchievement $achievement): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $achievement->resume_id !== $resume->id) {
            abort(404);
        }

        $achievement->delete();

        return response()->json([
            'message' => 'Achievement entry deleted successfully.',
        ]);
    }

    /**
     * Sync all achievement entries for a resume.
     */
    public function sync(Request $request, Resume $resume): JsonResponse|RedirectResponse|Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $request->validate([
            'achievements' => ['required', 'array'],
            'achievements.*.id' => ['nullable', 'string'],
            'achievements.*.title' => ['required', 'string', 'max:255'],
            'achievements.*.issuer' => ['nullable', 'string', 'max:255'],
            'achievements.*.achieved_on' => ['nullable', 'date'],
            'achievements.*.category' => ['nullable', 'string', 'max:255'],
            'achievements.*.url' => ['nullable', 'url', 'max:255'],
            'achievements.*.description' => ['nullable', 'string', 'max:5000'],
            'achievements.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'achievements.*.metadata' => ['nullable', 'array'],
        ]);

        $achievements = $this->service->syncAchievements($resume, $request->input('achievements', []));

        // Reload the resume with updated achievements
        $resume->load('achievements');

        if ($request->header('X-Inertia')) {
            // Return Inertia response with updated resume data
            return redirect()
                ->route('resumes.edit', $resume)
                ->with('success', 'Achievements saved.');
        }

        if ($request->wantsJson()) {
            $achievementsData = $achievements->map(fn (ResumeAchievement $a) => [
                'id' => $a->id,
                'resume_id' => $a->resume_id,
                'title' => $a->title,
                'issuer' => $a->issuer,
                'achieved_on' => $a->achieved_on?->toDateString(),
                'category' => $a->category,
                'url' => $a->url,
                'description' => $a->description,
                'sort_order' => $a->sort_order,
                'metadata' => $a->metadata,
            ])->values()->all();

            return response()->json([
                'achievements' => $achievementsData,
            ]);
        }

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Achievements saved.');
    }
}
