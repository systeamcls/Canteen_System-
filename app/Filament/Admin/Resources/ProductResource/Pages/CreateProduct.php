<?php

// app/Filament/Admin/Resources/ProductResource/Pages/CreateProduct.php
namespace App\Filament\Admin\Resources\ProductResource\Pages;

use App\Filament\Admin\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function handleRecordCreation(array $data): Model
    {   
        // Set created_by to current user
        $data['created_by'] = Auth::id();
        
        return parent::handleRecordCreation($data);
    }

    protected function getCreateFormAction(): Actions\Action
    {
        return parent::getCreateFormAction()
            ->label('Add Product')
            ->submit(null)
            ->keyBindings(['mod+s'])
            ->action(function () {
                $this->create();
                
                // Show success notification with next steps
                \Filament\Notifications\Notification::make()
                    ->title('Product added successfully!')
                    ->body('Your product is now available in your stall menu.')
                    ->success()
                    ->actions([
                        \Filament\Notifications\Actions\Action::make('create_another')
                            ->label('Add Another Product')
                            ->button()
                            ->url($this->getResource()::getUrl('create')),
                        \Filament\Notifications\Actions\Action::make('view_products')
                            ->label('View All Products')
                            ->button()
                            ->url($this->getResource()::getUrl('index')),
                    ])
                    ->send();
            });
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}