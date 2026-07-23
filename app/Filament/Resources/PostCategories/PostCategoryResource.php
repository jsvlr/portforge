<?php

namespace App\Filament\Resources\PostCategories;

use App\Filament\Resources\PostCategories\Pages\ManagePostCategories;
use App\Models\PostCategory;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Override;
use UnitEnum;

class PostCategoryResource extends Resource
{
    protected static ?string $model = PostCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Tag;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Categories';

    protected static string|UnitEnum|null $navigationGroup = \App\Enums\NavigationGroup::Blogs;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->live()
                    ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state))),

                TextInput::make('slug')
                    ->readOnly()
                    ->required(),

                RichEditor::make('description')
                    ->afterLabel('optional')
                    ->columnSpanFull(),

                Toggle::make('is_active')
                    ->default(true)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Search categories...')
            ->recordTitleAttribute('name')
            ->emptyStateHeading('No categories found')
            ->emptyStateDescription(
                'Create your first category to start organizing posts.'
            )
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('New Category')
                    ->icon('heroicon-m-plus')
                    ->color('primary'),
            ])
            ->columns([
                TextColumn::make('name')
                    ->icon('heroicon-o-tag')
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Visibility')
                    ->sortable()
                    ->alignCenter()
                    ->boolean(),

                // ToggleColumn::make('is_active')
                //     ->label('Active')
                //     ->onColor('success')
                //     ->offColor('gray')
                //     ->alignCenter()
                //     ->afterStateUpdated(function ($record, $state) {

                //         $newState = $state ? "Activated" : "Deactivated";
                //         $icon = $state ? Heroicon::CheckCircle : Heroicon::ExclamationTriangle;
                //         $iconColor = $state ? 'success' : 'warning';

                //         Notification::make('is_active_updated')
                //             ->title("Record Updated!")
                //             ->body("$record->name has been $newState")
                //             ->icon($icon)
                //             ->iconColor($iconColor)
                //             ->send();

                //     }),

                TextColumn::make('created_at')
                    ->date('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->options([
                        1 => 'Active',
                        0 => 'Inactive',
                    ])
            ])
            ->recordActions([

                ActionGroup::make([
                    ViewAction::make(),
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
            'index' => ManagePostCategories::route('/'),
        ];
    }
}
