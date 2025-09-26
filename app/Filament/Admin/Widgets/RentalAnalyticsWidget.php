<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalAnalyticsWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.rental-analytics';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public function getRentalMetrics(): array
    {
        $baseQuery = DB::table('rental_payments')
            ->where('status', 'paid');

        return [
            'today' => (clone $baseQuery)
                ->whereDate('paid_date', Carbon::today())
                ->sum('amount'),
            
            'month' => (clone $baseQuery)
                ->whereMonth('paid_date', Carbon::now()->month)
                ->whereYear('paid_date', Carbon::now()->year)
                ->sum('amount'),
            
            'year' => (clone $baseQuery)
                ->whereYear('paid_date', Carbon::now()->year)
                ->sum('amount'),
            
            'total_stalls' => DB::table('stalls')->where('is_active', true)->count(),
            
            'occupied_stalls' => DB::table('stalls')
                ->where('is_active', true)
                ->whereNotNull('tenant_id')
                ->count(),
        ];
    }

    public function getRentalTrend(): array
    {
        $last7Days = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $rentals = DB::table('rental_payments')
                ->where('status', 'paid')
                ->whereDate('paid_date', $date)
                ->sum('amount');

            $last7Days->push([
                'date' => $date->format('M j'),
                'rentals' => $rentals ?? 0
            ]);
        }

        return [
            'labels' => $last7Days->pluck('date')->toArray(),
            'data' => $last7Days->pluck('rentals')->toArray()
        ];
    }

    public function getStallPaymentStatus(): array
    {
        $currentMonth = Carbon::now()->format('Y-m');
        
        return DB::table('stalls')
            ->leftJoin('users as tenants', 'stalls.tenant_id', '=', 'tenants.id')
            ->leftJoin('rental_payments', function($join) use ($currentMonth) {
                $join->on('stalls.id', '=', 'rental_payments.stall_id')
                     ->where('rental_payments.period_start', 'like', $currentMonth . '%');
            })
            ->where('stalls.is_active', true)
            ->select([
                'stalls.id',
                'stalls.name as stall_name',
                'stalls.location',
                'stalls.rental_fee',
                'tenants.name as tenant_name',
                'rental_payments.status as payment_status',
                'rental_payments.due_date',
                'rental_payments.paid_date',
                DB::raw('CASE 
                    WHEN rental_payments.status = "paid" THEN "Paid"
                    WHEN rental_payments.due_date < CURDATE() AND rental_payments.status != "paid" THEN "Overdue"
                    WHEN rental_payments.status IS NULL THEN "Not Generated"
                    ELSE "Pending"
                END as display_status')
            ])
            ->orderBy('stalls.name')
            ->get()
            ->toArray();
    }

    public function getPaymentCompliance(): array
    {
        $currentMonth = Carbon::now()->format('Y-m');
        
        $totalActiveStalls = DB::table('stalls')
            ->where('is_active', true)
            ->whereNotNull('tenant_id')
            ->count();

        $paidStalls = DB::table('rental_payments')
            ->join('stalls', 'rental_payments.stall_id', '=', 'stalls.id')
            ->where('stalls.is_active', true)
            ->where('rental_payments.status', 'paid')
            ->where('rental_payments.period_start', 'like', $currentMonth . '%')
            ->count();

        $overdueStalls = DB::table('rental_payments')
            ->join('stalls', 'rental_payments.stall_id', '=', 'stalls.id')
            ->where('stalls.is_active', true)
            ->where('rental_payments.status', '!=', 'paid')
            ->where('rental_payments.due_date', '<', Carbon::now())
            ->where('rental_payments.period_start', 'like', $currentMonth . '%')
            ->count();

        $pendingStalls = $totalActiveStalls - $paidStalls - $overdueStalls;

        $complianceRate = $totalActiveStalls > 0 ? round(($paidStalls / $totalActiveStalls) * 100, 1) : 0;

        return [
            'total_stalls' => $totalActiveStalls,
            'paid_stalls' => $paidStalls,
            'overdue_stalls' => $overdueStalls,
            'pending_stalls' => $pendingStalls,
            'compliance_rate' => $complianceRate,
            'labels' => ['Paid', 'Overdue', 'Pending'],
            'data' => [$paidStalls, $overdueStalls, $pendingStalls],
            'colors' => ['rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)', 'rgba(251, 146, 60, 0.8)']
        ];
    }

    public function exportPDF()
    {
        // This method will be called via Livewire action
        $metrics = $this->getRentalMetrics();
        $stallStatus = $this->getStallPaymentStatus();
        $compliance = $this->getPaymentCompliance();
        
        // Generate PDF using TCPDF or similar
        // Return download response
        return response()->streamDownload(function() use ($metrics, $stallStatus, $compliance) {
            echo $this->generatePDFContent($metrics, $stallStatus, $compliance);
        }, 'rental-analytics-' . Carbon::now()->format('Y-m-d') . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function generatePDFContent($metrics, $stallStatus, $compliance): string
    {
        // Simple HTML to PDF conversion - you can enhance this with proper PDF library
        $html = '
        <style>
            body { font-family: Arial, sans-serif; }
            .header { text-align: center; margin-bottom: 30px; }
            .metrics { display: flex; justify-content: space-around; margin-bottom: 30px; }
            .metric-card { text-align: center; padding: 20px; border: 1px solid #ddd; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
        </style>
        <div class="header">
            <h1>Rental Analytics Report</h1>
            <p>Generated on: ' . Carbon::now()->format('F d, Y') . '</p>
        </div>
        
        <div class="metrics">
            <div class="metric-card">
                <h3>Today</h3>
                <p>₱' . number_format($metrics['today'], 2) . '</p>
            </div>
            <div class="metric-card">
                <h3>This Month</h3>
                <p>₱' . number_format($metrics['month'], 2) . '</p>
            </div>
            <div class="metric-card">
                <h3>This Year</h3>
                <p>₱' . number_format($metrics['year'], 2) . '</p>
            </div>
        </div>
        
        <h2>Payment Compliance: ' . $compliance['compliance_rate'] . '%</h2>
        
        <table>
            <thead>
                <tr>
                    <th>Stall Name</th>
                    <th>Location</th>
                    <th>Tenant</th>
                    <th>Rental Fee</th>
                    <th>Status</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($stallStatus as $stall) {
            $html .= '<tr>
                <td>' . htmlspecialchars($stall->stall_name) . '</td>
                <td>' . htmlspecialchars($stall->location) . '</td>
                <td>' . htmlspecialchars($stall->tenant_name ?? 'Vacant') . '</td>
                <td>₱' . number_format($stall->rental_fee, 2) . '</td>
                <td>' . htmlspecialchars($stall->display_status) . '</td>
                <td>' . ($stall->due_date ? Carbon::parse($stall->due_date)->format('M d, Y') : '-') . '</td>
            </tr>';
        }
        
        $html .= '</tbody></table>';
        
        return $html;
    }
}