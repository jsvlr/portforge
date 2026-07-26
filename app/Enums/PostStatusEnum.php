<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;
use Illuminate\Contracts\Support\Htmlable;

use Override;

enum PostStatusEnum: string implements HasLabel, HasColor
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';

    public static function default(): string
    {
        return self::Draft->value;
    }

    #[Override]
    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Published => 'Published',
            self::Scheduled => 'Scheduled'
        };
    }

    #[Override]
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'danger',
            self::Published => 'success',
            self::Scheduled => 'warning'
        };
    }
}
