<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class Notifications extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.admin.pages.notifications';

    public function table(Table $table): Table
    {
        return $table
            ->query(Auth::user()->notifications()->getQuery())
            ->columns([
                IconColumn::make('read_at')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('success')
                    ->falseColor('warning'),
                
                TextColumn::make('data.title')
                    ->label('Title')
                    ->weight('semibold')
                    ->limit(50),
                
                TextColumn::make('data.message')
                    ->label('Message')
                    ->limit(100)
                    ->wrap(),
                
                TextColumn::make('type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => 
                        str_replace('App\\Notifications\\', '', $state))
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'App\\Notifications\\OrderStatusNotification' => 'success',
                        'App\\Notifications\\NewOrderNotification' => 'warning',
                        'App\\Notifications\\ExpenseApprovalNotification' => 'info',
                        default => 'gray',
                    }),
                
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->since(),
            ])
            ->actions([
                Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->action(fn (DatabaseNotification $record) => $record->markAsRead())
                    ->visible(fn (DatabaseNotification $record) => is_null($record->read_at)),
                
                Action::make('mark_unread')
                    ->label('Mark Unread')
                    ->icon('heroicon-m-minus')
                    ->color('warning')
                    ->action(fn (DatabaseNotification $record) => $record->markAsUnread())
                    ->visible(fn (DatabaseNotification $record) => !is_null($record->read_at)),
                
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->action(fn (DatabaseNotification $record) => $record->delete())
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkAction::make('mark_all_read')
                    ->label('Mark All as Read')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->action(fn ($records) => $records->each->markAsRead()),
                
                \Filament\Tables\Actions\BulkAction::make('delete_selected')
                    ->label('Delete Selected')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->action(fn ($records) => $records->each->delete())
                    ->requiresConfirmation(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('10s')
            ->emptyStateHeading('No notifications')
            ->emptyStateDescription('You will see notifications here when there are updates.')
            ->emptyStateIcon('heroicon-o-bell-slash');
    }

    public function markAllAsRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        \Filament\Notifications\Notification::make()
            ->title('All notifications marked as read')
            ->success()
            ->send();
    }

    public function deleteAll(): void
    {
        Auth::user()->notifications()->delete();
        
        \Filament\Notifications\Notification::make()
            ->title('All notifications deleted')
            ->success()
            ->send();
    }
}