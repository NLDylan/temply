<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resume extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
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
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'profile' => 'array',
            'settings' => 'array',
            'expires_at' => 'immutable_datetime',
            'locked_at' => 'immutable_datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function coverLetters(): HasMany
    {
        return $this->hasMany(CoverLetter::class);
    }

    public function education(): HasMany
    {
        return $this->hasMany(ResumeEducation::class)->orderBy('sort_order');
    }

    public function experience(): HasMany
    {
        return $this->hasMany(ResumeExperience::class)->orderBy('sort_order');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(ResumeSkill::class)->orderBy('sort_order');
    }

    public function languages(): HasMany
    {
        return $this->hasMany(ResumeLanguage::class)->orderBy('sort_order');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(ResumeCertification::class)->orderBy('sort_order');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(ResumeProject::class)->orderBy('sort_order');
    }

    public function volunteering(): HasMany
    {
        return $this->hasMany(ResumeVolunteering::class)->orderBy('sort_order');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(ResumeAchievement::class)->orderBy('sort_order');
    }
}
