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
        if (\App\Models\Post::count() > 0) {
            return [
                CreateAction::make()
                    ->icon(Heroicon::Plus),
            ];
        }
        return [];
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PostStats::class
        ];
    }
}
