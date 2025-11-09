<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Cover Photo Header -->
        @if (Auth::user()->assignedStall)
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <!-- Cover Photo -->
                <div class="relative h-48 md:h-64 bg-gradient-to-r from-orange-400 to-orange-600">
                    @if (Auth::user()->assignedStall->cover_photo)
                        <img src="{{ Storage::disk('public')->url(Auth::user()->assignedStall->cover_photo) }}"
                            alt="Cover Photo" class="w-full h-full object-cover">
                    @else
                        <!-- Default gradient background if no cover photo -->
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-400 via-orange-500 to-orange-600">
                        </div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white opacity-20" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-9a2 2 0 012-2h2a2 2 0 012 2v9M7 21h2m-2 0v-9a2 2 0 012-2h2a2 2 0 012 2v9">
                                </path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Profile Info Overlay -->
                <div class="relative px-6 pb-6">
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-6 -mt-16">
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                            @if (Auth::user()->assignedStall->logo)
                                <img src="{{ Storage::disk('public')->url(Auth::user()->assignedStall->logo) }}"
                                    alt="{{ Auth::user()->assignedStall->name }}"
                                    class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-lg">
                            @else
                                <div
                                    class="w-32 h-32 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center border-4 border-white dark:border-gray-800 shadow-lg">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-9a2 2 0 012-2h2a2 2 0 012 2v9M7 21h2m-2 0v-9a2 2 0 012-2h2a2 2 0 012 2v9">
                                        </path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="flex-1 mt-4 md:mt-0 md:mb-2">
                            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                                {{ Auth::user()->name }}
                            </h2>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mt-1">
                                {{ Auth::user()->assignedStall->name }}
                            </p>
                            <div class="flex flex-wrap items-center mt-3 gap-3">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ Auth::user()->assignedStall->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                    <span
                                        class="w-2 h-2 rounded-full {{ Auth::user()->assignedStall->is_active ? 'bg-green-500' : 'bg-red-500' }} mr-2"></span>
                                    {{ Auth::user()->assignedStall->is_active ? 'Active' : 'Inactive' }}
                                </span>
                                @if (Auth::user()->assignedStall->location)
                                    <span class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ Auth::user()->assignedStall->location }}
                                    </span>
                                @endif
                                @if (Auth::user()->assignedStall->contact_number)
                                    <span class="inline-flex items-center text-sm text-gray-600 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                        {{ Auth::user()->assignedStall->contact_number }}
                                    </span>
                                @endif
                            </div>
                            @if (Auth::user()->assignedStall->description)
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-400 max-w-3xl">
                                    {{ Auth::user()->assignedStall->description }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Fallback for users without stall -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center space-x-6">
                    <div class="w-20 h-20 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ Auth::user()->name }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            No Stall Assigned
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Profile Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Personal Information
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your account details and contact information.
                </p>
            </div>

            <div class="p-6">
                <form wire:submit="updateProfile">
                    {{ $this->profileForm }}

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit" icon="heroicon-o-check">
                            Update Profile
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Stall Form -->
        @if (Auth::user()->assignedStall)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        Stall Information
                    </h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Manage your stall details and customer-facing information.
                    </p>
                </div>

                <div class="p-6">
                    <form wire:submit="updateStall">
                        {{ $this->stallForm }}

                        <div class="mt-6 flex justify-end">
                            <x-filament::button type="submit" icon="heroicon-o-building-storefront">
                                Update Stall
                            </x-filament::button>
                        </div>
                    </form>
                </div>
            </div>
        @else
            <div
                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            No Stall Assigned
                        </h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            Contact the canteen administrator to get assigned to a stall and start managing your
                            business.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Password Form -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Security Settings
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Update your password to keep your account secure.
                </p>
            </div>

            <div class="p-6">
                <form wire:submit="updatePassword">
                    {{ $this->passwordForm }}

                    <div class="mt-6 flex justify-end">
                        <x-filament::button type="submit" icon="heroicon-o-lock-closed" color="warning">
                            Change Password
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Stats (Optional) -->
        @if (Auth::user()->assignedStall)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ Auth::user()->assignedStall->products()->count() }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Products</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                    <div
                        class="text-3xl font-bold {{ Auth::user()->assignedStall->is_active ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ Auth::user()->assignedStall->is_active ? 'Open' : 'Closed' }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Status</div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 text-center">
                    <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                        {{ Auth::user()->created_at->format('M Y') }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Joined</div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
