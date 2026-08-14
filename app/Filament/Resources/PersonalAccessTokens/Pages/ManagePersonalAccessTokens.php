<?php

namespace App\Filament\Resources\PersonalAccessTokens\Pages;

use App\Filament\Resources\PersonalAccessTokens\PersonalAccessTokenResource;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Hamcrest\Arrays\IsArrayContainingKeyValuePair;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Laravel\Prompts\Elements\KeyValueList;
use Symfony\Component\Finder\Finder;

class ManagePersonalAccessTokens extends ManageRecords
{
    protected static string $resource = PersonalAccessTokenResource::class;

    public ?string $plainTextToken = null;
    public ?Model  $createdToken = null;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('New token')
                ->icon(Heroicon::Plus)
                ->steps([
                    $this->getDetailsStep(),
                    $this->getPermissionsForAllModels(),
                    $this->getTokenStep(),
                ])
                ->using(fn() => $this->createdToken),
        ];
    }

    private function getDetailsStep(): Step
    {
        return Step::make('Details')
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->prefixIcon(Heroicon::OutlinedKey)
                    ->required(),
            ]);
    }

    private function getPermissionsForAllModels(): Step
    {
        $sections = [];
        foreach ($this->getModelOptions() as $k => $v) {
            $sections[] = Section::make($v)
                ->collapsible()
                ->schema([
                    CheckboxList::make("abilities.$k")
                        ->required()
                        ->label('Token Permissions')
                        ->options([
                            "$k:read" => 'Read',
                            "$k:create" => 'Create',
                            "$k:update" => 'Update',
                            "$k:delete" => 'Delete',
                        ])
                        ->descriptions([
                            "$k:read" => 'View resources',
                            "$k:create" => 'Create new resources',
                            "$k:update" => 'Modify existing resources',
                            "$k:delete" => 'Remove resources permanently',
                        ])
                        ->default(["$k:read"])
                        ->columns(2)
                ]);
        }
        return Step::make('Models')
            ->columns(2)
            ->schema($sections)
            ->afterValidation(function (Get $get) {
                $name = $get('name');
                $abilities = collect($get('abilities'))->flatten()->toArray();

                $newToken = filament()->auth()->user()->createToken(
                    name: $name,
                    abilities: $abilities
                );

                $this->plainTextToken = $newToken->plainTextToken;
                $this->createdToken = $newToken->accessToken;
            });
    }

    private function getTokenStep(): Step
    {
        return Step::make('Token')
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
            ]);
    }

    private function getModelOptions(): Collection
    {
        return collect(Finder::create()->files()->in(app_path('Models')))
            ->map(fn($file) => 'App\\Models\\' . Str::replace(
                ['/', '.php'],
                ['\\', ''],
                Str::after($file->getRealPath(), app_path('Models') . DIRECTORY_SEPARATOR)
            ))
            ->filter(fn($class) => class_exists($class) && is_subclass_of($class, Model::class) && (new $class)->getTable() !== 'users')
            ->mapWithKeys(fn($class) => [(new $class)->getTable() => Str::headline(class_basename($class))]);
    }
}
