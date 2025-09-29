<?php

namespace App\Filament\Admin\Resources\ExpenseResource\Pages;

use App\Filament\Admin\Resources\ExpenseResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Expense recorded')
            ->body('The expense has been saved successfully.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = \Illuminate\Support\Facades\Auth::id();
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // You can add any additional logic here after expense creation
        // For example, sending notifications, updating budgets, etc.
    }
}