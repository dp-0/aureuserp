<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\Pages;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\ActivityPlanResource\RelationManagers;
use Webkul\Sale\Models\ActivityPlan;
use Webkul\Security\Filament\Resources\CompanyResource;

class ActivityPlanResource extends Resource
{
    protected static ?string $model = ActivityPlan::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $cluster = Configuration::class;

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/activity-plan.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('sales::filament/clusters/configurations/resources/activity-plan.navigation.group');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'department.name', 'company.name', 'plugin', 'createdBy.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('sales::filament/clusters/configurations/resources/activity-plan.global-search.name')         => $record->name ?? '—',
            __('sales::filament/clusters/configurations/resources/activity-plan.global-search.manager')      => $record->department?->manager?->name ?? '—',
            __('sales::filament/clusters/configurations/resources/activity-plan.global-search.company')      => $record->company?->name ?? '—',
            __('sales::filament/clusters/configurations/resources/activity-plan.global-search.plugin')       => $record->plugin ?? '—',
            __('sales::filament/clusters/configurations/resources/activity-plan.global-search.creator-name') => $record->createdBy?->name ?? '—',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('sales::filament/clusters/configurations/resources/activity-plan.form.sections.general.title'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('company_id')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.company'))
                            ->relationship(name: 'company', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->createOptionForm(fn (Form $form) => CompanyResource::form($form))
                            ->editOptionForm(fn (Form $form) => CompanyResource::form($form)),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.form.sections.general.fields.status'))
                            ->default(true)
                            ->inline(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.department'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.manager.name')
                    ->numeric()
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.manager'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->numeric()
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.company'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.status'))
                    ->sortable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.created-by'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.is-active')),
                Tables\Filters\QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.name'))
                            ->icon('heroicon-o-briefcase'),
                        Tables\Filters\QueryBuilder\Constraints\TextConstraint::make('plugin')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.plugin'))
                            ->icon('heroicon-o-briefcase'),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('activityTypes')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.activity-types'))
                            ->icon('heroicon-o-briefcase')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.activity-types'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('company')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.company'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint::make('department')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.department'))
                            ->icon('heroicon-o-building-office-2')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.department'))
                                    ->multiple()
                                    ->preload(),
                            ),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('created_at')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.created-at')),
                        Tables\Filters\QueryBuilder\Constraints\DateConstraint::make('updated_at')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.filters.updated-at')),
                    ]),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('is_active')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('sales::filament/clusters/configurations/resources/activity-plan.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                Tables\Actions\EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                Tables\Actions\RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.restore.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.restore.notification.body')),
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.delete.notification.body')),
                    ),
                Tables\Actions\ForceDeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.force-delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.actions.force-delete.notification.body')),
                    ),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.restore.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.restore.notification.body')),
                        ),
                    Tables\Actions\DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.delete.notification.body')),
                        ),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/activity-plan.table.empty-state.create.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/activity-plan.table.empty-state.create.notification.body')),
                    ),
            ])
            ->modifyQueryUsing(function ($query) {
                $query->where('plugin', 'sales');
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.title'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.name'))
                            ->icon('heroicon-o-briefcase')
                            ->placeholder('—'),
                        Infolists\Components\TextEntry::make('department.name')
                            ->icon('heroicon-o-building-office-2')
                            ->placeholder('—')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.department')),
                        Infolists\Components\TextEntry::make('department.manager.name')
                            ->icon('heroicon-o-user')
                            ->placeholder('—')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.manager')),
                        Infolists\Components\TextEntry::make('company.name')
                            ->icon('heroicon-o-building-office')
                            ->placeholder('—')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.company')),
                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('sales::filament/clusters/configurations/resources/activity-plan.infolist.sections.general.entries.status'))
                            ->boolean(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivityTemplateRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListActivityPlans::route('/'),
            'view'   => Pages\ViewActivityPlan::route('/{record}'),
            'edit'   => Pages\EditActivityPlan::route('/{record}/edit'),
        ];
    }
}
