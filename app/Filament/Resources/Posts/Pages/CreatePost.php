<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Resources\Pages\CreateRecord;
use Override;
use Str;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    // Creating a SEO based on the title and description of the data inputted
    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // return parent::mutateFormDataBeforeCreate($data);
        $data['meta_title'] = $data['title'];
        $data['met_description'] = $data['excerpt'];

        return $data;
    }
}
