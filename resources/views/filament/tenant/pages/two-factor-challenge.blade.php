<x-filament-panels::page>
    <div class="max-w-md mx-auto">
        <x-filament::section>
            <x-slot name="heading">
                Tenant Dashboard - Security Check
            </x-slot>
            
            <x-slot name="description">
                Please enter your 2FA code to access the tenant dashboard.
            </x-slot>
            
            <form wire:submit="verify">
                {{ $this->form }}
                
                <div class="mt-6">
                    {{ $this->getFormActions() }}
                </div>
            </form>
        </x-filament::section>
    </div>
</x-filament-panels::page>