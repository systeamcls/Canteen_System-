<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Canteen System') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('home.index') }}" class="text-2xl font-bold text-red-600">
                            Kajacms
                        </a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('menu.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('menu') ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">
                            Our Menu
                        </a>
                        <a href="{{ route('home.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('home') ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">
                            Home
                        </a>
                        <a href="{{ route('stalls.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('stalls') ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-500 hover:text-gray-700' }}">
                            Stalls
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="flex-1 flex items-center justify-end">
                        <div class="flex-shrink-0">
                            <div class="relative">
                                <a href="{{ route('cart') }}" class="p-2 text-gray-600 hover:text-gray-900">
                                    <span class="sr-only">Shopping cart</span>
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    @if(session()->has('cart') && count(session('cart')) > 0)
                                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                                            {{ count(session('cart')) }}
                                        </span>
                                    @endif
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>