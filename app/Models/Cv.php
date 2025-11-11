<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cv extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
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
     * The attributes that should be cast.
     *
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

    /**
     * The user that owns this CV.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The template associated with this CV.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Cover letters associated with this CV.
     */
    public function coverLetters(): HasMany
    {
        return $this->hasMany(CoverLetter::class);
    }

    /**
     * Education entries attached to the CV.
     */
    public function education(): HasMany
    {
        return $this->hasMany(CvEducation::class)->orderBy('sort_order');
    }

    /**
     * Experience entries attached to the CV.
     */
    public function experience(): HasMany
    {
        return $this->hasMany(CvExperience::class)->orderBy('sort_order');
    }

    /**
     * Skills associated with the CV.
     */
    public function skills(): HasMany
    {
        return $this->hasMany(CvSkill::class)->orderBy('sort_order');
    }

    /**
     * Languages associated with the CV.
     */
    public function languages(): HasMany
    {
        return $this->hasMany(CvLanguage::class)->orderBy('sort_order');
    }

    /**
     * Certifications associated with the CV.
     */
    public function certifications(): HasMany
    {
        return $this->hasMany(CvCertification::class)->orderBy('sort_order');
    }

    /**
     * Projects associated with the CV.
     */
    public function projects(): HasMany
    {
        return $this->hasMany(CvProject::class)->orderBy('sort_order');
    }
}
