<?php

namespace App\Http\Controllers\Resume;

use App\Http\Controllers\Controller;
use App\Models\Resume;
use Illuminate\Http\Request;

class ResumeDestroyController extends Controller
{
    /**
     * Delete the specified resume.
     */
    public function __invoke(Request $request, Resume $resume): \Illuminate\Http\RedirectResponse
    {
        if ($resume->user_id !== $request->user()->id) {
            abort(404);
        }

        $resume->delete();

        return redirect()
            ->route('resumes.index')
            ->with('success', 'Resume deleted successfully.');
    }
}
