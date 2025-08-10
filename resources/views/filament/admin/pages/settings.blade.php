<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="save">
            {{ $this->form }}
            
            <div class="flex justify-end mt-6">
                <x-filament::button type="submit" size="lg">
                    Save Settings
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>