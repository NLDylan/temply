<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreSkillRequest;
use App\Http\Requests\Resume\UpdateSkillRequest;
use App\Models\Resume;
use App\Models\ResumeSkill;
use App\Services\Resume\ResumeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class SkillController extends Controller
{
    public function __construct(
        private readonly ResumeSectionService $service
    ) {}

    /**
     * Store a new skill entry.
     */
    public function store(StoreSkillRequest $request, Resume $resume): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $skill = new ResumeSkill;
        $skill->resume_id = $resume->id;
        $skill->fill($request->validated());
        $skill->sort_order = $resume->skills()->max('sort_order') + 1 ?? 0;
        $skill->save();

        return response()->json([
            'skill' => $skill->toArray(),
        ], 201);
    }

    /**
     * Update an existing skill entry.
     */
    public function update(UpdateSkillRequest $request, Resume $resume, ResumeSkill $skill): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $skill->resume_id !== $resume->id) {
            abort(404);
        }

        $skill->fill($request->validated());
        $skill->save();

        return response()->json([
            'skill' => $skill->toArray(),
        ]);
    }

    /**
     * Delete a skill entry.
     */
    public function destroy(Request $request, Resume $resume, ResumeSkill $skill): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $skill->resume_id !== $resume->id) {
            abort(404);
        }

        $skill->delete();

        return response()->json([
            'message' => 'Skill entry deleted successfully.',
        ]);
    }

    /**
     * Sync all skill entries for a resume.
     */
    public function sync(Request $request, Resume $resume): JsonResponse|RedirectResponse|Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $request->validate([
            'skills' => ['required', 'array'],
            'skills.*.id' => ['nullable', 'string'],
            'skills.*.name' => ['required', 'string', 'max:255'],
            'skills.*.category' => ['nullable', 'string', 'max:255'],
            'skills.*.proficiency' => ['nullable', 'string', 'max:255'],
            'skills.*.is_featured' => ['nullable', 'boolean'],
            'skills.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'skills.*.metadata' => ['nullable', 'array'],
        ]);

        $skills = $this->service->syncSkills($resume, $request->input('skills', []));

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            $skillsData = $skills->map(fn (ResumeSkill $s) => [
                'id' => $s->id,
                'resume_id' => $s->resume_id,
                'name' => $s->name,
                'category' => $s->category,
                'proficiency' => $s->proficiency,
                'is_featured' => $s->is_featured,
                'sort_order' => $s->sort_order,
                'metadata' => $s->metadata,
            ])->values()->all();

            return response()->json([
                'skills' => $skillsData,
            ]);
        }

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Skills saved.');
    }
}
