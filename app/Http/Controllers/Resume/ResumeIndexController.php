<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ResumeIndexController extends Controller
{
    /**
     * Display the authenticated user's resumes index.
     */
    public function __invoke(Request $request): Response
    {
        $resumes = $request->user()
            ->resumes()
            ->select([
                'id',
                'title',
                'slug',
                'headline',
                'created_at',
                'updated_at',
            ])
            ->latest('created_at')
            ->get()
            ->map(fn (Resume $resume): array => [
                'id' => $resume->id,
                'title' => $resume->title,
                'slug' => $resume->slug,
                'headline' => $resume->headline,
                'created_at' => $resume->created_at?->toIso8601String(),
                'updated_at' => $resume->updated_at?->toIso8601String(),
            ]);

        return Inertia::render('Resume/Index', [
            'resumes' => $resumes,
        ]);
    }
}
