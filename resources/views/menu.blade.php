<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu - KAJACMS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#ea580c',
                        secondary: '#fb923c'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-primary">KAJACMS</h1>
                </div>
                <nav class="hidden md:flex space-x-6">
                    <a href="{{ route('welcome') }}" class="text-gray-600 hover:text-primary">Home</a>
                    <a href="{{ route('menu.index') }}" class="text-primary font-semibold">Menu</a>
                    <a href="{{ route('stalls.index') }}" class="text-gray-600 hover:text-primary">Stalls</a>
                </nav>
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.68 4.32M7 13l1.68-4.32M20 13v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6"></path>
                        </svg>
                        <span class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">0</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Page Title -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Find The Best Food</h2>
            <p class="text-gray-600">Discover delicious dishes from all our partner stalls in one place.</p>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" action="{{ route('menu.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <input type="text" 
                           name="search" 
                           value="{{ $search }}"
                           placeholder="Search dishes, cuisines, or stalls..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                </div>

                <!-- Stall Filter -->
                <div>
                    <select name="stall" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">All Stalls</option>
                        @foreach($stalls as $stall)
                            <option value="{{ $stall->id }}" {{ $stallId == $stall->id ? 'selected' : '' }}>
                                {{ $stall->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $categoryName)
                            <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>
                                {{ $categoryName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Submit -->
                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-primary text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition">
                        Filter
                    </button>
                    <a href="{{ route('menu.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Category Tabs -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('menu.index') }}" 
                   class="px-4 py-2 rounded-full {{ !$category ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition">
                    All
                </a>
                @foreach($categories as $key => $categoryName)
                    <a href="{{ route('menu.index', ['category' => $key]) }}" 
                       class="px-4 py-2 rounded-full {{ $category == $key ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }} transition">
                        {{ $categoryName }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Products Grid -->
        @if($products->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition group">
                        <!-- Product Image -->
                        <div class="relative h-48 bg-gray-200">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h4a1 1 0 110 2h-1v12a2 2 0 01-2 2H6a2 2 0 01-2-2V6H3a1 1 0 110-2h4zM9 6h6v11H9V6z"></path>
                                    </svg>
                                </div>
                            @endif
                            <!-- Favorite Button -->
                            <button class="absolute top-3 right-3 p-2 bg-white rounded-full shadow-md hover:bg-gray-50 transition">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Product Info -->
                        <div class="p-4">
                            <div class="mb-2">
                                <h3 class="font-semibold text-lg text-gray-800 mb-1">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $product->description }}</p>
                            </div>
                            
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xl font-bold text-primary">₱{{ number_format($product->price, 2) }}</span>
                                <span class="text-sm text-gray-500">{{ $product->stall->name }}</span>
                            </div>

                            <!-- Rating -->
                            <div class="flex items-center mb-3">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                            <path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500 ml-2">4.8</span>
                            </div>

                            <!-- Add to Cart Button -->
                            <button class="w-full bg-primary text-white py-2 px-4 rounded-lg hover:bg-orange-700 transition-colors">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="flex justify-center">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.084-2.343"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No products found</h3>
                <p class="text-gray-500">Try adjusting your search or filter criteria.</p>
            </div>
        @endif
    </main>
</body>
</html>