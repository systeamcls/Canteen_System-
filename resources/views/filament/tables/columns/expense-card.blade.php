{{-- resources/views/filament/tables/columns/expense-card.blade.php --}}
@php
    $expense = $getRecord();
    $categoryColor = $expense->category->color ?? '#6B7280';
@endphp

<div class="expense-card" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid #e5e7eb; padding: 16px; transition: all 0.2s; min-height: 140px; cursor: pointer;">
    {{-- Header: Date and Category --}}
    <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 12px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <div style="width: 12px; height: 12px; border-radius: 50%; background-color: {{ $categoryColor }};"></div>
            <span style="display: inline-flex; align-items: center; padding: 4px 8px; border-radius: 9999px; font-size: 12px; font-weight: 500; background-color: {{ $categoryColor }}20; color: {{ $categoryColor }};">
                {{ $expense->category->name }}
            </span>
        </div>
        <span style="font-size: 14px; color: #6b7280;">
            {{ $expense->expense_date->format('M j') }}
        </span>
    </div>

    {{-- Description --}}
    <div style="margin-bottom: 12px;">
        <h3 style="font-size: 14px; font-weight: 500; color: #111827; line-height: 1.4; margin: 0;">
            {{ $expense->description }}
        </h3>
        @if($expense->vendor)
            <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0; display: flex; align-items: center;">
                <svg style="width: 12px; height: 12px; display: inline-block; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                {{ $expense->vendor }}
            </p>
        @endif
    </div>

    {{-- Amount --}}
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <span style="font-size: 18px; font-weight: bold; color: #111827;">
            ₱{{ number_format($expense->amount, 2) }}
        </span>
        
        @if($expense->receipt_number)
            <span style="font-size: 12px; color: #9ca3af;">
                #{{ $expense->receipt_number }}
            </span>
        @endif
    </div>

    {{-- Optional Notes --}}
    @if($expense->notes)
        <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6;">
            <p style="font-size: 12px; color: #6b7280; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $expense->notes }}
            </p>
        </div>
    @endif

    {{-- Footer --}}
    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f3f4f6; display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #9ca3af;">
        <span style="display: flex; align-items: center;">
            <svg style="width: 12px; height: 12px; display: inline-block; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            {{ $expense->recordedBy->name }}
        </span>
        <span title="{{ $expense->created_at->format('M j, Y H:i') }}">
            {{ $expense->created_at->diffForHumans() }}
        </span>
    </div>
</div>

<style>
.expense-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
}

@media (max-width: 768px) {
    .expense-card {
        min-height: 120px !important;
    }
}
</style>:shadow-md transition-shadow duration-200">
    {{-- Header: Date and Category --}}
    <div class="flex items-start justify-between mb-3">
        <div class="flex items-center space-x-2">
            <div class="w-3 h-3 rounded-full" style="background-color: {{ $categoryColor }}"></div>
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium"
                  style="background-color: {{ $categoryColor }}20; color: {{ $categoryColor }};">
                {{ $expense->category->name }}
            </span>
        </div>
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $expense->expense_date->format('M j') }}
        </span>
    </div>

    {{-- Description --}}
    <div class="mb-3">
        <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 leading-tight">
            {{ $expense->description }}
        </h3>
        @if($expense->vendor)
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                <x-heroicon-o-building-storefront class="w-3 h-3 inline mr-1" />
                {{ $expense->vendor }}
            </p>
        @endif
    </div>

    {{-- Amount --}}
    <div class="flex items-center justify-between">
        <span class="text-lg font-bold text-gray-900 dark:text-gray-100">
            ₱{{ number_format($expense->amount, 2) }}
        </span>
        
        @if($expense->receipt_number)
            <span class="text-xs text-gray-400 dark:text-gray-500">
                #{{ $expense->receipt_number }}
            </span>
        @endif
    </div>

    {{-- Optional Notes (if present) --}}
    @if($expense->notes)
        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">
                {{ $expense->notes }}
            </p>
        </div>
    @endif

    {{-- Footer: Recorded by and timestamp --}}
    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
        <span>
            <x-heroicon-o-user class="w-3 h-3 inline mr-1" />
            {{ $expense->recordedBy->name }}
        </span>
        <span title="{{ $expense->created_at->format('M j, Y H:i') }}">
            {{ $expense->created_at->diffForHumans() }}
        </span>
    </div>
</div>

<style>
.expense-card {
    min-height: 140px;
}

.expense-card:hover {
    transform: translateY(-1px);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

@media (max-width: 768px) {
    .expense-card {
        min-height: 120px;
    }
}
</style>