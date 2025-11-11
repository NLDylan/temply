<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResumeCertification extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'resume_id',
        'name',
        'issuer',
        'issued_on',
        'expires_on',
        'credential_id',
        'credential_url',
        'description',
        'sort_order',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'expires_on' => 'date',
            'metadata' => 'array',
        ];
    }

    public function resume(): BelongsTo
    {
        return $this->belongsTo(Resume::class);
    }
}
