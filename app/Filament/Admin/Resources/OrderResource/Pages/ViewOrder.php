<?php

// Create this file: app/Filament/Admin/Resources/OrderResource/Pages/ViewOrder.php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Support\Facades\Auth;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('mark_processing')
                ->label('Start Processing')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('info')
                ->action(fn () => $this->record->update(['status' => 'processing']))
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->status === 'pending'),

            Actions\Action::make('mark_completed')
                ->label('Mark Completed')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(fn () => $this->record->update(['status' => 'completed']))
                ->requiresConfirmation()
                ->visible(fn (): bool => in_array($this->record->status, ['pending', 'processing'])),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Order Information')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('order_number')
                                    ->label('Order Number')
                                    ->copyable()
                                    ->badge()
                                    ->color('primary'),
                                
                                Components\TextEntry::make('status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'gray',
                                    }),
                                
                                Components\TextEntry::make('created_at')
                                    ->label('Order Date')
                                    ->dateTime('M j, Y H:i A'),
                            ]),
                    ]),

                Components\Section::make('Customer Information')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('customer_info')
                                    ->label('Customer Name')
                                    ->getStateUsing(function (): string {
                                        if ($this->record->user_id && $this->record->user) {
                                            return $this->record->user->name;
                                        }
                                        
                                        if ($this->record->customer_name) {
                                            return $this->record->customer_name;
                                        }
                                        
                                        if ($this->record->guest_details) {
                                            $details = is_string($this->record->guest_details) 
                                                ? json_decode($this->record->guest_details, true) 
                                                : $this->record->guest_details;
                                            
                                            if (is_array($details) && isset($details['name'])) {
                                                return $details['name'];
                                            }
                                        }
                                        
                                        return 'Guest Customer';
                                    }),
                                
                                Components\TextEntry::make('customer_phone')
                                    ->label('Phone')
                                    ->getStateUsing(function (): ?string {
                                        if ($this->record->customer_phone) {
                                            return $this->record->customer_phone;
                                        }
                                        
                                        if ($this->record->guest_details) {
                                            $details = is_string($this->record->guest_details) 
                                                ? json_decode($this->record->guest_details, true) 
                                                : $this->record->guest_details;
                                            
                                            if (is_array($details) && isset($details['phone'])) {
                                                return $details['phone'];
                                            }
                                        }
                                        
                                        return null;
                                    }),
                            ]),
                    ]),

                Components\Section::make('Items from My Stall')
                    ->schema([
                        Components\RepeatableEntry::make('my_stall_items')
                            ->label('')
                            ->getStateUsing(function (): array {
                                $stallId = Auth::user()->admin_stall_id;
                                if (!$stallId) return [];

                                return $this->record->items()
                                    ->whereHas('product', function ($query) use ($stallId) {
                                        $query->where('stall_id', $stallId);
                                    })
                                    ->with('product')
                                    ->get()
                                    ->toArray();
                            })
                            ->schema([
                                Components\Grid::make(4)
                                    ->schema([
                                        Components\TextEntry::make('product.name')
                                            ->label('Product')
                                            ->weight('semibold'),
                                        
                                        Components\TextEntry::make('quantity')
                                            ->label('Qty')
                                            ->alignCenter(),
                                        
                                        Components\TextEntry::make('unit_price')
                                            ->label('Unit Price')
                                            ->money('PHP')
                                            ->alignEnd(),
                                        
                                        Components\TextEntry::make('subtotal')
                                            ->label('Subtotal')
                                            ->money('PHP')
                                            ->weight('semibold')
                                            ->alignEnd(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Components\Section::make('Order Summary')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('my_stall_revenue')
                                    ->label('My Stall Revenue')
                                    ->getStateUsing(function (): float {
                                        $stallId = Auth::user()->admin_stall_id;
                                        if (!$stallId) return 0;

                                        return $this->record->items()
                                            ->whereHas('product', function ($query) use ($stallId) {
                                                $query->where('stall_id', $stallId);
                                            })
                                            ->sum('subtotal');
                                    })
                                    ->money('PHP')
                                    ->weight('semibold')
                                    ->color('success'),
                                
                                Components\TextEntry::make('total_order_amount')
                                    ->label('Total Order Amount')
                                    ->getStateUsing(function (): float {
                                        return $this->record->total_amount ?? ($this->record->amount_total / 100);
                                    })
                                    ->money('PHP')
                                    ->weight('semibold'),
                            ]),
                    ]),

                Components\Section::make('Payment & Delivery')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('payment_method')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'Not Set')
                                    ->color(fn (?string $state): string => match ($state) {
                                        'cash' => 'success',
                                        'gcash' => 'info',
                                        'card' => 'warning',
                                        'paymaya' => 'primary',
                                        default => 'gray',
                                    }),
                                
                                Components\TextEntry::make('payment_status')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'paid' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        default => 'gray',
                                    }),
                                
                                Components\TextEntry::make('order_type')
                                    ->formatStateUsing(fn ($state) => $state === 'online' ? 'Online Order' : 'Walk-in Order')
                                    ->badge(),
                            ]),
                    ]),

                Components\Section::make('Special Instructions')
                    ->schema([
                        Components\TextEntry::make('special_instructions')
                            ->label('')
                            ->placeholder('No special instructions')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn (): bool => !empty($this->record->special_instructions)),
            ]);
    }
}