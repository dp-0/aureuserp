<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource\Pages;
use Webkul\Inventory\Models\Receipt;

class ReceiptResource extends Resource
{
    protected static ?string $model = Receipt::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $cluster = Operations::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/receipt.navigation.group');
    }

    public static function form(Form $form): Form
    {
        return OperationResource::form($form);
    }

    public static function table(Table $table): Table
    {
        return OperationResource::table($table)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()
                        ->hidden(fn (Receipt $record): bool => $record->state == Enums\OperationState::DONE)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('inventories::filament/clusters/operations/resources/receipt.table.actions.delete.notification.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/receipt.table.actions.delete.notification.body')),
                        ),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\ActionGroup::make([
                ])
                    ->icon('heroicon-o-printer')
                    ->label('Print')
                    ->color('gray')
                    ->button(),
                Tables\Actions\DeleteBulkAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/receipt.table.bulk-actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/receipt.table.bulk-actions.delete.notification.body')),
                    ),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->whereHas('operationType', function (Builder $query) {
                    $query->where('type', Enums\OperationType::INCOMING);
                });
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return OperationResource::infolist($infolist);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewReceipt::class,
            Pages\EditReceipt::class,
            Pages\ManageMoves::class,
        ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReceipts::route('/'),
            'create' => Pages\CreateReceipt::route('/create'),
            'edit'   => Pages\EditReceipt::route('/{record}/edit'),
            'view'   => Pages\ViewReceipt::route('/{record}/view'),
            'moves'  => Pages\ManageMoves::route('/{record}/moves'),
        ];
    }
}
