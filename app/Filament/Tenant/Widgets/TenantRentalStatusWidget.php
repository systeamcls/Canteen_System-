<?php

// app/Filament/Tenant/Widgets/TenantRentalStatus.php
namespace App\Filament\Tenant\Widgets;

use App\Models\RentalPayment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class TenantRentalStatusWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = [
        'md' => 2,
        'lg' => 1,
        'xl' => 1,
    ];

    protected function getStats(): array
    {
        $user = Auth::user();
        $stall = $user->assignedStall;

        if (!$stall) {
            return [
                Stat::make('Rental Status', 'No Stall Assigned')
                    ->description('Contact admin for stall assignment')
                    ->descriptionIcon('heroicon-m-exclamation-triangle')
                    ->color('warning'),
            ];
        }

        $nextPayment = RentalPayment::where('tenant_id', $user->id)
            ->where('stall_id', $stall->id)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();

        $overduePayments = RentalPayment::where('tenant_id', $user->id)
            ->where('stall_id', $stall->id)
            ->where('status', 'pending')
            ->where('due_date', '<', now())
            ->count();

        $totalPaid = RentalPayment::where('tenant_id', $user->id)
            ->where('stall_id', $stall->id)
            ->where('status', 'paid')
            ->sum('amount');

        if ($nextPayment) {
            $isOverdue = $nextPayment->due_date->isPast();
            $daysUntilDue = $nextPayment->due_date->diffInDays(now(), false);
            
            return [
                Stat::make(
                    $isOverdue ? 'Overdue Payment' : 'Next Payment Due',
                    'PHP ' . number_format($nextPayment->amount, 2)
                )
                ->description(
                    $isOverdue 
                        ? 'Due ' . abs($daysUntilDue) . ' days ago'
                        : 'Due in ' . $daysUntilDue . ' days'
                )
                ->descriptionIcon($isOverdue ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-calendar')
                ->color($isOverdue ? 'danger' : ($daysUntilDue <= 7 ? 'warning' : 'success')),

                Stat::make('Rental History', 'PHP ' . number_format($totalPaid, 2))
                    ->description('Total paid to date')
                    ->descriptionIcon('heroicon-m-check-circle')
                    ->color('success'),
            ];
        }

        return [
            Stat::make('Rental Status', 'All Up to Date')
                ->description('No pending payments')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}