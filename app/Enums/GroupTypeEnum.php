<?php

declare(strict_types=1);

namespace App\Enums;

enum GroupTypeEnum: string implements EnumInterface
{
    use EnumTrait;

    case Credential = 'credential';
    case Note = 'note';

    public static function default(): static
    {
        return self::Credential;
    }
}
