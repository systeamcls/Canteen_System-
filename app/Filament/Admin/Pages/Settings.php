<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

class Settings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.admin.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'canteen_name' => config('app.name', 'Laravel Canteen System'),
            'canteen_description' => 'Fresh, delicious meals served daily',
            'operating_hours' => '6:00 AM - 8:00 PM',
            'contact_phone' => '+63 123 456 7890',
            'contact_email' => 'info@canteen.com',
            'address' => '123 Main Street, City, Philippines',
            'enable_notifications' => true,
            'enable_realtime_updates' => true,
            'max_order_items' => 10,
            'delivery_fee' => 0,
            'tax_rate' => 0,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Canteen Information')
                    ->schema([
                        TextInput::make('canteen_name')
                            ->label('Canteen Name')
                            ->required()
                            ->maxLength(255),
                        
                        Textarea::make('canteen_description')
                            ->label('Description')
                            ->maxLength(500)
                            ->rows(3),
                        
                        FileUpload::make('logo')
                            ->label('Logo')
                            ->image()
                            ->directory('settings')
                            ->maxSize(2048),
                        
                        TextInput::make('operating_hours')
                            ->label('Operating Hours')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Section::make('Contact Information')
                    ->schema([
                        TextInput::make('contact_phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(255),
                        
                        TextInput::make('contact_email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255),
                        
                        Textarea::make('address')
                            ->label('Address')
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('System Settings')
                    ->schema([
                        Toggle::make('enable_notifications')
                            ->label('Enable Notifications')
                            ->helperText('Send notifications for order updates'),
                        
                        Toggle::make('enable_realtime_updates')
                            ->label('Enable Real-time Updates')
                            ->helperText('Live updates for orders and dashboard'),
                        
                        TextInput::make('max_order_items')
                            ->label('Maximum Items per Order')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(50),
                        
                        TextInput::make('delivery_fee')
                            ->label('Delivery Fee')
                            ->numeric()
                            ->prefix('₱')
                            ->minValue(0),
                        
                        TextInput::make('tax_rate')
                            ->label('Tax Rate (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%'),
                    ])
                    ->columns(2),
                
                Section::make('Payment Methods')
                    ->schema([
                        Toggle::make('accept_cash')
                            ->label('Accept Cash Payments')
                            ->default(true),
                        
                        Toggle::make('accept_gcash')
                            ->label('Accept GCash Payments')
                            ->default(true),
                        
                        Toggle::make('accept_cards')
                            ->label('Accept Card Payments')
                            ->default(false),
                        
                        Toggle::make('accept_online')
                            ->label('Accept Online Payments')
                            ->default(false),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        
        // Here you would typically save to database or config files
        // For now, we'll just show a success notification
        
        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }
}