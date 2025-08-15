<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Review;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class AdminRecentReviewsWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Customer Reviews';
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        $user = Auth::user();
        $stallId = $user->admin_stall_id;

        return $table
            ->query(function () use ($stallId) {
                if (!$stallId) {
                    return Review::query()->whereRaw('1 = 0'); // Return empty query
                }

                return Review::query()
                    ->whereHas('product', function ($query) use ($stallId) {
                        $query->where('stall_id', $stallId);
                    })
                    ->with(['user', 'product'])
                    ->latest()
                    ->limit(5);
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Customer')
                    ->default('Anonymous')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->color('primary')
                    ->limit(20),
                Tables\Columns\ViewColumn::make('rating')
                    ->label('Rating')
                    ->view('filament.components.star-rating'),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Review')
                    ->limit(50)
                    ->tooltip(function ($record) {
                        return $record->comment;
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->since()
                    ->sortable(),
            ])
            ->emptyStateHeading('No reviews yet')
            ->emptyStateDescription('Customer reviews will appear here.')
            ->emptyStateIcon('heroicon-o-star')
            ->paginated(false);
    }
}