<x-filament-panels::page>
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #e5e7eb;
        }
        
        .stats-card.collected {
            border-left-color: #16a34a;
            background-color: #f0fdf4;
        }
        
        .stats-card.outstanding {
            border-left-color: #dc2626;
            background-color: #fef2f2;
        }
        
        .stats-card.rate {
            border-left-color: #2563eb;
            background-color: #eff6ff;
        }
        
        .stats-card.total {
            border-left-color: #7c3aed;
            background-color: #f3e8ff;
        }
        
        .stats-icon {
            width: 3rem;
            height: 3rem;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .stats-icon.collected {
            background-color: #dcfce7;
            color: #16a34a;
        }
        
        .stats-icon.outstanding {
            background-color: #fee2e2;
            color: #dc2626;
        }
        
        .stats-icon.rate {
            background-color: #dbeafe;
            color: #2563eb;
        }
        
        .stats-icon.total {
            background-color: #e9d5ff;
            color: #7c3aed;
        }
        
        .stats-content h3 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111827;
            margin: 0;
        }
        
        .stats-content p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
            margin-top: 0.25rem;
        }

        .tenant-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .tenant-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }

        .tenant-card:hover {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .tenant-card.paid {
            border-left: 4px solid #16a34a;
            background-color: #f9fafb;
        }

        .tenant-card.unpaid {
            border-left: 4px solid #dc2626;
        }

        .tenant-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .tenant-info h4 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        .tenant-info p {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
            margin-top: 0.25rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: uppercase;
        }

        .status-badge.paid {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-badge.unpaid {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .tenant-amount {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }

        .tenant-actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-success {
            background-color: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background-color: #15803d;
        }

        .btn-warning {
            background-color: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }

        .empty-state svg {
            width: 4rem;
            height: 4rem;
            margin: 0 auto 1rem;
            color: #d1d5db;
        }
    </style>

    <div class="space-y-6">
        <!-- Date Selector -->
        <div class="bg-white rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        <!-- Daily Statistics -->
        <div class="stats-grid">
            <div class="stats-card total">
                <div class="stats-icon total">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="stats-content">
                    <h3>{{ $dailyStats['total_tenants'] ?? 0 }}</h3>
                    <p>Total Tenants</p>
                </div>
            </div>

            <div class="stats-card collected">
                <div class="stats-icon collected">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="stats-content">
                    <h3>₱{{ number_format($dailyStats['total_collected'] ?? 0, 0) }}</h3>
                    <p>Collected Today ({{ $dailyStats['paid_count'] ?? 0 }} paid)</p>
                </div>
            </div>

            <div class="stats-card outstanding">
                <div class="stats-icon outstanding">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="stats-content">
                    <h3>₱{{ number_format($dailyStats['total_outstanding'] ?? 0, 0) }}</h3>
                    <p>Outstanding ({{ $dailyStats['unpaid_count'] ?? 0 }} unpaid)</p>
                </div>
            </div>

            <div class="stats-card rate">
                <div class="stats-icon rate">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="stats-content">
                    <h3>{{ $dailyStats['collection_rate'] ?? 0 }}%</h3>
                    <p>Collection Rate</p>
                </div>
            </div>
        </div>

        <!-- Tenant Payment Cards -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Today's Rent Collection</h3>
                <p class="mt-1 text-sm text-gray-500">Click buttons to mark payments as collected</p>
            </div>

            @if(count($rentPayments) > 0)
                <div class="p-6">
                    <div class="tenant-grid">
                        @foreach($rentPayments as $payment)
                            <div class="tenant-card {{ $payment['status'] === 'paid' ? 'paid' : 'unpaid' }}">
                                <div class="tenant-header">
                                    <div class="tenant-info">
                                        <h4>{{ $payment['tenant']['name'] ?? 'N/A' }}</h4>
                                        <p>{{ $payment['stall']['name'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="status-badge {{ $payment['status'] === 'paid' ? 'paid' : 'unpaid' }}">
                                        {{ $payment['status'] === 'paid' ? 'Paid' : 'Unpaid' }}
                                    </div>
                                </div>

                                <div class="tenant-amount">
                                    ₱{{ number_format($payment['amount'], 0) }}
                                </div>

                                <div class="tenant-actions">
                                    @if($payment['status'] === 'paid')
                                        <button 
                                            wire:click="markAsUnpaid({{ $payment['id'] }})"
                                            class="btn btn-warning"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                            </svg>
                                            Mark Unpaid
                                        </button>
                                        @if($payment['paid_date'])
                                            <span class="text-xs text-gray-500 self-center">
                                                Paid: {{ \Carbon\Carbon::parse($payment['paid_date'])->format('g:i A') }}
                                            </span>
                                        @endif
                                    @else
                                        <button 
                                            wire:click="markAsPaid({{ $payment['id'] }})"
                                            class="btn btn-success"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Mark Paid
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">No Rent Records</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        No rental payment records found for this date. 
                        @if(\App\Models\Stall::where('is_active', true)->whereNotNull('tenant_id')->count() > 0)
                            Click "Generate Daily Rent" to create payment records for all active tenants.
                        @else
                            Make sure you have active stalls with tenants assigned.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>