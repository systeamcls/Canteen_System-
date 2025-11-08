<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.6;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .header .meta {
            font-size: 10px;
            opacity: 0.9;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-card {
            display: table-cell;
            width: 25%;
            padding: 15px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            text-align: center;
        }

        .stat-card .label {
            font-size: 10px;
            color: #6c757d;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .stat-card .value {
            font-size: 18px;
            font-weight: bold;
            color: #212529;
        }

        .stat-card.success .value {
            color: #22c55e;
        }

        .stat-card.danger .value {
            color: #ef4444;
        }

        .stat-card.info .value {
            color: #3b82f6;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #667eea;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th {
            background: #f3f4f6;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #e5e7eb;
        }

        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        table tr:last-child td {
            border-bottom: none;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            font-size: 9px;
            color: #6c757d;
            text-align: center;
        }

        .expense-item {
            margin-bottom: 8px;
            padding: 8px;
            background: #f8f9fa;
            border-left: 4px solid #667eea;
        }

        .expense-item .name {
            font-weight: bold;
        }

        .expense-item .amount {
            float: right;
            font-weight: bold;
            color: #ef4444;
        }

        .expense-item .percentage {
            font-size: 9px;
            color: #6c757d;
        }
    </style>
</head>

<body>
    {{-- Header --}}
    <div class="header">
        <h1>{{ $title }}</h1>
        <div class="meta">
            Period: {{ $period }} | Generated: {{ $generated_at }} | By: {{ $generated_by }}
        </div>
    </div>

    {{-- Stats Overview --}}
    <div class="section">
        <h2 class="section-title">Financial Overview</h2>
        <div class="stats-grid">
            <div class="stat-card success">
                <div class="label">Revenue</div>
                <div class="value">₱{{ number_format($stats['revenue'], 2) }}</div>
            </div>
            <div class="stat-card danger">
                <div class="label">Expenses</div>
                <div class="value">₱{{ number_format($stats['total_expenses'], 2) }}</div>
            </div>
            <div class="stat-card info">
                <div class="label">Net Profit</div>
                <div class="value">₱{{ number_format($stats['net_profit'], 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="label">Profit Margin</div>
                <div class="value">{{ number_format($stats['profit_margin'], 1) }}%</div>
            </div>
        </div>
    </div>

    {{-- Revenue Breakdown --}}
    <div class="section">
        <h2 class="section-title">Revenue Breakdown</h2>
        <table>
            <tr>
                <td><strong>Sales Revenue</strong></td>
                <td style="text-align: right;">₱{{ number_format($stats['sales'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Rental Income</strong></td>
                <td style="text-align: right;">₱{{ number_format($stats['rental_income'], 2) }}</td>
            </tr>
            <tr style="background: #f3f4f6;">
                <td><strong>Total Revenue</strong></td>
                <td style="text-align: right;"><strong>₱{{ number_format($stats['revenue'], 2) }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Expense Breakdown --}}
    <div class="section">
        <h2 class="section-title">Expense Breakdown</h2>
        @foreach ($expense_breakdown as $expense)
            <div class="expense-item" style="border-left-color: {{ $expense['color'] }};">
                <span class="name">{{ $expense['name'] }}</span>
                <span class="amount">₱{{ number_format($expense['amount'], 2) }}</span>
                <br>
                <span class="percentage">{{ number_format($expense['percentage'], 1) }}% of total expenses</span>
            </div>
        @endforeach

        <table style="margin-top: 15px;">
            <tr>
                <td><strong>Operating Expenses</strong></td>
                <td style="text-align: right;">₱{{ number_format($stats['operating_expenses'], 2) }}</td>
            </tr>
            <tr>
                <td><strong>Staff Salaries</strong></td>
                <td style="text-align: right;">₱{{ number_format($stats['salary_expenses'], 2) }}</td>
            </tr>
            <tr style="background: #f3f4f6;">
                <td><strong>Total Expenses</strong></td>
                <td style="text-align: right;"><strong>₱{{ number_format($stats['total_expenses'], 2) }}</strong></td>
            </tr>
        </table>
    </div>

    {{-- Order Statistics --}}
    <div class="section">
        <h2 class="section-title">Order Statistics</h2>
        <table>
            <tr>
                <th>Metric</th>
                <th style="text-align: right;">Value</th>
            </tr>
            <tr>
                <td>Total Orders</td>
                <td style="text-align: right;">{{ $stats['total_orders'] }}</td>
            </tr>
            <tr>
                <td>Completed Orders</td>
                <td style="text-align: right;">{{ $stats['completed_orders'] }}</td>
            </tr>
            <tr>
                <td>Completion Rate</td>
                <td style="text-align: right;">{{ number_format($stats['completion_rate'], 1) }}%</td>
            </tr>
        </table>
    </div>

    {{-- Rental Payments --}}
    <div class="section">
        <h2 class="section-title">Recent Rental Payments</h2>
        <table>
            <thead>
                <tr>
                    <th>Stall</th>
                    <th>Tenant</th>
                    <th>Amount</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rental_payments as $payment)
                    <tr>
                        <td>{{ $payment['stall'] }}</td>
                        <td>{{ $payment['tenant'] }}</td>
                        <td>₱{{ number_format($payment['amount'], 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($payment['payment_date'])->format('M d, Y') }}</td>
                        <td>
                            <span
                                class="badge badge-{{ $payment['status'] === 'paid' ? 'success' : ($payment['status'] === 'overdue' ? 'danger' : 'warning') }}">
                                {{ ucfirst($payment['status']) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>This is an automated financial report generated by the system.</p>
        <p>For questions or concerns, please contact the finance department.</p>
    </div>
</body>

</html>
