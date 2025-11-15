<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resume\StoreResumeRequest;
use App\Models\Resume;
use Illuminate\Support\Str;

class ResumeStoreController extends Controller
{
    /**
     * Store a newly created resume.
     */
    public function __invoke(StoreResumeRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();
        $title = $request->validated()['title'];
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;

        // Ensure slug is unique for this user
        $counter = 1;
        while ($user->resumes()->where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => $title,
            'slug' => $slug,
            'template_id' => $request->validated()['template_id'] ?? null,
        ]);

        return redirect()
            ->route('resumes.edit', $resume)
            ->with('success', 'Resume created successfully.');
    }
}
