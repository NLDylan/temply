<?php

namespace App\Enums;

enum TemplateType: string
{
    case Cv = 'cv';
    case CoverLetter = 'cover_letter';
    case Both = 'both';

    /**
     * Determine if the template can be used for CVs.
     */
    public function supportsCv(): bool
    {
        return $this === self::Cv || $this === self::Both;
    }

    /**
     * Determine if the template can be used for cover letters.
     */
    public function supportsCoverLetter(): bool
    {
        return $this === self::CoverLetter || $this === self::Both;
    }
}
