<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('KAJACMS Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold" style="color: #ea580c; margin-bottom: 10px;">
                    Welcome to KAJACMS Dashboard
                </h1>
                <p class="text-gray-600 text-lg">
                    Hello {{ auth()->user()->name }}! You are logged in as: 
                    <span style="background: #fef3c7; color: #92400e; padding: 4px 8px; border-radius: 6px; font-weight: 600;">
                        @if(auth()->user()->hasRole('admin'))
                            Admin (Concessionaire)
                        @elseif(auth()->user()->hasRole('tenant'))
                            Tenant
                        @elseif(auth()->user()->hasRole('cashier'))
                            Cashier
                        @else
                            Employee
                        @endif
                    </span>
                </p>
            </div>

            <!-- Admin Dashboard -->
            @if(auth()->user()->hasRole('admin'))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- System Overview -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4" style="border-left-color: #ea580c;">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 System Overview</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Stalls:</span>
                                <span class="font-semibold" style="color: #ea580c;">{{ \App\Models\Stall::count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Active Tenants:</span>
                                <span class="font-semibold" style="color: #ea580c;">{{ \App\Models\User::role('tenant')->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Products:</span>
                                <span class="font-semibold" style="color: #ea580c;">{{ \App\Models\Product::count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Orders:</span>
                                <span class="font-semibold" style="color: #ea580c;">{{ \App\Models\Order::count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue & Financial -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">💰 Financial Overview</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Monthly Revenue:</span>
                                <span class="font-semibold text-green-600">₱{{ number_format(\App\Models\Order::sum('total_amount'), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Rental Income:</span>
                                <span class="font-semibold text-green-600">₱{{ number_format(\App\Models\Stall::sum('rental_fee'), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Commission:</span>
                                <span class="font-semibold text-green-600">₱{{ number_format(\App\Models\Order::sum('total_amount') * 0.05, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="/admin" class="block text-center py-3 px-4 rounded-lg text-white font-semibold transition-all hover:opacity-90" style="background: #ea580c;">
                                🏢 Admin Panel
                            </a>
                            <a href="{{ route('menu.index') }}" class="block text-center py-3 px-4 rounded-lg font-semibold transition-all hover:opacity-90" style="background: #fed7aa; color: #ea580c;">
                                🛒 View Menu
                            </a>
                            <a href="{{ route('stalls.index') }}" class="block text-center py-3 px-4 rounded-lg font-semibold transition-all hover:opacity-90" style="background: #fed7aa; color: #ea580c;">
                                🏪 Manage Stalls
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Tenant Dashboard -->
            @if(auth()->user()->hasRole('tenant'))
                @php
                    $userStall = auth()->user()->stall;
                    $stallProducts = $userStall ? $userStall->products : collect();
                    $stallOrders = $userStall ? \App\Models\Order::whereHas('items.product', function($q) use ($userStall) {
                        $q->where('stall_id', $userStall->id);
                    }) : collect();
                @endphp
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- My Stall Overview -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4" style="border-left-color: #ea580c;">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🏪 My Stall: {{ $userStall->name ?? 'No Stall Assigned' }}</h3>
                        @if($userStall)
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Location:</span>
                                    <span class="font-semibold" style="color: #ea580c;">{{ $userStall->location }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Monthly Rent:</span>
                                    <span class="font-semibold" style="color: #ea580c;">₱{{ number_format($userStall->rental_fee, 2) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Products:</span>
                                    <span class="font-semibold" style="color: #ea580c;">{{ $stallProducts->count() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="px-2 py-1 rounded font-semibold {{ $userStall->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $userStall->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Sales Overview -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📈 My Sales</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Orders:</span>
                                <span class="font-semibold text-green-600">{{ $stallOrders->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Revenue:</span>
                                <span class="font-semibold text-green-600">₱{{ number_format($stallOrders->sum('total_amount'), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Avg. Order:</span>
                                <span class="font-semibold text-green-600">₱{{ $stallOrders->count() > 0 ? number_format($stallOrders->avg('total_amount'), 2) : '0.00' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="/admin" class="block text-center py-3 px-4 rounded-lg text-white font-semibold transition-all hover:opacity-90" style="background: #ea580c;">
                                🛠️ Manage Products
                            </a>
                            <a href="{{ route('menu.index') }}" class="block text-center py-3 px-4 rounded-lg font-semibold transition-all hover:opacity-90" style="background: #fed7aa; color: #ea580c;">
                                👀 View My Products
                            </a>
                            @if($userStall)
                            <a href="{{ route('stalls.show', $userStall) }}" class="block text-center py-3 px-4 rounded-lg font-semibold transition-all hover:opacity-90" style="background: #fed7aa; color: #ea580c;">
                                🏪 View My Stall
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Cashier Dashboard -->
            @if(auth()->user()->hasRole('cashier'))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Today's Orders -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4" style="border-left-color: #ea580c;">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🛒 Today's Orders</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Orders Processed:</span>
                                <span class="font-semibold" style="color: #ea580c;">{{ \App\Models\Order::whereDate('created_at', today())->count() }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Sales:</span>
                                <span class="font-semibold" style="color: #ea580c;">₱{{ number_format(\App\Models\Order::whereDate('created_at', today())->sum('total_amount'), 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pending Orders:</span>
                                <span class="font-semibold text-yellow-600">{{ \App\Models\Order::where('status', 'pending')->count() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- POS System -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">💳 Point of Sale</h3>
                        <div class="space-y-3">
                            <a href="{{ route('menu.index') }}" class="block text-center py-4 px-4 rounded-lg text-white font-semibold text-lg transition-all hover:opacity-90" style="background: #ea580c;">
                                🛒 Create Walk-in Order
                            </a>
                            <div class="text-center text-gray-600 text-sm">
                                Process orders for customers at the canteen
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">⚡ Quick Actions</h3>
                        <div class="space-y-2">
                            <a href="/admin" class="block text-center py-3 px-4 rounded-lg font-semibold transition-all hover:opacity-90" style="background: #fed7aa; color: #ea580c;">
                                📋 View All Orders
                            </a>
                            <a href="{{ route('stalls.index') }}" class="block text-center py-3 px-4 rounded-lg font-semibold transition-all hover:opacity-90" style="background: #fed7aa; color: #ea580c;">
                                🏪 View All Stalls
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Customer/Employee Dashboard (Default) -->
            @if(auth()->user()->hasRole('customer') || !auth()->user()->hasAnyRole(['admin', 'tenant', 'cashier']))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Browse Menu -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4" style="border-left-color: #ea580c;">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🍽️ Browse Menu</h3>
                        <p class="text-gray-600 mb-4">Explore delicious food from our multi-vendor canteen</p>
                        <a href="{{ route('menu.index') }}" class="block text-center py-3 px-4 rounded-lg text-white font-semibold transition-all hover:opacity-90" style="background: #ea580c;">
                            🛒 View Full Menu
                        </a>
                    </div>

                    <!-- My Orders -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-green-500">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 My Orders</h3>
                        @php
                            $userOrders = \App\Models\Order::where('user_id', auth()->id())->latest()->take(3)->get();
                        @endphp
                        @if($userOrders->count() > 0)
                            <div class="space-y-2">
                                @foreach($userOrders as $order)
                                    <div class="border border-gray-200 p-3 rounded">
                                        <div class="flex justify-between mb-1">
                                            <span class="font-semibold">Order #{{ $order->id }}</span>
                                            <span class="text-green-600">₱{{ number_format($order->total_amount, 2) }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600">{{ $order->created_at->format('M d, Y') }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-600">No orders yet. Start ordering from the menu!</p>
                        @endif
                    </div>

                    <!-- Explore Stalls -->
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🏪 Explore Stalls</h3>
                        <p class="text-gray-600 mb-4">Discover food from different vendors</p>
                        <a href="{{ route('stalls.index') }}" class="block text-center py-3 px-4 rounded-lg font-semibold transition-all hover:opacity-90" style="background: #fed7aa; color: #ea580c;">
                            🔍 Browse All Stalls
                        </a>
                    </div>
                </div>
            @endif

            <!-- KAJACMS System Status -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-6">📊 KAJACMS System Status</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div class="text-center p-6 rounded-lg" style="background: #fef3c7;">
                        <div class="text-4xl mb-3">🏪</div>
                        <div class="font-semibold" style="color: #92400e;">{{ \App\Models\Stall::count() }} Stalls</div>
                        <div class="text-sm" style="color: #92400e;">Multi-vendor marketplace</div>
                    </div>
                    <div class="text-center p-6 rounded-lg" style="background: #fef3c7;">
                        <div class="text-4xl mb-3">🍽️</div>
                        <div class="font-semibold" style="color: #92400e;">{{ \App\Models\Product::count() }} Products</div>
                        <div class="text-sm" style="color: #92400e;">Delicious options</div>
                    </div>
                    <div class="text-center p-6 rounded-lg" style="background: #fef3c7;">
                        <div class="text-4xl mb-3">👥</div>
                        <div class="font-semibold" style="color: #92400e;">{{ \App\Models\User::count() }} Users</div>
                        <div class="text-sm" style="color: #92400e;">Active community</div>
                    </div>
                    <div class="text-center p-6 rounded-lg" style="background: #fef3c7;">
                        <div class="text-4xl mb-3">🛒</div>
                        <div class="font-semibold" style="color: #92400e;">{{ \App\Models\Order::count() }} Orders</div>
                        <div class="text-sm" style="color: #92400e;">Successful transactions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
