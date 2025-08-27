{{-- resources/views/components/stall-metrics.blade.php --}}
<div class="flex flex-col gap-1">
    @php
        $today = $getRecord()->orders()->whereDate('created_at', today())->where('status', 'completed')->sum('total_amount') ?? 0;
        $thisMonth = $getRecord()->orders()->whereMonth('created_at', now()->month)->where('status', 'completed')->count();
    @endphp
    
    <div class="text-sm font-medium text-gray-900 dark:text-white">
        ₱{{ number_format($today, 2) }}
    </div>
    <div class="text-xs text-gray-500">
        {{ $thisMonth }} orders this month
    </div>
</div>