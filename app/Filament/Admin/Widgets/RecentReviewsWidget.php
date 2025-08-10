<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Review;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class RecentReviewsWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'Recent Reviews';
    protected static ?string $pollingInterval = '10s'; // Real-time updates every 10 seconds

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $adminStall = Auth::user()->stall;
                
                if (!$adminStall) {
                    return Review::query()->whereRaw('1 = 0'); // Return empty query
                }

                // Show only reviews for products from admin's stall
                return Review::query()
                    ->whereHas('product', function (Builder $query) use ($adminStall) {
                        $query->where('stall_id', $adminStall->id);
                    })
                    ->with(['user', 'product', 'order'])
                    ->where('is_visible', true)
                    ->latest()
                    ->limit(8);
            })
            ->columns([
                Tables\Columns\ImageColumn::make('product.image')
                    ->label('Product')
                    ->circular()
                    ->size(40)
                    ->defaultImageUrl('/images/placeholder-product.png'),
                
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->weight('semibold')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->default('Guest Customer')
                    ->icon('heroicon-m-user'),
                
                Tables\Columns\TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('⭐', $state) . str_repeat('☆', 5 - $state))
                    ->alignCenter(),
                
                Tables\Columns\TextColumn::make('comment')
                    ->label('Review')
                    ->wrap()
                    ->limit(100)
                    ->tooltip(fn (Review $record): string => $record->comment ?? '')
                    ->placeholder('No comment provided'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->since()
                    ->tooltip(fn (Review $record): string => $record->created_at->format('F j, Y \a\t g:i A')),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->actions([
                Tables\Actions\Action::make('toggle_visibility')
                    ->label(fn (Review $record): string => $record->is_visible ? 'Hide' : 'Show')
                    ->icon(fn (Review $record): string => $record->is_visible ? 'heroicon-m-eye-slash' : 'heroicon-m-eye')
                    ->color(fn (Review $record): string => $record->is_visible ? 'danger' : 'success')
                    ->action(function (Review $record) {
                        $record->update(['is_visible' => !$record->is_visible]);
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Are you sure you want to change the visibility of this review?'),
            ])
            ->emptyStateHeading('No Recent Reviews')
            ->emptyStateDescription('Customer reviews for your products will appear here.')
            ->emptyStateIcon('heroicon-o-star');
    }
}