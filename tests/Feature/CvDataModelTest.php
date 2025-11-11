<?php

use App\Enums\TemplateType;
use App\Models\CoverLetter;
use App\Models\Cv;
use App\Models\CvCertification;
use App\Models\CvEducation;
use App\Models\CvExperience;
use App\Models\CvLanguage;
use App\Models\CvProject;
use App\Models\CvSkill;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

it('creates expected schema for templates, cvs, cover letters, and sections', function (): void {
    expect(Schema::hasTable('templates'))->toBeTrue();
    expect(Schema::hasColumns('templates', [
        'id',
        'name',
        'slug',
        'type',
        'metadata',
        'is_active',
        'published_at',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    expect(Schema::hasTable('cvs'))->toBeTrue();
    expect(Schema::hasColumns('cvs', [
        'id',
        'user_id',
        'template_id',
        'title',
        'slug',
        'headline',
        'location',
        'summary',
        'profile',
        'settings',
        'expires_at',
        'locked_at',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    expect(Schema::hasTable('cover_letters'))->toBeTrue();
    expect(Schema::hasColumns('cover_letters', [
        'id',
        'user_id',
        'template_id',
        'cv_id',
        'title',
        'slug',
        'recipient_name',
        'recipient_title',
        'recipient_company',
        'subject',
        'body',
        'metadata',
        'created_at',
        'updated_at',
    ]))->toBeTrue();

    foreach ([
        'cv_education',
        'cv_experience',
        'cv_skills',
        'cv_languages',
        'cv_certifications',
        'cv_projects',
    ] as $table) {
        expect(Schema::hasTable($table))->toBeTrue();
        expect(Schema::hasColumn($table, 'cv_id'))->toBeTrue();
        expect(Schema::hasColumn($table, 'sort_order'))->toBeTrue();
        expect(Schema::hasColumn($table, 'created_at'))->toBeTrue();
        expect(Schema::hasColumn($table, 'updated_at'))->toBeTrue();
    }
});

it('casts template type to enum and applies defaults', function (): void {
    $template = Template::query()->create([
        'name' => 'Classic CV',
        'slug' => 'classic-cv',
    ]);

    expect($template->type)->toBe(TemplateType::Cv)
        ->and($template->is_active)->toBeTrue()
        ->and($template->supportsCv())->toBeTrue()
        ->and($template->supportsCoverLetter())->toBeFalse();

    $coverLetterTemplate = Template::query()->create([
        'name' => 'Universal',
        'slug' => 'universal',
        'type' => TemplateType::Both,
        'is_active' => false,
    ]);

    expect($coverLetterTemplate->type)->toBe(TemplateType::Both)
        ->and($coverLetterTemplate->is_active)->toBeFalse()
        ->and($coverLetterTemplate->supportsCoverLetter())->toBeTrue();
});

it('relates CV aggregates and cascades sections on delete', function (): void {
    $user = User::factory()->create();
    $template = Template::query()->create([
        'name' => 'Modern CV',
        'slug' => 'modern-cv',
    ]);

    $cv = Cv::query()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'title' => 'Product Designer',
        'slug' => 'product-designer',
        'headline' => 'Crafting friendly experiences',
        'location' => 'Remote',
    ]);

    $educationA = CvEducation::query()->create([
        'cv_id' => $cv->id,
        'institution' => 'Design University',
        'degree' => 'BFA',
        'sort_order' => 2,
    ]);

    $educationB = CvEducation::query()->create([
        'cv_id' => $cv->id,
        'institution' => 'Community College',
        'degree' => 'AA',
        'sort_order' => 1,
    ]);

    CvExperience::query()->create([
        'cv_id' => $cv->id,
        'company' => 'Acme',
        'role' => 'Lead Designer',
        'is_current' => true,
    ]);

    CvSkill::query()->create([
        'cv_id' => $cv->id,
        'name' => 'Figma',
        'is_featured' => true,
    ]);

    CvLanguage::query()->create([
        'cv_id' => $cv->id,
        'language' => 'English',
        'is_native' => true,
    ]);

    CvCertification::query()->create([
        'cv_id' => $cv->id,
        'name' => 'UX Certification',
        'issuer' => 'NN/g',
    ]);

    CvProject::query()->create([
        'cv_id' => $cv->id,
        'name' => 'Portfolio Refresh',
        'is_current' => true,
    ]);

    expect($cv->education->pluck('id')->all())->toBe([$educationB->id, $educationA->id])
        ->and($cv->experience)->toHaveCount(1)
        ->and($cv->skills)->toHaveCount(1)
        ->and($cv->languages)->toHaveCount(1)
        ->and($cv->certifications)->toHaveCount(1)
        ->and($cv->projects)->toHaveCount(1);

    $cv->delete();

    expect(CvEducation::query()->count())->toBe(0)
        ->and(CvExperience::query()->count())->toBe(0)
        ->and(CvSkill::query()->count())->toBe(0)
        ->and(CvLanguage::query()->count())->toBe(0)
        ->and(CvCertification::query()->count())->toBe(0)
        ->and(CvProject::query()->count())->toBe(0);
});

it('associates cover letters with users, templates, and CVs', function (): void {
    $user = User::factory()->create();
    $template = Template::query()->create([
        'name' => 'Letter Pro',
        'slug' => 'letter-pro',
        'type' => TemplateType::CoverLetter,
    ]);

    $cv = Cv::query()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'title' => 'Dev CV',
        'slug' => 'dev-cv',
    ]);

    $coverLetter = CoverLetter::query()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'cv_id' => $cv->id,
        'title' => 'Senior Developer Cover Letter',
        'slug' => 'senior-developer',
        'recipient_name' => 'Alex Doe',
        'body' => 'Thank you for your consideration.',
    ]);

    expect($coverLetter->user->is($user))->toBeTrue()
        ->and($coverLetter->template?->supportsCoverLetter())->toBeTrue()
        ->and($coverLetter->cv?->is($cv))->toBeTrue();
});
