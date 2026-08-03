<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use App\Enums\ProjectStatusEnum;
use Filament\Notifications\Notification;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->searchPlaceholder('Search projects...')
            ->emptyStateHeading('No projects found')
            ->emptyStateDescription('Create your first project')
            ->emptyStateIcon('heroicon-o-folder-open')
            ->emptyStateActions([
                CreateAction::make()
                    ->icon(Heroicon::Plus)
            ])
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),

                TextColumn::make('client')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project_role.name')
                    ->label('Role')
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('status')

            ])
            ->filters([
                //
            ])
            ->recordActions([

                ActionGroup::make([
                    Action::make('published_project')
                        ->label(fn($record) => $record->status === ProjectStatusEnum::Published ? 'Draft' : 'Publish')
                        ->color(fn($record) => $record->status === ProjectStatusEnum::Published ? 'warning' : 'success')
                        ->icon(fn($record) => $record->status === ProjectStatusEnum::Published ?  Heroicon::ExclamationTriangle : Heroicon::RocketLaunch)
                        ->action(function ($record) {
                            $oldStatus = $record->status;

                            $record->update([
                                'status' => $oldStatus == ProjectStatusEnum::Published ? ProjectStatusEnum::Draft : ProjectStatusEnum::Published
                            ]);

                            Notification::make('new_project_status_updated')
                                ->title('Project Status Updated')
                                ->seconds(5)
                                ->send();
                        }),

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
}
