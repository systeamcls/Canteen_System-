<?php

namespace App\Filament\Admin\Pages;

use App\Models\RentalPayment;
use App\Models\Stall;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class DailyRentCollection extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static string $view = 'filament.admin.pages.daily-rent-collection';
    protected static ?string $navigationGroup = 'Admin Management';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Daily Rent Collection';
    protected static ?string $navigationLabel = 'Rent Collection';

    public $selectedDate;
    public $rentPayments = [];
    public $dailyStats = [];

    public function mount(): void
    {
        $this->selectedDate = today()->format('Y-m-d');
        $this->loadRentData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)
                    ->schema([
                        DatePicker::make('selectedDate')
                            ->label('Select Date')
                            ->default(today())
                            ->live()
                            ->afterStateUpdated(fn () => $this->loadRentData()),
                    ]),
            ]);
    }

    public function loadRentData(): void
    {
        // Get all rental payments for selected date
        $this->rentPayments = RentalPayment::with(['stall', 'tenant'])
            ->whereDate('period_start', $this->selectedDate)
            ->whereDate('period_end', $this->selectedDate)
            ->orderBy('stall_id')
            ->get()
            ->toArray();

        // Calculate daily statistics
        $this->calculateDailyStats();
    }

    public function calculateDailyStats(): void
    {
        $payments = collect($this->rentPayments);
        
        $this->dailyStats = [
            'total_tenants' => $payments->count(),
            'paid_count' => $payments->where('status', 'paid')->count(),
            'unpaid_count' => $payments->whereIn('status', ['pending', 'overdue'])->count(),
            'total_expected' => $payments->sum('amount'),
            'total_collected' => $payments->where('status', 'paid')->sum('amount'),
            'total_outstanding' => $payments->whereIn('status', ['pending', 'overdue'])->sum('amount'),
            'collection_rate' => $payments->count() > 0 ? 
                round(($payments->where('status', 'paid')->count() / $payments->count()) * 100, 1) : 0,
        ];
    }

    public function markAsPaid($paymentId): void
    {
        $payment = RentalPayment::find($paymentId);
        
        if (!$payment) {
            Notification::make()
                ->title('Error')
                ->body('Payment record not found')
                ->danger()
                ->send();
            return;
        }

        $payment->update([
            'status' => 'paid',
            'paid_date' => now(),
            'notes' => ($payment->notes ? $payment->notes . "\n" : '') . 
                      'Marked as paid on ' . now()->format('M j, Y g:i A'),
        ]);

        $this->loadRentData();

        Notification::make()
            ->title('Payment Recorded')
            ->body("{$payment->tenant->name} - {$payment->stall->name} marked as paid")
            ->success()
            ->send();
    }

    public function markAsUnpaid($paymentId): void
    {
        $payment = RentalPayment::find($paymentId);
        
        if (!$payment) {
            Notification::make()
                ->title('Error')
                ->body('Payment record not found')
                ->danger()
                ->send();
            return;
        }

        $payment->update([
            'status' => 'pending',
            'paid_date' => null,
            'notes' => ($payment->notes ? $payment->notes . "\n" : '') . 
                      'Marked as unpaid on ' . now()->format('M j, Y g:i A'),
        ]);

        $this->loadRentData();

        Notification::make()
            ->title('Payment Reverted')
            ->body("{$payment->tenant->name} - {$payment->stall->name} marked as unpaid")
            ->warning()
            ->send();
    }

    public function markAllPaid(): void
    {
        $unpaidPayments = RentalPayment::whereDate('period_start', $this->selectedDate)
            ->whereDate('period_end', $this->selectedDate)
            ->whereIn('status', ['pending', 'overdue'])
            ->get();

        if ($unpaidPayments->isEmpty()) {
            Notification::make()
                ->title('No Unpaid Payments')
                ->body('All payments for this date are already marked as paid')
                ->info()
                ->send();
            return;
        }

        foreach ($unpaidPayments as $payment) {
            $payment->update([
                'status' => 'paid',
                'paid_date' => now(),
                'notes' => ($payment->notes ? $payment->notes . "\n" : '') . 
                          'Bulk marked as paid on ' . now()->format('M j, Y g:i A'),
            ]);
        }

        $this->loadRentData();

        Notification::make()
            ->title('Bulk Payment Recorded')
            ->body("Marked {$unpaidPayments->count()} payments as paid")
            ->success()
            ->send();
    }

    public function generateDailyRent(): void
    {
        // Get all active stalls with tenants that don't have payment records for this date
        $activeStalls = Stall::where('is_active', true)
            ->whereNotNull('tenant_id')
            ->whereDoesntHave('rentalPayments', function ($query) {
                $query->whereDate('period_start', $this->selectedDate)
                      ->whereDate('period_end', $this->selectedDate);
            })
            ->with('tenant')
            ->get();

        if ($activeStalls->isEmpty()) {
            Notification::make()
                ->title('No New Records Needed')
                ->body('All active tenants already have payment records for this date')
                ->info()
                ->send();
            return;
        }

        $created = 0;
        foreach ($activeStalls as $stall) {
            RentalPayment::create([
                'stall_id' => $stall->id,
                'tenant_id' => $stall->tenant_id,
                'amount' => $stall->rental_fee,
                'period_start' => $this->selectedDate,
                'period_end' => $this->selectedDate,
                'due_date' => $this->selectedDate,
                'status' => 'pending',
                'notes' => 'Generated for ' . Carbon::parse($this->selectedDate)->format('M j, Y'),
            ]);
            $created++;
        }

        $this->loadRentData();

        Notification::make()
            ->title('Daily Rent Generated')
            ->body("Created {$created} new payment records")
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('mark_all_paid')
                ->label('Mark All Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action('markAllPaid')
                ->requiresConfirmation()
                ->modalDescription('This will mark all unpaid rent for this date as paid.'),

            Action::make('generate_rent')
                ->label('Generate Daily Rent')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->action('generateDailyRent')
                ->visible(fn () => collect($this->rentPayments)->count() < 
                    Stall::where('is_active', true)->whereNotNull('tenant_id')->count()),

            Action::make('view_history')
                ->label('View All Records')
                ->icon('heroicon-o-table-cells')
                ->color('gray')
                ->url(route('filament.admin.resources.rental-payments.index')),
        ];
    }

    public function getTitle(): string
    {
        $date = Carbon::parse($this->selectedDate);
        return 'Daily Rent Collection - ' . $date->format('M j, Y');
    }
}