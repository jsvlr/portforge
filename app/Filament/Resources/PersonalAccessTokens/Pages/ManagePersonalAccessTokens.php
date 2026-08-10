<?php

namespace App\Filament\Resources\PersonalAccessTokens\Pages;

use App\Filament\Resources\PersonalAccessTokens\PersonalAccessTokenResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Checkbox;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard;

class ManagePersonalAccessTokens extends ManageRecords
{
    protected static string $resource = PersonalAccessTokenResource::class;

    public ?string $plainTextToken = null;
    public ?Model $createdToken = null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New token')
                ->icon(Heroicon::Plus)
                ->steps([
                    Wizard\Step::make('Details')
                        ->schema([
                            TextInput::make('name')
                                ->required(),
                        ])
                        ->afterValidation(function (Get $get) {
                            $newToken = filament()->auth()->user()->createToken(
                                name: $get('name'),
                                abilities: ['*']
                            );
                            $this->plainTextToken = $newToken->plainTextToken;
                            $this->createdToken = $newToken->accessToken;
                        }),

                    Wizard\Step::make('Token')
                        ->schema([
                            Section::make()
                                ->icon(Heroicon::ExclamationTriangle)
                                ->iconColor('warning')
                                ->heading('Save your token now')
                                ->description('For security reason, this token will never be shown again')
                                ->schema([
                                    TextEntry::make('token')
                                        ->label('Your new access token')
                                        ->state(fn() => $this->plainTextToken)
                                        ->copyable()
                                        ->copyMessage('Copied to clipboard!')
                                        ->copyMessageDuration(1500)
                                        ->weight('bold')
                                        ->size('lg')
                                        ->fontFamily('mono')
                                        ->color('warning')
                                        ->extraAttributes([
                                            'class' => 'text-center break-all bg-warning-50 dark:bg-warning-950/30 rounded-lg py-4 px-3 border border-warning-200 dark:border-warning-800',
                                        ]),
                                ])
                                ->extraAttributes([
                                    'class' => 'border-warning-300 dark:border-warning-700',
                                ]),

                            Checkbox::make('confirmed_copy')
                                ->label('I have copied this token and understand it will not be shown again')
                                ->required()
                                ->accepted()
                                ->live(),
                        ])

                ])
                ->using(fn() => $this->createdToken),
        ];
    }
}
