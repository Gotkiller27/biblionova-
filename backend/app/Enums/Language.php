<?php

namespace App\Enums;

enum Language: string
{
    case FRENCH = 'fr';
    case ENGLISH = 'en';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match($this) {
            self::FRENCH => 'Français',
            self::ENGLISH => 'English',
            self::OTHER => 'Autre',
        };
    }

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
