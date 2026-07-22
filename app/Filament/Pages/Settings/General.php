<?php

namespace App\Filament\Pages\Settings;

use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconPosition;
use Outerweb\FilamentSettings\Pages\Settings;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Override;
use UnitEnum;

class General extends Settings
{
    protected string $brandDisk = 'public';
    protected string $brandDirectory = 'brand';
    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected function brandLogoSetting(FileUpload $fileUpload): FileUpload
    {
        return $fileUpload
            ->image()
            ->disk($this->brandDisk)
            ->directory($this->brandDirectory)
            ->imageEditor();
    }

    #[Override]
    public function getSavedNotification(): ?Notification
    {
        return Notification::make('saved')
            ->title('Saved Successfully')
            ->body('New settings has been saved successfully')
            ->icon(Heroicon::CheckCircle)
            ->iconColor('success')
            ->send();
    }

    #[Override]
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->vertical()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Site Identity')
                            ->icon('heroicon-o-identification')
                            ->iconPosition(IconPosition::Before)
                            ->schema([

                                Callout::make('Missing required fields')
                                    ->description('Some required brand fields are empty. Fill these in before publishing.')
                                    ->danger()
                                    ->visible(
                                        fn(Get $get) =>
                                        blank($get('general.brand_name')) ||
                                            blank($get('general.tagline')) ||
                                            blank($get('general.favicon')) ||
                                            blank($get('general.brand_logo')) ||
                                            blank($get('general.brand_logo_dark'))
                                    ),

                                Section::make('Details')
                                    ->description('Your site name and tagline.')
                                    ->columns(2)
                                    ->collapsible()
                                    ->schema([

                                        TextInput::make('general.brand_name')
                                            ->label('Name')
                                            ->live()
                                            ->default(config('app.name'))
                                            ->placeholder('Laravel')
                                            ->required(),

                                        TextInput::make('general.tagline')
                                            ->label('Tagline')
                                            ->placeholder('I code because its my life'),

                                    ]),

                                Section::make('Brand Assets')
                                    ->description('Logos and favicon used across the site.')
                                    ->columns(3)
                                    ->collapsible()
                                    ->schema([

                                        $this->brandLogoSetting(
                                            FileUpload::make('general.brand_logo')
                                                ->label('Primary')
                                        ),

                                        $this->brandLogoSetting(
                                            FileUpload::make('general.brand_logo_dark')
                                                ->label('Dark Mode')
                                        ),

                                        FileUpload::make('general.favicon')
                                            ->label('Favicon')
                                            ->disk($this->brandDisk)
                                            ->directory($this->brandDirectory)
                                            ->acceptedFileTypes(['.ico'])
                                            ->helperText('Only .ico files are accepted.'),
                                    ]),
                            ]),

                        Tab::make('About')
                            ->icon('heroicon-o-user-circle')
                            ->iconPosition(IconPosition::Before)
                            ->columns(2)
                            ->schema([
                                Section::make('Personal Information')
                                    ->description('Basic personal details.')
                                    ->collapsible()
                                    ->columnSpanFull()
                                    ->columns(7)
                                    ->schema([
                                        TextInput::make('general.firstname')
                                            ->columnSpan(2)
                                            ->placeholder('Pedro')
                                            ->required(),

                                        TextInput::make('general.middle_initial')
                                            ->columnSpan(2)
                                            ->placeholder('Gil')
                                            ->afterLabel('optional'),


                                        TextInput::make('general.lastname')
                                            ->columnSpan(2)
                                            ->placeholder('Santos')
                                            ->required(),

                                        TextInput::make('general.extension')
                                            // ->columnSpan(1)
                                            ->label('Ext.')
                                            ->placeholder('Jr.')
                                            ->afterLabel('if Any'),

                                        DatePicker::make('general.date_of_birth')
                                            ->columnSpan(3)
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                                if (blank($state)) {
                                                    $set('general.age', null);
                                                    return;
                                                }

                                                $age = Carbon::parse($state)->age;

                                                $set('general.age', $age);
                                            })
                                            ->required(),

                                        TextInput::make('general.age')
                                            ->numeric()
                                            ->readOnly()
                                            ->required(),

                                        Select::make('general.gender')
                                            ->columnSpan(2)
                                            ->live()
                                            ->options([
                                                'male'   => 'Male',
                                                'female' => 'Female',
                                                'other'  => 'Other'
                                            ])
                                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                                if ($state != 'other') {
                                                    $set('general.other_gender', null);
                                                }
                                            }),

                                        TextInput::make('general.other_gender')
                                            ->label('If any')
                                            ->disabled(fn(Get $get) => $get('general.gender') != 'other')

                                    ]),

                                Section::make('Professional Information')
                                    ->description('Your professional identity.')
                                    ->collapsible()
                                    ->columnSpanFull()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('general.profession')
                                            ->columnSpan(2)
                                            ->label('Current Profession')
                                            ->live(),

                                        TextInput::make('general.job_title')
                                            ->columnSpan(1)
                                            ->label('Job Title'),

                                        TextInput::make('general.company'),

                                        DatePicker::make('general.start_date_working')
                                            ->disabled(fn(Get $get) => blank($get('general.profession'))),

                                        DatePicker::make('general.end_date_working')
                                            ->disabled(fn(Get $get) => blank($get('general.start_date_working'))),

                                        Select::make('general.employment_status')
                                            ->options([
                                                'student' => 'Student',
                                                'freelancer' => 'Freelancer',
                                                'employed' => 'Employed',
                                                'open_to_work' => 'Open to Work'
                                            ])
                                    ])
                            ])
                    ]),

            ]);
    }
}
