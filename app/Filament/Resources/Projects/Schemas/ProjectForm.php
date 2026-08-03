<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Resources\ProjectRoles\ProjectRoleResource;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns([
                'lg' => 4,
                'sm' => 1
            ])
            ->components([
                TextInput::make('title')
                    ->required()
                    ->placeholder('Employee Management System')
                    ->live()
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', blank($state) ? '' : Str::slug($state)))
                    ->columnSpan(2),

                TextInput::make('slug')
                    ->required()
                    ->readOnly(),

                Select::make('project_role_id')
                    ->label('Role')
                    ->relationship('project_role', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->live()
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', blank($state) ? '' : Str::slug($state))),

                        TextInput::make('slug')
                            ->required()
                            ->readOnly(),
                    ]),

                TextInput::make('client')
                    ->required(),

                Select::make('status')
                    ->options(\App\Enums\ProjectStatusEnum::class),

                DatePicker::make('started_at')
                    ->default(now()),

                DatePicker::make('completed_at'),



                FileUpload::make('gallery')
                    ->required()
                    ->image()
                    ->multiple()
                    ->maxFiles(5)
                    ->reorderable()
                    ->directory('project_images')
                    ->disk('public')
                    ->columnSpan(1),

                RichEditor::make('content')
                    ->columnSpan(3),

            ]);
    }
}
