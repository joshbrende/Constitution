<?php

namespace App\Enums;

enum MembershipSource: string
{
    case Academy = 'academy';
    case Invite = 'invite';
    case AdminCreated = 'admin_created';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
