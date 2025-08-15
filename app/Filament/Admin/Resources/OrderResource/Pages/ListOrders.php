<?php

namespace App\Filament\Admin\Resources\OrderResource\Pages;

use App\Filament\Admin\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    return $this->export('csv');
                }),
            Actions\Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(function () {
                    return $this->export('pdf');
                }),
        ];
    }

    public function export($format)
    {
        $user = auth()->user();
        $stallId = $user->admin_stall_id;
        
        if (!$stallId) {
            return;
        }

        $orders = Order::with(['user', 'items.product'])
            ->whereHas('items.product', function ($query) use ($stallId) {
                $query->where('stall_id', $stallId);
            })
            ->get();

        if ($format === 'csv') {
            return $this->exportToCsv($orders);
        } elseif ($format === 'pdf') {
            return $this->exportToPdf($orders);
        }
    }

    private function exportToCsv($orders)
    {
        $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order Number', 'Customer', 'Total Amount', 'Status', 'Payment Status', 'Created At']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->user->name ?? 'Guest',
                    $order->total_amount,
                    $order->status,
                    $order->payment_status,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportToPdf($orders)
    {
        // Basic PDF export - in a real implementation, you'd use a PDF library like DomPDF
        $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.html';
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $html = '<html><head><title>Orders Export</title></head><body>';
        $html .= '<h1>Orders Report</h1>';
        $html .= '<table border="1" style="width:100%; border-collapse: collapse;">';
        $html .= '<tr><th>Order Number</th><th>Customer</th><th>Total Amount</th><th>Status</th><th>Payment Status</th><th>Created At</th></tr>';

        foreach ($orders as $order) {
            $html .= '<tr>';
            $html .= '<td>' . $order->order_number . '</td>';
            $html .= '<td>' . ($order->user->name ?? 'Guest') . '</td>';
            $html .= '<td>PHP ' . number_format($order->total_amount, 2) . '</td>';
            $html .= '<td>' . ucfirst($order->status) . '</td>';
            $html .= '<td>' . ucfirst($order->payment_status) . '</td>';
            $html .= '<td>' . $order->created_at->format('Y-m-d H:i:s') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table></body></html>';

        return response($html, 200, $headers);
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'pending' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),
            'processing' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'processing')),
            'completed' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed')),
            'cancelled' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
        ];
    }
}