<?php

use App\Http\Controllers\Resume\AchievementController;
use App\Http\Controllers\Resume\ResumeBuilderController;
use App\Http\Controllers\Resume\ResumeIndexController;
use App\Http\Controllers\Resume\ResumeUpdateController;
use App\Http\Controllers\Resume\VolunteeringController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/resumes', ResumeIndexController::class)->name('resumes.index');
    Route::get('/resumes/{resume}', ResumeBuilderController::class)->name('resumes.edit');
    Route::patch('/resumes/{resume}', ResumeUpdateController::class)->name('resumes.update');

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
});

require __DIR__.'/settings.php';
