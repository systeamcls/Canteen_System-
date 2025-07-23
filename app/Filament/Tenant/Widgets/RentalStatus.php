<?php

namespace App\Filament\Tenant\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\RentalPayment;
use Illuminate\Support\Facades\Auth;


class RentalStatus extends BaseWidget
{
    protected static ?string $pollingInterval = null; // Or e.g. '10s'

    protected function getCards(): array
    {
        $tenantId = Auth::id();

        $totalPaid = RentalPayment::where('user_id', $tenantId)
            ->where('status', 'paid')
            ->sum('amount');

        $totalUnpaid = RentalPayment::where('user_id', $tenantId)
            ->where('status', 'unpaid')
            ->sum('amount');

        $overdueCount = RentalPayment::where('user_id', $tenantId)
            ->where('status', 'unpaid')
            ->where('due_date', '<', now())
            ->count();

        return [
            Stat::make('Total Paid', '₱' . number_format($totalPaid, 2))
                ->description('Rent paid successfully')
                ->color('success'),

            Stat::make('Total Unpaid', '₱' . number_format($totalUnpaid, 2))
                ->description($overdueCount . ' Overdue')
                ->color($overdueCount > 0 ? 'danger' : 'success'),
        ];
    }
}
