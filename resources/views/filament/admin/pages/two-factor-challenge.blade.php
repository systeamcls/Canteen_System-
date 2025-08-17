<x-filament-panels::layout.simple>
    <div class="fi-simple-page">
        <div class="fi-simple-header mb-6 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary-100 dark:bg-primary-900">
                <x-heroicon-o-shield-check class="h-8 w-8 text-primary-600 dark:text-primary-400" />
            </div>
            
            <h1 class="fi-header-heading text-2xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-3xl">
                {{ $this->getTitle() }}
            </h1>
            
            @if($subheading = $this->getSubheading())
                <p class="fi-simple-header-subheading mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    {{ $subheading }}
                </p>
            @endif
        </div>
        
        <div class="fi-simple-main max-w-md mx-auto">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
                {{ $this->form }}
            </div>
            
            <div class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400">
                <p>Lost your device? Contact your administrator for assistance.</p>
            </div>
        </div>
    </div>
</x-filament-panels::layout.simple>