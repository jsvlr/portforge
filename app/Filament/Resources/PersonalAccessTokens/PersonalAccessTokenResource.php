<?php

namespace App\Filament\Resources\PersonalAccessTokens;

use App\Filament\Resources\PersonalAccessTokens\Pages\ManagePersonalAccessTokens;
use Laravel\Sanctum\PersonalAccessToken;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class PersonalAccessTokenResource extends Resource
{
    protected static ?string $model = PersonalAccessToken::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'API Tokens';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('abilities')
                    ->badge()
                    ->separator(',')
                    ->formatStateUsing(fn($state) => $state),

            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => ManagePersonalAccessTokens::route('/'),
        ];
    }

    #[Override]
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tokenable_id', filament()->auth()->id())
            ->where('tokenable_type', (new (filament()->auth()->user()::class))->getMorphClass());
    }

    #[Override]
    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }

    #[Override]
    public static function getNavigationBadgeTooltip(): string|Htmlable|null
    {
        return 'token created';
    }
}
