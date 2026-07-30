<?php

namespace App\Filament\Resources\ProjectRoles;

use App\Filament\Resources\ProjectRoles\Pages\ManageProjectRoles;
use App\Models\ProjectRole;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class ProjectRoleResource extends Resource
{
    protected static ?string $model = ProjectRole::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bolt;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Roles';

    protected static string|UnitEnum|null $navigationGroup = \App\Enums\NavigationGroup::Projects;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live()
                    ->maxLength(100)
                    ->afterStateUpdated(fn(Set $set, ?string $state) =>  $set('slug', blank($state) ? '' : Str::slug($state))),

                TextInput::make('slug')
                    ->required()
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Search roles...')
            ->recordTitleAttribute('name')
            ->emptyStateHeading('No roles found')
            ->emptyStateDescription(
                'Create your first role to start organizing'
            )
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('New Role')
                    ->icon('heroicon-m-plus')
            ])
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->icon(Heroicon::User)
                    ->weight(FontWeight::Bold)
                    ->iconColor('primary'),

                TextColumn::make('projects_count')
                    ->sortable()
                    ->label('Projects')
                    ->counts('projects')
                    ->default(0)
                    ->badge(),

                TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->searchable(),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProjectRoles::route('/'),
        ];
    }
}
