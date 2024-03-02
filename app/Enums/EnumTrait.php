<?php

declare(strict_types=1);

namespace App\Enums;

trait EnumTrait
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }

    public static function arrayInverted(): array
    {
        return array_combine(self::names(), self::values());
    }

    public static function options(string $langPrefix): array
    {
        $options = [];

        foreach (self::values() as $value) {
            $options[$value] = __($langPrefix . $value);
        }

        return $options;
    }
}
