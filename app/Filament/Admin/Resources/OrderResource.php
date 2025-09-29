<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Carbon\Carbon;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Order Oversight';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?int $navigationSort = 1;


    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        return parent::getEloquentQuery()
            ->with(['user', 'orderItems.product', 'orderGroup'])
            ->when($stallId, function (Builder $query) use ($stallId) {
                $query->whereHas('orderItems', function (Builder $itemQuery) use ($stallId) {
                    $itemQuery->whereHas('product', function (Builder $productQuery) use ($stallId) {
                        $productQuery->where('stall_id', $stallId);
                    });
                });
            })
            ->when(!$stallId, function (Builder $query) {
                $query->whereRaw('1 = 0');
            })
            ->latest();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Order Information')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('order_number')
                                    ->label('Order Number')
                                    ->disabled()
                                    ->dehydrated(false),
                                
                                Forms\Components\Select::make('status')
                                    ->required()
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Processing',
                                        'completed' => 'Completed',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->native(false),
                                
                                Forms\Components\TextInput::make('total_amount')
                                    ->label('Total Amount')
                                    ->prefix('₱')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Customer Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('customer_name')
                                    ->label('Customer Name')
                                    ->disabled()
                                    ->dehydrated(false),
                                
                                Forms\Components\TextInput::make('customer_email')
                                    ->label('Customer Email')
                                    ->disabled()
                                    ->dehydrated(false),
                                
                                Forms\Components\TextInput::make('customer_phone')
                                    ->label('Customer Phone')
                                    ->disabled()
                                    ->dehydrated(false),
                                
                                Forms\Components\Select::make('user_type')
                                    ->label('Customer Type')
                                    ->options([
                                        'employee' => 'Employee',
                                        'guest' => 'Guest',
                                    ])
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Payment & Service Details')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('payment_method')
                                    ->options([
                                        'cash' => 'Cash',
                                        'gcash' => 'GCash',
                                        'paymaya' => 'PayMaya',
                                        'card' => 'Credit/Debit Card',
                                    ])
                                    ->native(false),
                                
                                Forms\Components\Select::make('payment_status')
                                    ->required()
                                    ->options([
                                        'pending' => 'Pending',
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                    ])
                                    ->native(false),
                                
                                Forms\Components\Select::make('service_type')
                                    ->options([
                                        'dine-in' => 'Dine In',
                                        'take-away' => 'Take Away',
                                    ])
                                    ->native(false),
                            ]),
                    ]),

                Forms\Components\Section::make('Admin Management')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Toggle::make('has_complaint')
                                    ->label('Customer Complaint Filed')
                                    ->helperText('Mark if customer has filed a complaint about this order'),
                                
                                Forms\Components\Select::make('refund_status')
                                    ->label('Refund Status')
                                    ->options([
                                        'none' => 'No Refund',
                                        'requested' => 'Refund Requested',
                                        'approved' => 'Refund Approved',
                                        'processed' => 'Refund Processed',
                                        'denied' => 'Refund Denied',
                                    ])
                                    ->default('none')
                                    ->native(false),
                            ]),
                        
                        Forms\Components\Textarea::make('complaint_details')
                            ->label('Complaint Details')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get): bool => $get('has_complaint'))
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->helperText('Internal notes for admin reference only')
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('resolution_notes')
                            ->label('Resolution Notes')
                            ->helperText('Notes about how complaint/dispute was resolved')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get): bool => $get('has_complaint'))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Order Instructions')
                    ->schema([
                        Forms\Components\Textarea::make('special_instructions')
                            ->label('Customer Instructions')
                            ->rows(3)
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('semibold')
                    ->color('primary'),

                Tables\Columns\IconColumn::make('has_complaint')
                    ->label('Dispute')
                    ->boolean()
                    ->color(fn ($state): string => $state ? 'danger' : 'gray')
                    ->tooltip(fn ($record): string => $record->has_complaint ? 'Customer complaint filed' : 'No complaints'),

                Tables\Columns\TextColumn::make('customer_info')
                    ->label('Customer')
                    ->getStateUsing(function (Order $record): string {
                        if ($record->user_id && $record->user) {
                            return $record->user->name . ' (Employee)';
                        }
                        
                        if ($record->customer_name) {
                            return $record->customer_name . ' (Guest)';
                        }
                        
                        if ($record->guest_details) {
                            $details = is_string($record->guest_details) 
                                ? json_decode($record->guest_details, true) 
                                : $record->guest_details;
                            
                            if (is_array($details) && isset($details['name'])) {
                                return $details['name'] . ' (Guest)';
                            }
                        }
                        
                        return 'Guest Customer';
                    })
                    ->searchable(['customer_name'])
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('my_stall_items')
                    ->label('My Stall Items')
                    ->getStateUsing(function (Order $record): string {
                        $stallId = Auth::user()->admin_stall_id;
                        if (!$stallId) return 'No stall assigned';

                        $stallItems = $record->orderItems()
                            ->whereHas('product', function (Builder $query) use ($stallId) {
                                $query->where('stall_id', $stallId);
                            })
                            ->with('product')
                            ->get();

                        if ($stallItems->isEmpty()) {
                            return 'No items from your stall';
                        }

                        return $stallItems->map(function ($item) {
                            return $item->quantity . '× ' . $item->product->name;
                        })->take(2)->join(', ') . ($stallItems->count() > 2 ? '...' : '');
                    })
                    ->wrap()
                    ->tooltip(function (Order $record): ?string {
                        $stallId = Auth::user()->admin_stall_id;
                        if (!$stallId) return null;

                        $stallItems = $record->orderItems()
                            ->whereHas('product', function (Builder $query) use ($stallId) {
                                $query->where('stall_id', $stallId);
                            })
                            ->with('product')
                            ->get();

                        return $stallItems->map(function ($item) {
                            return $item->quantity . '× ' . $item->product->name . ' (₱' . number_format($item->line_total / 100, 2) . ')';
                        })->join("\n");
                    }),

                Tables\Columns\TextColumn::make('my_stall_revenue')
                    ->label('My Revenue')
                    ->getStateUsing(function (Order $record): float {
                        $stallId = Auth::user()->admin_stall_id;
                        if (!$stallId) return 0;

                        return $record->orderItems()
                            ->whereHas('product', function (Builder $query) use ($stallId) {
                                $query->where('stall_id', $stallId);
                            })
                            ->sum('line_total') / 100;
                    })
                    ->money('PHP')
                    ->weight('semibold')
                    ->alignEnd()
                    ->color('success'),



                Tables\Columns\TextColumn::make('order_total')
                    ->label('Total Order')
                    ->getStateUsing(function (Order $record): float {
                        return $record->orderGroup ? ($record->orderGroup->amount_total / 100) : 0;
                    })
                    ->money('PHP')
                    ->sortable()
                    ->alignEnd()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('refund_status')
                    ->label('Refund')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'requested' => 'warning',
                        'approved' => 'info', 
                        'processed' => 'success',
                        'denied' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'None')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? ucfirst($state) : 'Not Set')
                    ->color(fn (?string $state): string => match ($state) {
                        'cash' => 'success',
                        'gcash' => 'info',
                        'card' => 'warning',
                        'paymaya' => 'primary',
                        default => 'gray',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime('M j, Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('last_modified')
                    ->label('Last Modified')
                    ->getStateUsing(function (Order $record): string {
                        $diffForHumans = $record->updated_at->diffForHumans();
                        return $diffForHumans;
                    })
                    ->tooltip(function (Order $record): string {
                        return 'Updated: ' . $record->updated_at->format('M j, Y H:i:s');
                    })
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing', 
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->multiple(),

                TernaryFilter::make('has_complaint')
                    ->label('Customer Complaints')
                    ->placeholder('All orders')
                    ->trueLabel('With complaints')
                    ->falseLabel('No complaints'),

                SelectFilter::make('refund_status')
                    ->options([
                        'requested' => 'Refund Requested',
                        'approved' => 'Refund Approved',
                        'processed' => 'Refund Processed',
                        'denied' => 'Refund Denied',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                    ])
                    ->multiple(),

                SelectFilter::make('payment_method')
                    ->options([
                        'cash' => 'Cash',
                        'gcash' => 'GCash',
                        'paymaya' => 'PayMaya',
                        'card' => 'Credit/Debit Card',
                    ])
                    ->multiple(),

                Filter::make('high_value')
                    ->label('High Value Orders (₱500+)')
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('orderGroup', function ($q) {
                            $q->where('amount_total', '>=', 50000); // 500 pesos in centavos
                        });
                    })
                    ->toggle(),

                Filter::make('today')
                    ->label('Today\'s Orders')
                    ->query(fn (Builder $query): Builder => $query->whereDate('created_at', today()))
                    ->toggle(),

                Filter::make('this_week')
                    ->label('This Week')
                    ->query(fn (Builder $query): Builder => $query->whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()->endOfWeek()
                    ]))
                    ->toggle(),

                Filter::make('needs_attention')
                    ->label('Needs Admin Attention')
                    ->query(function (Builder $query): Builder {
                        return $query->where(function ($q) {
                            $q->where('has_complaint', true)
                              ->orWhere('refund_status', 'requested')
                              ->orWhere('payment_status', 'failed');
                        });
                    })
                    ->toggle(),
            ], layout: FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('View Details'),
                    
                    Tables\Actions\Action::make('handle_dispute')
                        ->label('Handle Dispute')
                        ->icon('heroicon-o-exclamation-triangle')
                        ->color('warning')
                        ->form([
                            Forms\Components\Textarea::make('resolution_notes')
                                ->required()
                                ->label('Resolution Notes')
                                ->rows(3),
                            Forms\Components\Select::make('resolution_action')
                                ->options([
                                    'refund_approved' => 'Approve Refund',
                                    'refund_denied' => 'Deny Refund',
                                    'replace_order' => 'Replace Order',
                                    'store_credit' => 'Issue Store Credit',
                                ])
                                ->required()
                                ->label('Resolution Action'),
                        ])
                        ->action(function (Order $record, array $data) {
                            $updates = [
                                'resolution_notes' => $data['resolution_notes'],
                                'dispute_resolved_at' => now(),
                            ];

                            if (str_contains($data['resolution_action'], 'refund')) {
                                $updates['refund_status'] = str_contains($data['resolution_action'], 'approved') 
                                    ? 'approved' : 'denied';
                            }

                            $record->update($updates);

                            Notification::make()
                                ->title('Dispute Resolved')
                                ->success()
                                ->send();
                        })
                        ->visible(fn (Order $record): bool => $record->has_complaint),

                    Tables\Actions\Action::make('admin_override')
                        ->label('Admin Override')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('danger')
                        ->form([
                            Forms\Components\Select::make('override_reason')
                                ->options([
                                    'policy_violation' => 'Policy Violation',
                                    'fraud_suspected' => 'Suspected Fraud',
                                    'customer_request' => 'Customer Request',
                                    'system_error' => 'System Error',
                                ])
                                ->required()
                                ->label('Override Reason'),
                            Forms\Components\Select::make('new_status')
                                ->options([
                                    'cancelled' => 'Cancel Order',
                                    'completed' => 'Force Complete',
                                    'pending' => 'Reset to Pending',
                                ])
                                ->required(),
                            Forms\Components\Textarea::make('override_notes')
                                ->required()
                                ->label('Override Notes')
                                ->rows(3),
                        ])
                        ->requiresConfirmation()
                        ->modalDescription('This will override the normal order workflow. Use with caution.')
                        ->action(function (Order $record, array $data) {
                            $record->update([
                                'status' => $data['new_status'],
                                'admin_notes' => ($record->admin_notes ? $record->admin_notes . "\n\n" : '') . 
                                               "ADMIN OVERRIDE ({$data['override_reason']}): " . $data['override_notes'],
                                'admin_override_at' => now(),
                                'admin_override_by' => Auth::id(),
                            ]);

                            Notification::make()
                                ->title('Admin Override Applied')
                                ->warning()
                                ->send();
                        }),

                    Tables\Actions\Action::make('audit_check')
                        ->label('Audit Data')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color('info')
                        ->action(function (Order $record) {
                            $issues = [];
                            
                            // Check total amount consistency
                            $itemsTotal = $record->orderItems->sum('line_total') / 100;
                            $orderTotal = $record->orderGroup ? ($record->orderGroup->amount_total / 100) : 0;
                            
                            if (abs($itemsTotal - $orderTotal) > 0.01) {
                                $issues[] = "Total amount mismatch: Items sum to ₱{$itemsTotal}, order total is ₱{$orderTotal}";
                            }
                            
                            // Check payment status vs order status
                            if ($record->status === 'completed' && $record->payment_status !== 'paid') {
                                $issues[] = 'Order completed but payment not marked as paid';
                            }
                            
                            if (empty($issues)) {
                                Notification::make()
                                    ->title('Audit Passed')
                                    ->body('No data integrity issues found.')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Audit Issues Found')
                                    ->body(implode(' | ', $issues))
                                    ->warning()
                                    ->send();
                            }
                        }),

                    Tables\Actions\EditAction::make()
                        ->label('Edit Order'),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->tooltip('Admin Actions'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mass_refund')
                        ->label('Process Refunds')
                        ->icon('heroicon-o-banknotes')
                        ->color('warning')
                        ->form([
                            Forms\Components\Textarea::make('refund_reason')
                                ->required()
                                ->label('Reason for Mass Refund')
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update([
                                    'refund_status' => 'processed',
                                    'admin_notes' => ($record->admin_notes ? $record->admin_notes . "\n\n" : '') . 
                                                   "MASS REFUND: " . $data['refund_reason'],
                                    'refunded_at' => now(),
                                ]);
                            }
                            
                            Notification::make()
                                ->title('Mass Refund Processed')
                                ->body(count($records) . ' orders processed for refund.')
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalDescription('This will mark all selected orders as refunded.'),

                    Tables\Actions\BulkAction::make('flag_for_review')
                        ->label('Flag for Review')
                        ->icon('heroicon-o-flag')
                        ->color('warning')
                        ->form([
                            Forms\Components\Textarea::make('review_reason')
                                ->required()
                                ->label('Reason for Review')
                                ->rows(2),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update([
                                    'admin_notes' => ($record->admin_notes ? $record->admin_notes . "\n\n" : '') . 
                                                   "FLAGGED FOR REVIEW: " . $data['review_reason'],
                                    'flagged_at' => now(),
                                ]);
                            }
                            
                            Notification::make()
                                ->title('Orders Flagged')
                                ->body(count($records) . ' orders flagged for review.')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('export_for_accounting')
                        ->label('Export for Accounting')
                        ->icon('heroicon-o-document-arrow-down')
                        ->action(function ($records) {
                            // This would typically generate a CSV/Excel export
                            Notification::make()
                                ->title('Export Generated')
                                ->body('Accounting export for ' . count($records) . ' orders.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s') // Auto refresh every minute
            ->emptyStateHeading('No orders found')
            ->emptyStateDescription('Orders from your stall requiring admin oversight will appear here.')
            ->emptyStateIcon('heroicon-o-shield-check')
            ->striped();
    }

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;
        
        if (!$stallId) return null;

        $count = static::getModel()::whereHas('orderItems.product', function ($q) use ($stallId) {
            $q->where('stall_id', $stallId);
        })
        ->where(function ($query) {
            $query->where('has_complaint', true)
                  ->orWhere('refund_status', 'requested')
                  ->orWhere('payment_status', 'failed');
        })
        ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return self::getNavigationBadge() ? 'warning' : null;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}