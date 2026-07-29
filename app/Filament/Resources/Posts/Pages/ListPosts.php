<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Override;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {

        return $this->getModel()::count() > 0 ? [
            CreateAction::make()
                ->icon('heroicon-m-plus')
        ] : [];
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PostStats::class
        ];
    }
}
