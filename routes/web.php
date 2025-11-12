<?php

use App\Http\Controllers\Resume\ResumeBuilderController;
use App\Http\Controllers\Resume\ResumeIndexController;
use App\Http\Controllers\Resume\ResumeUpdateController;
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
});

require __DIR__.'/settings.php';
