<?php

namespace App\Filament\Resources\ProjectRoles\Pages;

use App\Filament\Resources\ProjectRoles\ProjectRoleResource;
use App\Filament\Widgets\ProjectRoleStats;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Override;

class ManageProjectRoles extends ManageRecords
{
    protected static string $resource = ProjectRoleResource::class;

    protected function getHeaderActions(): array
    {
        return $this->getModel()::count() > 0 ? [
            CreateAction::make()
                ->icon('heroicon-m-plus')
                ->successNotification(function ($record): Notification {
                    return Notification::make()
                        ->iconColor('success')
                        ->icon(Heroicon::Check)
                        ->title('Project Role Created')
                        ->body("The role '{$record->name}' has been added")
                        ->seconds(5)
                        ->send();
                })
        ] : [];
    }

    #[Override]
    public function getTitle(): string|Htmlable
    {
        return 'Roles';
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            ProjectRoleStats::class
        ];
    }
}
