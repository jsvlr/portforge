<?php

namespace App\Filament\Resources\PostCategories\Pages;

use App\Filament\Resources\PostCategories\PostCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Override;

class ManagePostCategories extends ManageRecords
{
    protected static string $resource = PostCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return $this->getModel()::count() > 0 ? [
            CreateAction::make()
                ->icon('heroicon-m-plus')
        ] : [];
    }

    public function getTitle(): string
    {
        return 'Post Categories';
    }

    #[Override]
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\PostCategoryStats::class
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Manage categories used to organize your blog posts.';
    }
}
