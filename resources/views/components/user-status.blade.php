{{-- resources/views/components/user-status.blade.php --}}
<div class="flex flex-col gap-1">
    @if($getRecord()->is_active)
        <x-filament::badge color="success" icon="heroicon-m-check-circle">
            Active
        </x-filament::badge>
    @else
        <x-filament::badge color="danger" icon="heroicon-m-x-circle">
            Inactive
        </x-filament::badge>
    @endif
    
    @if($getRecord()->email_verified_at)
        <x-filament::badge color="info" size="sm">
            Verified
        </x-filament::badge>
    @else
        <x-filament::badge color="gray" size="sm">
            Unverified
        </x-filament::badge>
    @endif
</div>