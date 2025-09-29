{{-- resources/views/filament/widgets/cash-flow-widget.blade.php --}}
<x-filament-widgets::widget>
    <x-filament::section>
        <div style="padding: 16px;">
            <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: #111827;">
                Financial Summary (This Month)
            </h2>

            {{-- Revenue Section --}}
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 14px; font-weight: 600; color: #059669; margin-bottom: 12px;">
                    💰 REVENUE
                </h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; padding-left: 16px;">
                    <div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Your Stall Sales</div>
                        <div style="font-size: 18px; font-weight: bold; color: #111827;">
                            ₱{{ number_format($this->getViewData()['financial']['revenue']['stall_sales'], 2) }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Rental Income</div>
                        <div style="font-size: 18px; font-weight: bold; color: #111827;">
                            ₱{{ number_format($this->getViewData()['financial']['revenue']['rental_income'], 2) }}
                        </div>
                    </div>
                    <div style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); padding: 12px; border-radius: 8px;">
                        <div style="font-size: 12px; color: #059669; margin-bottom: 4px;">Total Revenue</div>
                        <div style="font-size: 20px; font-weight: bold; color: #047857;">
                            ₱{{ number_format($this->getViewData()['financial']['revenue']['total'], 2) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Expenses Section --}}
            <div style="margin-bottom: 24px;">
                <h3 style="font-size: 14px; font-weight: 600; color: #dc2626; margin-bottom: 12px;">
                    📊 EXPENSES
                </h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; padding-left: 16px;">
                    <div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Operational</div>
                        <div style="font-size: 18px; font-weight: bold; color: #111827;">
                            ₱{{ number_format($this->getViewData()['financial']['expenses']['operational'], 2) }}
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Staff Payroll</div>
                        <div style="font-size: 18px; font-weight: bold; color: #111827;">
                            ₱{{ number_format($this->getViewData()['financial']['expenses']['payroll'], 2) }}
                        </div>
                    </div>
                    <div style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); padding: 12px; border-radius: 8px;">
                        <div style="font-size: 12px; color: #dc2626; margin-bottom: 4px;">Total Expenses</div>
                        <div style="font-size: 20px; font-weight: bold; color: #b91c1c;">
                            ₱{{ number_format($this->getViewData()['financial']['expenses']['total'], 2) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Net Profit Section --}}
            <div style="border-top: 2px solid #e5e7eb; padding-top: 16px;">
                @php
                    $financial = $this->getViewData()['financial'];
                    $comparison = $this->getViewData()['comparison'];
                    $isProfitable = $financial['is_profitable'];
                @endphp
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 16px;">
                    <div style="background: linear-gradient(135deg, {{ $isProfitable ? '#ecfdf5' : '#fef2f2' }} 0%, {{ $isProfitable ? '#d1fae5' : '#fee2e2' }} 100%); padding: 20px; border-radius: 12px; border: 2px solid {{ $isProfitable ? '#059669' : '#dc2626' }};">
                        <div style="font-size: 14px; color: {{ $isProfitable ? '#059669' : '#dc2626' }}; margin-bottom: 8px; font-weight: 600;">
                            {{ $isProfitable ? '✅ NET PROFIT' : '⚠️ NET LOSS' }}
                        </div>
                        <div style="font-size: 32px; font-weight: bold; color: {{ $isProfitable ? '#047857' : '#b91c1c' }};">
                            ₱{{ number_format(abs($financial['net_profit']), 2) }}
                        </div>
                        <div style="font-size: 12px; color: {{ $isProfitable ? '#059669' : '#dc2626' }}; margin-top: 4px;">
                            {{ $financial['profit_margin'] }}% profit margin
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; justify-content: center;">
                        <div style="text-align: center; padding: 12px; background: #f9fafb; border-radius: 8px; margin-bottom: 8px;">
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Revenue Growth</div>
                            <div style="font-size: 20px; font-weight: bold; color: {{ $comparison['revenue_growth'] >= 0 ? '#059669' : '#dc2626' }};">
                                {{ $comparison['revenue_growth'] >= 0 ? '+' : '' }}{{ number_format($comparison['revenue_growth'], 1) }}%
                            </div>
                        </div>
                        <div style="text-align: center; padding: 12px; background: #f9fafb; border-radius: 8px;">
                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Profit Growth</div>
                            <div style="font-size: 20px; font-weight: bold; color: {{ $comparison['profit_growth'] >= 0 ? '#059669' : '#dc2626' }};">
                                {{ $comparison['profit_growth'] >= 0 ? '+' : '' }}{{ number_format($comparison['profit_growth'], 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>