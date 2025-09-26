<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class InventoryAnalyticsWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.inventory-analytics';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public function getStockOverview(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return [
                'total_products' => 0,
                'low_stock_count' => 0,
                'out_of_stock_count' => 0,
                'total_stock_value' => 0,
                'average_stock_level' => 0
            ];
        }

        $baseQuery = DB::table('products')
            ->where('stall_id', $adminStallId)
            ->where('is_published', true)
            ->whereNull('deleted_at');

        $totalProducts = (clone $baseQuery)->count();
        
        $lowStockCount = (clone $baseQuery)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', DB::raw('low_stock_alert'))
            ->count();

        $outOfStockCount = (clone $baseQuery)
            ->where('stock_quantity', 0)
            ->count();

        $stockData = (clone $baseQuery)
            ->select(
                DB::raw('SUM(stock_quantity * price) as total_value'),
                DB::raw('AVG(stock_quantity) as avg_stock')
            )
            ->first();

        return [
            'total_products' => $totalProducts,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount,
            'total_stock_value' => $stockData->total_value ?? 0,
            'average_stock_level' => round($stockData->avg_stock ?? 0, 1)
        ];
    }

    public function getLowStockItems(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return [];
        }

        return DB::table('products')
            ->where('stall_id', $adminStallId)
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', DB::raw('low_stock_alert'))
            ->select([
                'id',
                'name',
                'stock_quantity',
                'low_stock_alert',
                'price',
                DB::raw('stock_quantity * price as stock_value')
            ])
            ->orderBy('stock_quantity', 'asc')
            ->get()
            ->toArray();
    }

    public function getOutOfStockItems(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return [];
        }

        return DB::table('products')
            ->where('stall_id', $adminStallId)
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->where('stock_quantity', 0)
            ->select([
                'id',
                'name',
                'price',
                'low_stock_alert',
                'updated_at'
            ])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->toArray();
    }

    public function getStockDistribution(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return ['labels' => [], 'data' => []];
        }

        $stockLevels = DB::table('products')
            ->where('stall_id', $adminStallId)
            ->where('is_published', true)
            ->whereNull('deleted_at')
            ->select([
                'name',
                'stock_quantity'
            ])
            ->orderBy('stock_quantity', 'desc')
            ->limit(10)
            ->get();

        return [
            'labels' => $stockLevels->pluck('name')->toArray(),
            'data' => $stockLevels->pluck('stock_quantity')->toArray()
        ];
    }

    public function getStockStatusBreakdown(): array
    {
        $adminUser = Auth::user();
        $adminStallId = $adminUser->admin_stall_id;

        if (!$adminStallId) {
            return ['labels' => [], 'data' => []];
        }

        $baseQuery = DB::table('products')
            ->where('stall_id', $adminStallId)
            ->where('is_published', true)
            ->whereNull('deleted_at');

        $outOfStock = (clone $baseQuery)->where('stock_quantity', 0)->count();
        $lowStock = (clone $baseQuery)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', DB::raw('low_stock_alert'))
            ->count();
        $normalStock = (clone $baseQuery)
            ->where('stock_quantity', '>', DB::raw('low_stock_alert'))
            ->count();

        return [
            'labels' => ['Out of Stock', 'Low Stock', 'Normal Stock'],
            'data' => [$outOfStock, $lowStock, $normalStock],
            'colors' => [
                'rgba(239, 68, 68, 0.8)',   // Red for out of stock
                'rgba(251, 146, 60, 0.8)',  // Orange for low stock
                'rgba(34, 197, 94, 0.8)'    // Green for normal stock
            ]
        ];
    }

    public function exportStockReport()
    {
        $overview = $this->getStockOverview();
        $lowStockItems = $this->getLowStockItems();
        $outOfStockItems = $this->getOutOfStockItems();
        
        return response()->streamDownload(function() use ($overview, $lowStockItems, $outOfStockItems) {
            echo $this->generateStockReportContent($overview, $lowStockItems, $outOfStockItems);
        }, 'inventory-report-' . Carbon::now()->format('Y-m-d') . '.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    private function generateStockReportContent($overview, $lowStockItems, $outOfStockItems): string
    {
        $html = '
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
            .overview { display: flex; justify-content: space-around; margin-bottom: 30px; }
            .metric-card { text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 8px; }
            .metric-value { font-size: 24px; font-weight: bold; color: #333; }
            .metric-label { font-size: 12px; color: #666; margin-top: 5px; }
            .section { margin-bottom: 30px; }
            .section-title { font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .alert-low { background-color: #fff3cd; }
            .alert-out { background-color: #f8d7da; }
            .text-right { text-align: right; }
        </style>
        
        <div class="header">
            <h1>Inventory Stock Report</h1>
            <p><strong>Generated:</strong> ' . Carbon::now()->format('F d, Y \a\t g:i A') . '</p>
            <p><strong>Admin Stall Inventory Overview</strong></p>
        </div>
        
        <div class="overview">
            <div class="metric-card">
                <div class="metric-value">' . $overview['total_products'] . '</div>
                <div class="metric-label">Total Products</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">₱' . number_format($overview['total_stock_value'], 2) . '</div>
                <div class="metric-label">Total Stock Value</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">' . $overview['low_stock_count'] . '</div>
                <div class="metric-label">Low Stock Items</div>
            </div>
            <div class="metric-card">
                <div class="metric-value">' . $overview['out_of_stock_count'] . '</div>
                <div class="metric-label">Out of Stock</div>
            </div>
        </div>';

        if (!empty($outOfStockItems)) {
            $html .= '
            <div class="section">
                <div class="section-title">🚨 Out of Stock Items (Immediate Attention Required)</div>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Recommended Stock</th>
                            <th>Last Updated</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($outOfStockItems as $item) {
                $html .= '<tr class="alert-out">
                    <td><strong>' . htmlspecialchars($item->name) . '</strong></td>
                    <td>₱' . number_format($item->price / 100, 2) . '</td>
                    <td>' . $item->low_stock_alert . ' units</td>
                    <td>' . Carbon::parse($item->updated_at)->format('M d, Y') . '</td>
                </tr>';
            }
            
            $html .= '</tbody></table></div>';
        }

        if (!empty($lowStockItems)) {
            $html .= '
            <div class="section">
                <div class="section-title">⚠️ Low Stock Items (Restock Soon)</div>
                <table>
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Current Stock</th>
                            <th>Alert Level</th>
                            <th>Unit Price</th>
                            <th>Stock Value</th>
                        </tr>
                    </thead>
                    <tbody>';
            
            foreach ($lowStockItems as $item) {
                $html .= '<tr class="alert-low">
                    <td><strong>' . htmlspecialchars($item->name) . '</strong></td>
                    <td>' . $item->stock_quantity . ' units</td>
                    <td>' . $item->low_stock_alert . ' units</td>
                    <td>₱' . number_format($item->price / 100, 2) . '</td>
                    <td class="text-right">₱' . number_format($item->stock_value / 100, 2) . '</td>
                </tr>';
            }
            
            $html .= '</tbody></table></div>';
        }

        $html .= '
        <div class="section">
            <div class="section-title">📊 Summary</div>
            <p><strong>Inventory Health:</strong> ' . 
            ($overview['out_of_stock_count'] == 0 ? 
                ($overview['low_stock_count'] <= 2 ? 'Good' : 'Needs Attention') : 
                'Critical - Immediate Action Required') . '</p>
            <p><strong>Average Stock Level:</strong> ' . $overview['average_stock_level'] . ' units per product</p>
            <p><strong>Recommendations:</strong></p>
            <ul>';
        
        if ($overview['out_of_stock_count'] > 0) {
            $html .= '<li>🔴 <strong>Urgent:</strong> Restock ' . $overview['out_of_stock_count'] . ' out-of-stock items immediately</li>';
        }
        if ($overview['low_stock_count'] > 0) {
            $html .= '<li>🟡 <strong>Soon:</strong> Plan to restock ' . $overview['low_stock_count'] . ' low-stock items</li>';
        }
        if ($overview['out_of_stock_count'] == 0 && $overview['low_stock_count'] == 0) {
            $html .= '<li>🟢 <strong>Good:</strong> All items are adequately stocked</li>';
        }
        
        $html .= '</ul></div>';
        
        return $html;
    }
}