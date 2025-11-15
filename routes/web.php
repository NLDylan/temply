<?php

use App\Http\Controllers\Resume\AchievementController;
use App\Http\Controllers\Resume\CertificationController;
use App\Http\Controllers\Resume\EducationController;
use App\Http\Controllers\Resume\ExperienceController;
use App\Http\Controllers\Resume\LanguageController;
use App\Http\Controllers\Resume\ProjectController;
use App\Http\Controllers\Resume\ResumeBuilderController;
use App\Http\Controllers\Resume\ResumeDestroyController;
use App\Http\Controllers\Resume\ResumeDuplicateController;
use App\Http\Controllers\Resume\ResumeIndexController;
use App\Http\Controllers\Resume\ResumeStoreController;
use App\Http\Controllers\Resume\ResumeUpdateController;
use App\Http\Controllers\Resume\SkillController;
use App\Http\Controllers\Resume\VolunteeringController;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function (Request $request) {
    $user = $request->user();

    $recentResumes = $user->resumes()
        ->select(['id', 'title', 'slug', 'headline', 'updated_at'])
        ->latest('updated_at')
        ->limit(5)
        ->get()
        ->map(fn (Resume $resume): array => [
            'id' => $resume->id,
            'title' => $resume->title,
            'slug' => $resume->slug,
            'headline' => $resume->headline,
            'updated_at' => $resume->updated_at?->toIso8601String(),
        ]);

    $totalResumes = $user->resumes()->count();
    $recentlyUpdated = $user->resumes()
        ->where('updated_at', '>=', now()->subDays(7))
        ->count();

    return Inertia::render('Dashboard', [
        'recentResumes' => $recentResumes,
        'totalResumes' => $totalResumes,
        'recentlyUpdated' => $recentlyUpdated,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/resumes', ResumeIndexController::class)->name('resumes.index');
    Route::post('/resumes', ResumeStoreController::class)->name('resumes.store');
    Route::get('/resumes/{resume}', ResumeBuilderController::class)->name('resumes.edit');
    Route::patch('/resumes/{resume}', ResumeUpdateController::class)->name('resumes.update');
    Route::delete('/resumes/{resume}', ResumeDestroyController::class)->name('resumes.destroy');
    Route::post('/resumes/{resume}/duplicate', ResumeDuplicateController::class)->name('resumes.duplicate');

    // Volunteering routes
    Route::post('/resumes/{resume}/volunteering', [VolunteeringController::class, 'store'])->name('resumes.volunteering.store');
    Route::patch('/resumes/{resume}/volunteering/{volunteering}', [VolunteeringController::class, 'update'])->name('resumes.volunteering.update');
    Route::delete('/resumes/{resume}/volunteering/{volunteering}', [VolunteeringController::class, 'destroy'])->name('resumes.volunteering.destroy');
    Route::post('/resumes/{resume}/volunteering/sync', [VolunteeringController::class, 'sync'])->name('resumes.volunteering.sync');

    // Achievement routes
    Route::post('/resumes/{resume}/achievements', [AchievementController::class, 'store'])->name('resumes.achievements.store');
    Route::patch('/resumes/{resume}/achievements/{achievement}', [AchievementController::class, 'update'])->name('resumes.achievements.update');
    Route::delete('/resumes/{resume}/achievements/{achievement}', [AchievementController::class, 'destroy'])->name('resumes.achievements.destroy');
    Route::post('/resumes/{resume}/achievements/sync', [AchievementController::class, 'sync'])->name('resumes.achievements.sync');

    // Education routes
    Route::post('/resumes/{resume}/education', [EducationController::class, 'store'])->name('resumes.education.store');
    Route::patch('/resumes/{resume}/education/{education}', [EducationController::class, 'update'])->name('resumes.education.update');
    Route::delete('/resumes/{resume}/education/{education}', [EducationController::class, 'destroy'])->name('resumes.education.destroy');
    Route::post('/resumes/{resume}/education/sync', [EducationController::class, 'sync'])->name('resumes.education.sync');

    // Experience routes
    Route::post('/resumes/{resume}/experience', [ExperienceController::class, 'store'])->name('resumes.experience.store');
    Route::patch('/resumes/{resume}/experience/{experience}', [ExperienceController::class, 'update'])->name('resumes.experience.update');
    Route::delete('/resumes/{resume}/experience/{experience}', [ExperienceController::class, 'destroy'])->name('resumes.experience.destroy');
    Route::post('/resumes/{resume}/experience/sync', [ExperienceController::class, 'sync'])->name('resumes.experience.sync');

    // Skills routes
    Route::post('/resumes/{resume}/skills', [SkillController::class, 'store'])->name('resumes.skills.store');
    Route::patch('/resumes/{resume}/skills/{skill}', [SkillController::class, 'update'])->name('resumes.skills.update');
    Route::delete('/resumes/{resume}/skills/{skill}', [SkillController::class, 'destroy'])->name('resumes.skills.destroy');
    Route::post('/resumes/{resume}/skills/sync', [SkillController::class, 'sync'])->name('resumes.skills.sync');

    // Languages routes
    Route::post('/resumes/{resume}/languages', [LanguageController::class, 'store'])->name('resumes.languages.store');
    Route::patch('/resumes/{resume}/languages/{language}', [LanguageController::class, 'update'])->name('resumes.languages.update');
    Route::delete('/resumes/{resume}/languages/{language}', [LanguageController::class, 'destroy'])->name('resumes.languages.destroy');
    Route::post('/resumes/{resume}/languages/sync', [LanguageController::class, 'sync'])->name('resumes.languages.sync');

    // Certifications routes
    Route::post('/resumes/{resume}/certifications', [CertificationController::class, 'store'])->name('resumes.certifications.store');
    Route::patch('/resumes/{resume}/certifications/{certification}', [CertificationController::class, 'update'])->name('resumes.certifications.update');
    Route::delete('/resumes/{resume}/certifications/{certification}', [CertificationController::class, 'destroy'])->name('resumes.certifications.destroy');
    Route::post('/resumes/{resume}/certifications/sync', [CertificationController::class, 'sync'])->name('resumes.certifications.sync');

    // Projects routes
    Route::post('/resumes/{resume}/projects', [ProjectController::class, 'store'])->name('resumes.projects.store');
    Route::patch('/resumes/{resume}/projects/{project}', [ProjectController::class, 'update'])->name('resumes.projects.update');
    Route::delete('/resumes/{resume}/projects/{project}', [ProjectController::class, 'destroy'])->name('resumes.projects.destroy');
    Route::post('/resumes/{resume}/projects/sync', [ProjectController::class, 'sync'])->name('resumes.projects.sync');
});

require __DIR__.'/settings.php';
