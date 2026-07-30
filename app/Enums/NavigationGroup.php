<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;
use Override;

enum NavigationGroup: string implements HasLabel
{
    case Projects = 'projects';
    case Blogs = 'blogs';
    case Settings = 'settings';


    #[Override]
    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Projects => 'Projects',
            self::Blogs => 'Blogs',
            self::Settings => 'Settings',
        };
    }
}
