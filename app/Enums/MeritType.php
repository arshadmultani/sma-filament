<?php

namespace App\Enums;

enum MeritType: string
{
    case Education = 'education';
    case License = 'license';
    case Certification = 'certification';
    case Award = 'award';
    case Publication = 'publication';
    case Membership = 'membership';

    public static function getOptions(): array
    {
        return collect(self::cases())->mapWithKeys(fn($case) => [$case->value => $case->getLabel()])->all();
    }

    public function getLabel(): string
    {

        return match ($this) {
            self::Education => 'Education',
            self::License => 'License',
            self::Certification => 'Certification',
            self::Award => 'Award',
            self::Publication => 'Publication',
            self::Membership => 'Membership',
        };
    }
}
