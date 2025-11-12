<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\Request;
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

        return Inertia::render('Resume/Builder', [
            'resume' => [
                'id' => $resume->id,
                'title' => $resume->title,
                'headline' => $resume->headline,
                'updated_at' => $resume->updated_at?->toIso8601String(),
            ],
        ]);
    }
}
