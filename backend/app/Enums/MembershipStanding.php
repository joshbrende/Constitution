<?php

namespace App\Enums;

enum MembershipStanding: string
{
    case Applicant = 'applicant';
    case Provisional = 'provisional';
    case Member = 'member';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Applicant => 'Applicant',
            self::Provisional => 'Provisional member',
            self::Member => 'Full member',
            self::Suspended => 'Suspended',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
