{{-- resources/views/filament/pages/expenses/header.blade.php --}}
<div class="expense-summary-header" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); border: 1px solid #e5e7eb; padding: 24px; margin-bottom: 24px;">
    {{-- Main Summary Stats --}}
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px;">
        <div style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-radius: 8px; padding: 16px;">
            <div style="font-size: 14px; color: #059669; margin-bottom: 4px;">Today</div>
            <div style="font-size: 24px; font-weight: bold; color: #047857;">
                ₱{{ number_format($summary['today'], 2) }}
            </div>
        </div>
        
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border-radius: 8px; padding: 16px;">
            <div style="font-size: 14px; color: #2563eb; margin-bottom: 4px;">This Week</div>
            <div style="font-size: 24px; font-weight: bold; color: #1d4ed8;">
                ₱{{ number_format($summary['this_week'], 2) }}
            </div>
        </div>
        
        <div style="background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%); border-radius: 8px; padding: 16px;">
            <div style="font-size: 14px; color: #9333ea; margin-bottom: 4px;">This Month</div>
            <div style="font-size: 24px; font-weight: bold; color: #7e22ce;">
                ₱{{ number_format($summary['this_month'], 2) }}
            </div>
        </div>
        
        <div style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-radius: 8px; padding: 16px;">
            <div style="font-size: 14px; color: #ea580c; margin-bottom: 4px;">This Year</div>
            <div style="font-size: 24px; font-weight: bold; color: #c2410c;">
                ₱{{ number_format($summary['this_year'], 2) }}
            </div>
        </div>
    </div>

    {{-- Category Breakdown (Full Width) --}}
    <div style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
        <div style="display: flex; align-items: center; margin-bottom: 16px;">
            <svg style="width: 20px; height: 20px; color: #3b82f6; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
            <h3 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">
                Category Breakdown (This Month)
            </h3>
        </div>
        
        @if($summary['categories']->isNotEmpty())
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
                @foreach($summary['categories'] as $category)
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: {{ $category['color'] }};"></div>
                            <div style="flex: 1;">
                                <div style="font-size: 14px; font-weight: 500; color: #111827;">
                                    {{ $category['name'] }}
                                </div>
                                <div style="font-size: 12px; color: #6b7280;">
                                    ₱{{ number_format($category['amount'], 2) }}
                                </div>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 14px; font-weight: 600; color: #374151;">
                                {{ $category['percentage'] }}%
                            </div>
                            <div style="width: 80px; background-color: #e5e7eb; border-radius: 9999px; height: 6px; margin-top: 4px;">
                                <div style="height: 6px; border-radius: 9999px; transition: width 0.3s; width: {{ $category['percentage'] }}%; background-color: {{ $category['color'] }};"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 32px 0; color: #9ca3af;">
                <svg style="width: 48px; height: 48px; margin: 0 auto 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p style="font-size: 14px; margin: 0;">No expenses this month</p>
            </div>
        @endif
    </div>
</div>

<style>
@media (max-width: 1024px) {
    .expense-summary-header > div:nth-child(1) {
        grid-template-columns: repeat(2, 1fr) !important;
    }
    
    .expense-summary-header > div:nth-child(2) > div:nth-child(2) {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .expense-summary-header {
        padding: 16px !important;
    }
    
    .expense-summary-header > div:nth-child(1) {
        grid-template-columns: 1fr !important;
    }
    
    .expense-summary-header > div:nth-child(2) > div:nth-child(2) {
        grid-template-columns: 1fr !important;
    }
}
</style>