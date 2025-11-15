<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreCertificationRequest;
use App\Http\Requests\Resume\UpdateCertificationRequest;
use App\Models\Resume;
use App\Models\ResumeCertification;
use App\Services\Resume\ResumeSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class CertificationController extends Controller
{
    public function __construct(
        private readonly ResumeSectionService $service
    ) {}

    /**
     * Store a new certification entry.
     */
    public function store(StoreCertificationRequest $request, Resume $resume): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $certification = new ResumeCertification;
        $certification->resume_id = $resume->id;
        $certification->fill($request->validated());
        $certification->sort_order = $resume->certifications()->max('sort_order') + 1 ?? 0;
        $certification->save();

        return response()->json([
            'certification' => $certification->toArray(),
        ], 201);
    }

    /**
     * Update an existing certification entry.
     */
    public function update(UpdateCertificationRequest $request, Resume $resume, ResumeCertification $certification): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $certification->resume_id !== $resume->id) {
            abort(404);
        }

        $certification->fill($request->validated());
        $certification->save();

        return response()->json([
            'certification' => $certification->toArray(),
        ]);
    }

    /**
     * Delete a certification entry.
     */
    public function destroy(Request $request, Resume $resume, ResumeCertification $certification): JsonResponse
    {
        if ($resume->user_id !== $request->user()->id || $certification->resume_id !== $resume->id) {
            abort(404);
        }

        $certification->delete();

        return response()->json([
            'message' => 'Certification entry deleted successfully.',
        ]);
    }

    /**
     * Sync all certification entries for a resume.
     */
    public function sync(Request $request, Resume $resume): JsonResponse|RedirectResponse|Response
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $request->validate([
            'certifications' => ['required', 'array'],
            'certifications.*.id' => ['nullable', 'string'],
            'certifications.*.name' => ['required', 'string', 'max:255'],
            'certifications.*.issuer' => ['nullable', 'string', 'max:255'],
            'certifications.*.issued_on' => ['nullable', 'date'],
            'certifications.*.expires_on' => ['nullable', 'date'],
            'certifications.*.credential_id' => ['nullable', 'string', 'max:255'],
            'certifications.*.credential_url' => ['nullable', 'url', 'max:255'],
            'certifications.*.description' => ['nullable', 'string', 'max:5000'],
            'certifications.*.sort_order' => ['nullable', 'integer', 'min:0'],
            'certifications.*.metadata' => ['nullable', 'array'],
        ]);

        $certifications = $this->service->syncCertifications($resume, $request->input('certifications', []));

        if ($request->wantsJson() && ! $request->header('X-Inertia')) {
            $certificationsData = $certifications->map(fn (ResumeCertification $c) => [
                'id' => $c->id,
                'resume_id' => $c->resume_id,
                'name' => $c->name,
                'issuer' => $c->issuer,
                'issued_on' => $c->issued_on?->toDateString(),
                'expires_on' => $c->expires_on?->toDateString(),
                'credential_id' => $c->credential_id,
                'credential_url' => $c->credential_url,
                'description' => $c->description,
                'sort_order' => $c->sort_order,
                'metadata' => $c->metadata,
            ])->values()->all();

            return response()->json([
                'certifications' => $certificationsData,
            ]);
        }

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Certifications saved.');
    }
}
