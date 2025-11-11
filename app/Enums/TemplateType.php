<?php

namespace App\Enums;

enum TemplateType: string
{
    case Resume = 'resume';
    case CoverLetter = 'cover_letter';
    case Both = 'both';

    /**
     * Determine if the template can be used for resumes.
     */
    public function supportsResume(): bool
    {
        return $this === self::Resume || $this === self::Both;
    }

    /**
     * Determine if the template can be used for cover letters.
     */
    public function supportsCoverLetter(): bool
    {
        return $this === self::CoverLetter || $this === self::Both;
    }
}
