<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold text-gray-900">Our Food Stalls</h1>
            <p class="mt-2 text-gray-600">Explore delicious offerings from our vendors</p>
            
            <div class="mt-6 flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.debounce.300ms="search"
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="Search stalls or products..."
                        >
                        <div class="absolute left-3 top-2.5">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                
                <div class="sm:w-64">
                    <select
                        wire:model="selectedCategory"
                        class="w-full rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    >
                        <option value="">All Categories</option>
                        <option value="meals">Meals</option>
                        <option value="drinks">Drinks</option>
                        <option value="snacks">Snacks</option>
                        <option value="desserts">Desserts</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Stalls Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Debug info -->
        <div class="mb-4 p-4 bg-yellow-100 rounded">
            Debug: Stalls count = {{ $debug_count ?? 'undefined' }}
            @if(isset($stalls))
                | Stalls variable exists with {{ count($stalls) }} items
            @else
                | Stalls variable does not exist
            @endif
        </div>
        
        <div class="grid grid-cols-1 gap-8">
            @isset($stalls)  <!-- Check if variable exists -->
                @foreach($stalls as $stall)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <!-- Stall Header -->
                        <div class="relative h-48 md:h-64">
                            <img
                                src="{{ $stall->image ? Storage::url($stall->image) : asset('images/default-stall.jpg') }}"
                                alt="{{ $stall->name }}"
                                class="w-full h-full object-cover"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <div class="absolute bottom-4 left-4">
                                <h2 class="text-2xl font-bold text-white">{{ $stall->name }}</h2>
                            </div>
                        </div>

                        <!-- Products Grid -->
                        <div class="p-4">
                            @if($stall->products && $stall->products->where('is_available', true)->isNotEmpty())
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($stall->products->where('is_available', true) as $product)
                                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                                            @if($product->image)
                                                <img
                                                    src="{{ Storage::url($product->image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="w-full h-48 object-cover"
                                                >
                                            @endif
                                            
                                            <div class="p-4">
                                                <h3 class="font-semibold text-gray-900">{{ $product->name }}</h3>
                                                <p class="text-sm text-gray-600 mt-1">{{ $product->description }}</p>
                                                <div class="mt-4 flex items-center justify-between">
                                                    <span class="text-lg font-bold text-red-600">₱{{ number_format($product->price, 2) }}</span>
                                                    <button
                                                        wire:click="addToCart({{ $product->id }})"
                                                        class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                                    >
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center py-4 text-gray-500">No available products in this stall</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-center py-12 text-gray-500">No stalls available</p>
            @endisset
        </div>
    </div>

    <!-- Notification -->
    <div
        x-data="{ show: false, message: '' }"
        @notify.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
        class="fixed bottom-4 right-4"
    >
        <div
            x-show="show"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="bg-green-500 text-white px-4 py-3 rounded-lg shadow-lg"
        >
            <p x-text="message"></p>
        </div>
    </div>
</div>
