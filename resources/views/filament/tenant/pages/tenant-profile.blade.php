<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Profile Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center space-x-6">
                @if(Auth::user()->assignedStall?->logo)
                    <img src="{{ Storage::disk('public')->url(Auth::user()->assignedStall->logo) }}" 
                         alt="{{ Auth::user()->assignedStall->name }}" 
                         class="w-20 h-20 rounded-full object-cover border-4 border-gray-200 dark:border-gray-600">
                @else
                    <div class="w-20 h-20 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m5 0v-9a2 2 0 012-2h2a2 2 0 012 2v9M7 21h2m-2 0v-9a2 2 0 012-2h2a2 2 0 012 2v9"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ Auth::user()->name }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        {{ Auth::user()->assignedStall?->name ?? 'No Stall Assigned' }}
                    </p>
                    @if(Auth::user()->assignedStall)
                        <div class="flex items-center mt-2 space-x-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ Auth::user()->assignedStall->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ Auth::user()->assignedStall->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            @if(Auth::user()->assignedStall->location)
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    📍 {{ Auth::user()->assignedStall->location }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

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
        @if(Auth::user()->assignedStall)
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
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            No Stall Assigned
                        </h3>
                        <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                            Contact the canteen administrator to get assigned to a stall and start managing your business.
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

        <!-- Business Overview (Optional) -->
        @if(Auth::user()->assignedStall)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Business Overview
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ Auth::user()->assignedStall->products_count ?? 0 }}
                        </div>
                        <div class="text-sm text-blue-600 dark:text-blue-400">Products</div>
                    </div>
                    
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ Auth::user()->assignedStall->is_active ? 'Open' : 'Closed' }}
                        </div>
                        <div class="text-sm text-green-600 dark:text-green-400">Status</div>
                    </div>
                    
                    <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ Auth::user()->created_at->format('M Y') }}
                        </div>
                        <div class="text-sm text-purple-600 dark:text-purple-400">Joined</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>