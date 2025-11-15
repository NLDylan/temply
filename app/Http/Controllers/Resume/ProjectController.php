<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreProjectRequest;
use App\Http\Requests\Resume\UpdateProjectRequest;
use App\Models\Resume;
use App\Models\ResumeProject;
use App\Services\Resume\ResumeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private readonly ResumeSectionService $service
    ) {}

    /**
     * Store a new project entry.
     */
    public function store(StoreProjectRequest $request, Resume $resume): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $project = new ResumeProject;
        $project->resume_id = $resume->id;
        $project->fill($request->validated());
        $project->sort_order = $resume->projects()->max('sort_order') + 1 ?? 0;
        $project->save();

        return response()->json([
            'project' => $project->toArray(),
        ], 201);
    }

    /**
     * Update an existing project entry.
     */
    public function update(UpdateProjectRequest $request, Resume $resume, ResumeProject $project): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $project->resume_id !== $resume->id) {
            abort(404);
        }

        $project->fill($request->validated());
        $project->save();

        return response()->json([
            'project' => $project->toArray(),
        ]);
    }

    /**
     * Delete a project entry.
     */
    public function destroy(Request $request, Resume $resume, ResumeProject $project): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $project->resume_id !== $resume->id) {
            abort(404);
        }

        $project->delete();

        return response()->json([
            'message' => 'Project entry deleted successfully.',
        ]);
    }

    /**
     * Sync all project entries for a resume.
     */
    public function sync(Request $request, Resume $resume): JsonResponse|RedirectResponse|Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $request->validate([
            'projects' => ['required', 'array'],
            'projects.*.id' => ['nullable', 'string'],
            'projects.*.name' => ['required', 'string', 'max:255'],
            'projects.*.role' => ['nullable', 'string', 'max:255'],
            'projects.*.organization' => ['nullable', 'string', 'max:255'],
            'projects.*.url' => ['nullable', 'url', 'max:255'],
            'projects.*.started_on' => ['nullable', 'date'],
            'projects.*.ended_on' => ['nullable', 'date'],
            'projects.*.is_current' => ['nullable', 'boolean'],
            'projects.*.description' => ['nullable', 'string', 'max:5000'],
            'projects.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'projects.*.metadata' => ['nullable', 'array'],
        ]);

        $projects = $this->service->syncProjects($resume, $request->input('projects', []));

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            $projectsData = $projects->map(fn (ResumeProject $p) => [
                'id' => $p->id,
                'resume_id' => $p->resume_id,
                'name' => $p->name,
                'role' => $p->role,
                'organization' => $p->organization,
                'url' => $p->url,
                'started_on' => $p->started_on?->toDateString(),
                'ended_on' => $p->ended_on?->toDateString(),
                'is_current' => $p->is_current,
                'description' => $p->description,
                'sort_order' => $p->sort_order,
                'metadata' => $p->metadata,
            ])->values()->all();

            return response()->json([
                'projects' => $projectsData,
            ]);
        }

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Projects saved.');
    }
}
