<?php

use App\Http\Controllers\Resume\ResumeIndexController;
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

    Route::get('/resume/builder', function () {
        return Inertia::render('Resume/Builder');
    })->name('resume.builder');
});

require __DIR__.'/settings.php';
