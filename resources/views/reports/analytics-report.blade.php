<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report_type }} - {{ $date ?? $period ?? $month }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.4;
            color: #333;
            font-size: 12px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .section {
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        
        .section h2 {
            color: #667eea;
            font-size: 16px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .section h2::before {
            content: "📊";
            margin-right: 8px;
        }
        
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .kpi-card {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e1e5e9;
            text-align: center;
        }
        
        .kpi-value {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .kpi-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .kpi-change {
            font-size: 10px;
            margin-top: 3px;
        }
        
        .positive { color: #28a745; }
        .negative { color: #dc3545; }
        .neutral { color: #6c757d; }
        
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e1e5e9;
        }
        
        .table th, .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }
        
        .table th {
            background: #f1f3f4;
            font-weight: bold;
            font-size: 11px;
            color: #495057;
        }
        
        .table td {
            font-size: 11px;
        }
        
        .table tr:last-child td {
            border-bottom: none;
        }
        
        .summary-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        
        .summary-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e1e5e9;
        }
        
        .summary-item h3 {
            font-size: 13px;
            color: #495057;
            margin-bottom: 10px;
        }
        
        .chart-placeholder {
            background: white;
            border: 1px solid #e1e5e9;
            border-radius: 6px;
            padding: 40px 20px;
            text-align: center;
            color: #6c757d;
            margin-top: 15px;
        }
        
        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 10px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e1e5e9;
        }
        
        .highlight {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        
        .highlight strong {
            color: #856404;
        }
        
        @media print {
            .container {
                padding: 10px;
            }
            .section {
                break-inside: avoid;
                margin-bottom: 20px;
            }
            .chart-placeholder {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>{{ $report_type }}</h1>
            <p>{{ $date ?? $period ?? $month }} • Generated on {{ $generated_at }}</p>
        </div>

        <!-- Key Performance Indicators -->
        @if(isset($kpis))
        <div class="section">
            <h2>Key Performance Indicators</h2>
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-value">₱{{ number_format($kpis['sales']['amount'], 2) }}</div>
                    <div class="kpi-label">Today's Sales</div>
                    <div class="kpi-change {{ $kpis['sales']['change'] >= 0 ? 'positive' : 'negative' }}">
                        {{ $kpis['sales']['change'] >= 0 ? '+' : '' }}{{ number_format($kpis['sales']['change'], 1) }}% vs yesterday
                    </div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-value">₱{{ number_format($kpis['net_revenue'], 2) }}</div>
                    <div class="kpi-label">Net Revenue</div>
                    <div class="kpi-change {{ $kpis['net_revenue'] >= 0 ? 'positive' : 'negative' }}">
                        {{ $kpis['net_revenue'] >= 0 ? 'Profit' : 'Loss' }}
                    </div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['sales']['orders_count'] }}</div>
                    <div class="kpi-label">Orders Completed</div>
                    <div class="kpi-change neutral">Today</div>
                </div>
                
                <div class="kpi-card">
                    <div class="kpi-value">{{ $kpis['active_stalls'] }}</div>
                    <div class="kpi-label">Active Stalls</div>
                    <div class="kpi-change neutral">Operating</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Sales Performance -->
        @if(isset($top_products) && count($top_products) > 0)
        <div class="section">
            <h2>Top Performing Products</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($top_products as $index => $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->total_sold }}</td>
                        <td>₱{{ number_format($product->revenue, 2) }}</td>
                        <td>
                            @if($index == 0)
                                <span style="color: #28a745;">🏆 Top Seller</span>
                            @elseif($index < 3)
                                <span style="color: #ffc107;">⭐ Strong</span>
                            @else
                                <span style="color: #6c757d;">📈 Good</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Financial Summary -->
        @if(isset($cash_flow))
        <div class="section">
            <h2>Financial Summary</h2>
            <div class="summary-row">
                <div class="summary-item">
                    <h3>💰 Income Sources</h3>
                    <p><strong>Sales Revenue:</strong> ₱{{ number_format($cash_flow['income']['sales'], 2) }}</p>
                    <p><strong>Rental Income:</strong> ₱{{ number_format($cash_flow['income']['rentals'], 2) }}</p>
                    <p style="border-top: 1px solid #dee2e6; margin-top: 8px; padding-top: 8px;">
                        <strong>Total Income:</strong> ₱{{ number_format($cash_flow['income']['total'], 2) }}
                    </p>
                </div>
                
                <div class="summary-item">
                    <h3>💸 Expenses</h3>
                    <p><strong>Operations:</strong> ₱{{ number_format($cash_flow['expenses']['operational'], 2) }}</p>
                    <p><strong>Payroll:</strong> ₱{{ number_format($cash_flow['expenses']['payroll'], 2) }}</p>
                    <p style="border-top: 1px solid #dee2e6; margin-top: 8px; padding-top: 8px;">
                        <strong>Total Expenses:</strong> ₱{{ number_format($cash_flow['expenses']['total'], 2) }}
                    </p>
                </div>
            </div>
            
            <div class="highlight">
                <strong>Net Cash Flow:</strong> ₱{{ number_format($cash_flow['net_cash_flow'], 2) }}
                @if($cash_flow['net_cash_flow'] >= 0)
                    ✅ Positive cash flow indicates healthy business performance
                @else
                    ⚠️ Negative cash flow - consider reviewing expenses or increasing revenue
                @endif
            </div>
        </div>
        @endif

        <!-- Rental Insights -->
        @if(isset($rental_insights))
        <div class="section">
            <h2>Rental Management</h2>
            <div class="summary-row">
                <div class="summary-item">
                    <h3>📈 Collection Performance</h3>
                    <p><strong>Today Collected:</strong> ₱{{ number_format($rental_insights['today_collected'], 2) }}</p>
                    <p><strong>Monthly Total:</strong> ₱{{ number_format($rental_insights['monthly_collected'], 2) }}</p>
                </div>
                
                <div class="summary-item">
                    <h3>⚡ Compliance Status</h3>
                    <p><strong>Payment Rate:</strong> {{ $rental_insights['compliance_rate'] }}%</p>
                    <p><strong>Overdue:</strong> {{ $rental_insights['overdue_count'] }} payments</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Payment Methods -->
        @if(isset($payment_methods) && count($payment_methods['labels']) > 0)
        <div class="section">
            <h2>Payment Method Distribution</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = array_sum($payment_methods['data']); @endphp
                    @foreach($payment_methods['labels'] as $index => $method)
                    <tr>
                        <td>{{ $method }}</td>
                        <td>₱{{ number_format($payment_methods['data'][$index], 2) }}</td>
                        <td>{{ $total > 0 ? number_format(($payment_methods['data'][$index] / $total) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Chart Placeholders -->
        <div class="section">
            <h2>Visual Analytics</h2>
            <div class="chart-placeholder">
                📈 Sales Trend Chart<br>
                <small>Interactive charts available in the online dashboard</small>
            </div>
            <div class="chart-placeholder">
                🍕 Product Performance Chart<br>
                <small>View detailed analytics at your dashboard</small>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Canteen Management System</strong></p>
            <p>This report was automatically generated on {{ $generated_at }}</p>
            <p>Data includes completed orders, confirmed payments, and verified transactions only</p>
        </div>
    </div>
</body>
</html>