<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'lg' => 4,
                'sm' => 1
            ])
            ->components([
                FileUpload::make('cover_image')
                    ->label('Thumbnail')
                    ->disk('public')
                    ->directory('posts_cover_images')
                    ->columnSpanFull()
                    ->image()
                    ->imageEditor(),

                TextInput::make('title')
                    ->required()
                    ->placeholder('How to create a new repo on github')
                    ->live()
                    ->columnSpan(2)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if (!blank($state)) {
                            $set('slug', Str::slug($state));
                            return;
                        }
                        $set('slug', '');
                    }),

                TextInput::make('slug')
                    ->required()
                    ->helperText('this is auto generated'),

                Select::make('post_category_id')
                    ->label('Category')
                    ->relationship('post_category', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm(
                        \App\Filament\Resources\PostCategories\PostCategoryResource::getFormSchema()
                    ),

                TextInput::make('excerpt')
                    ->columnSpanFull()
                    ->helperText('short description for the post'),

                RichEditor::make('content')
                    ->columnSpanFull(),

                DatePicker::make('published_at')
                    ->default(now()),

                TagsInput::make('tags')
                    ->suggestions(
                        fn() => \App\Models\Post::query()
                            ->pluck('tags')
                            ->flatten()
                            ->unique()
                            ->values()
                            ->toArray()
                    )
                    ->separator(',')
                    ->splitKeys(['Tab', ',']),


                Select::make('status')
                    ->options(\App\Enums\PostStatusEnum::class)
                    ->default(\App\Enums\PostStatusEnum::default())
            ]);
    }
}
