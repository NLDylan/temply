<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeEducation extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var string
     */
    protected $table = 'resume_educations';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'resume_id',
        'institution',
        'degree',
        'field_of_study',
        'location',
        'started_on',
        'ended_on',
        'is_current',
        'description',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'started_on' => 'date',
            'ended_on' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
