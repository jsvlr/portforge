<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Override;

enum ProjectStatusEnum: string implements HasColor, HasLabel
{
    case Published = 'published';
    case Draft = 'draft';


    #[Override]
    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Published => 'Published',
            self::Draft => 'Draft'
        };
    }

    #[Override]
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Published => 'success',
            self::Draft => 'warning'
        };
    }
}
