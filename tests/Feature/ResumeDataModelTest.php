<?php

use App\Enums\TemplateType;
use App\Models\CoverLetter;
use App\Models\Resume;
use App\Models\ResumeCertification;
use App\Models\ResumeEducation;
use App\Models\ResumeExperience;
use App\Models\ResumeLanguage;
use App\Models\ResumeProject;
use App\Models\ResumeSkill;
use App\Models\Template;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

it('creates expected schema for templates, resumes, cover letters, and sections', function (): void {
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

    expect(Schema::hasTable('resumes'))->toBeTrue();
    expect(Schema::hasColumns('resumes', [
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
        'resume_id',
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
        'resume_education',
        'resume_experience',
        'resume_skills',
        'resume_languages',
        'resume_certifications',
        'resume_projects',
    ] as $table) {
        expect(Schema::hasTable($table))->toBeTrue();
        expect(Schema::hasColumn($table, 'resume_id'))->toBeTrue();
        expect(Schema::hasColumn($table, 'sort_order'))->toBeTrue();
        expect(Schema::hasColumn($table, 'created_at'))->toBeTrue();
        expect(Schema::hasColumn($table, 'updated_at'))->toBeTrue();
    }
});

it('casts template type to enum and applies defaults', function (): void {
    $resumeTemplate = Template::query()->create([
        'name' => 'Classic Resume',
        'slug' => 'classic-resume',
    ]);

    expect($resumeTemplate->type)->toBe(TemplateType::Resume)
        ->and($resumeTemplate->is_active)->toBeTrue()
        ->and($resumeTemplate->supportsResume())->toBeTrue()
        ->and($resumeTemplate->supportsCoverLetter())->toBeFalse();

    $comboTemplate = Template::query()->create([
        'name' => 'Universal',
        'slug' => 'universal',
        'type' => TemplateType::Both,
        'is_active' => false,
    ]);

    expect($comboTemplate->type)->toBe(TemplateType::Both)
        ->and($comboTemplate->is_active)->toBeFalse()
        ->and($comboTemplate->supportsCoverLetter())->toBeTrue()
        ->and($comboTemplate->supportsResume())->toBeTrue();
});

it('relates resume aggregates and cascades sections on delete', function (): void {
    $user = User::factory()->create();
    $template = Template::query()->create([
        'name' => 'Modern Resume',
        'slug' => 'modern-resume',
    ]);

    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'title' => 'Product Designer',
        'slug' => 'product-designer',
        'headline' => 'Crafting friendly experiences',
        'location' => 'Remote',
    ]);

    $educationA = ResumeEducation::query()->create([
        'resume_id' => $resume->id,
        'institution' => 'Design University',
        'degree' => 'BFA',
        'sort_order' => 2,
    ]);

    $educationB = ResumeEducation::query()->create([
        'resume_id' => $resume->id,
        'institution' => 'Community College',
        'degree' => 'AA',
        'sort_order' => 1,
    ]);

    ResumeExperience::query()->create([
        'resume_id' => $resume->id,
        'company' => 'Acme',
        'role' => 'Lead Designer',
        'is_current' => true,
    ]);

    ResumeSkill::query()->create([
        'resume_id' => $resume->id,
        'name' => 'Figma',
        'is_featured' => true,
    ]);

    ResumeLanguage::query()->create([
        'resume_id' => $resume->id,
        'language' => 'English',
        'is_native' => true,
    ]);

    ResumeCertification::query()->create([
        'resume_id' => $resume->id,
        'name' => 'UX Certification',
        'issuer' => 'NN/g',
    ]);

    ResumeProject::query()->create([
        'resume_id' => $resume->id,
        'name' => 'Portfolio Refresh',
        'is_current' => true,
    ]);

    expect($resume->education->pluck('id')->all())->toBe([$educationB->id, $educationA->id])
        ->and($resume->experience)->toHaveCount(1)
        ->and($resume->skills)->toHaveCount(1)
        ->and($resume->languages)->toHaveCount(1)
        ->and($resume->certifications)->toHaveCount(1)
        ->and($resume->projects)->toHaveCount(1);

    $resume->delete();

    expect(ResumeEducation::query()->count())->toBe(0)
        ->and(ResumeExperience::query()->count())->toBe(0)
        ->and(ResumeSkill::query()->count())->toBe(0)
        ->and(ResumeLanguage::query()->count())->toBe(0)
        ->and(ResumeCertification::query()->count())->toBe(0)
        ->and(ResumeProject::query()->count())->toBe(0);
});

it('associates cover letters with users, templates, and resumes', function (): void {
    $user = User::factory()->create();
    $template = Template::query()->create([
        'name' => 'Letter Pro',
        'slug' => 'letter-pro',
        'type' => TemplateType::CoverLetter,
    ]);

    $resume = Resume::query()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'title' => 'Dev Resume',
        'slug' => 'dev-resume',
    ]);

    $coverLetter = CoverLetter::query()->create([
        'user_id' => $user->id,
        'template_id' => $template->id,
        'resume_id' => $resume->id,
        'title' => 'Senior Developer Cover Letter',
        'slug' => 'senior-developer',
        'recipient_name' => 'Alex Doe',
        'body' => 'Thank you for your consideration.',
    ]);

    expect($coverLetter->user->is($user))->toBeTrue()
        ->and($coverLetter->template?->supportsCoverLetter())->toBeTrue()
        ->and($coverLetter->resume?->is($resume))->toBeTrue();
});
