<div class="flex items-center gap-2">
    @if($getRecord()->is_available && $getRecord()->is_published)
        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
            Available
        </span>
    @elseif($getRecord()->is_available && !$getRecord()->is_published)
        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
            Hidden
        </span>
    @else
        <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">
            Unavailable
        </span>
    @endif
</div>