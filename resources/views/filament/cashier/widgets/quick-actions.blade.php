{{-- resources/views/filament/cashier/widgets/quick-actions.blade.php --}}

<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            ⚡ Quick Actions
        </x-slot>
        
        <x-slot name="description">
            Essential cashier operations at your fingertips
        </x-slot>

        <div class="grid grid-cols-2 gap-4">
            {{-- Open POS --}}
            <a href="/cashier/pos-system" 
               class="flex flex-col items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg border border-green-200 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-green-500 rounded-full mb-3 group-hover:bg-green-600 transition-colors">
                    <x-heroicon-o-calculator class="w-6 h-6 text-white" />
                </div>
                <span class="text-sm font-medium text-green-700 text-center">Open POS</span>
            </a>

            {{-- View Orders --}}
            <a href="/cashier/orders" 
               class="flex flex-col items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg border border-blue-200 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-500 rounded-full mb-3 group-hover:bg-blue-600 transition-colors">
                    <x-heroicon-o-shopping-bag class="w-6 h-6 text-white" />
                </div>
                <span class="text-sm font-medium text-blue-700 text-center">View Orders</span>
            </a>

            {{-- Manage Products --}}
            <a href="/cashier/products" 
               class="flex flex-col items-center p-4 bg-orange-50 hover:bg-orange-100 rounded-lg border border-orange-200 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-orange-500 rounded-full mb-3 group-hover:bg-orange-600 transition-colors">
                    <x-heroicon-o-cube class="w-6 h-6 text-white" />
                </div>
                <span class="text-sm font-medium text-orange-700 text-center">Products</span>
            </a>

            {{-- Refresh Data --}}
            <button onclick="window.location.reload()" 
                    class="flex flex-col items-center p-4 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 transition-colors group">
                <div class="flex items-center justify-center w-12 h-12 bg-gray-500 rounded-full mb-3 group-hover:bg-gray-600 transition-colors">
                    <x-heroicon-o-arrow-path class="w-6 h-6 text-white" />
                </div>
                <span class="text-sm font-medium text-gray-700 text-center">Refresh</span>
            </button>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>