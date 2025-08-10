<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="flex justify-end space-x-3">
            <x-filament::button 
                wire:click="markAllAsRead" 
                color="success"
                size="sm"
            >
                <x-heroicon-m-check class="w-4 h-4 mr-1" />
                Mark All Read
            </x-filament::button>
            
            <x-filament::button 
                wire:click="deleteAll" 
                color="danger"
                size="sm"
                wire:confirm="Are you sure you want to delete all notifications?"
            >
                <x-heroicon-m-trash class="w-4 h-4 mr-1" />
                Delete All
            </x-filament::button>
        </div>

        <!-- Notifications Table -->
        {{ $this->table }}
    </div>
</x-filament-panels::page>