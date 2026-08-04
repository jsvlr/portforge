<?php

namespace App\Filament\Widgets;

use App\Models\ProjectRole;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectRoleStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Roles', number_format(ProjectRole::count()))
        ];
    }
}
