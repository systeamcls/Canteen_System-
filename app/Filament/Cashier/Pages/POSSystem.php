<?php
// app/Filament/Cashier/Pages/POSSystem.php - FIXED

namespace App\Filament\Cashier\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderGroup;
use App\Models\Stall;
use Livewire\Component;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class POSSystem extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'POS System';
    protected static ?string $navigationGroup = '🎯 Operations';
    protected static ?int $navigationSort = 10;
    protected static string $view = 'filament.cashier.pages.pos-system';
    protected static ?string $title = '🛒 Point of Sale';
    protected $listeners = ['refreshData'];

    // Cart state
    public array $cart = [];
    public float $cartTotal = 0.00;
    public int $cartCount = 0;
    
    // Customer info
    public string $customerName = '';
    
    // Order settings
    public string $orderType = 'dine-in'; // dine-in or take-away
    public string $paymentMethod = 'cash'; // cash or qr
    public $cashReceived = 0.00;
    public $changeAmount = 0.00;
    public $showCashInput = false;
    
    // UI state
    public string $searchTerm = '';
    public ?int $selectedCategoryId = null; // Changed from $selectedCategory to $selectedCategoryId
    public bool $showOrderSummary = false;
    public bool $showReceipt = false;
    
    // Products and categories
    public $products;
    public $categories;
    public $filteredProducts;
    
    // Admin stall context
    public ?int $adminStallId = null;
    public $adminStall = null;
    
    // Current order details
    public string $orderNumber;
    public array $lastOrder = [];

    public function mount(): void
    {
        $this->getAdminStallContext();
        $this->loadData();
        $this->resetCart();
        $this->generateOrderNumber();
    }

    /**
     * Get admin stall context
     */
    public function getAdminStallContext(): void
    {
        $user = Auth::user();
        
        if ($user->admin_stall_id) {
            $this->adminStallId = $user->admin_stall_id;
            $this->adminStall = Stall::find($this->adminStallId);
        } else {
            $this->adminStall = Stall::where('owner_id', $user->id)->first();
            $this->adminStallId = $this->adminStall?->id;
        }
        
        // Fallback
        if (!$this->adminStallId) {
            $this->adminStallId = 1;
            $this->adminStall = Stall::find(1);
        }
    }

    public function loadData(): void
    {
        if (!$this->adminStallId) return;

        // Load products from admin's stall only
        $this->products = Product::with(['category'])
            ->where('stall_id', $this->adminStallId)
            ->where('is_available', true)
            ->where('is_published', true)
            ->orderBy('name')
            ->get();
            
        // Load categories that have products in admin's stall
        $this->categories = Category::whereHas('products', function ($query) {
            $query->where('stall_id', $this->adminStallId)
                  ->where('is_available', true)
                  ->where('is_published', true);
        })
        ->where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();
        
        $this->filterProducts();
    }

    public function filterProducts(): void
    {
        $query = $this->products;
        
        // Filter by category
        if ($this->selectedCategoryId) {
            $query = $query->where('category_id', $this->selectedCategoryId);
        }
        
        // Filter by search term
        if ($this->searchTerm) {
            $query = $query->filter(function ($product) {
                return str_contains(
                    strtolower($product->name), 
                    strtolower($this->searchTerm)
                ) || str_contains(
                    strtolower($product->description ?? ''), 
                    strtolower($this->searchTerm)
                );
            });
        }
        
        $this->filteredProducts = $query;
    }

    public function updatedSearchTerm(): void
    {
        $this->filterProducts();
    }

    public function selectCategory(?int $categoryId): void
    {
        $this->selectedCategoryId = $categoryId;
        $this->searchTerm = '';
        $this->filterProducts();
    }

    public function setOrderType(string $type): void
    {
        $this->orderType = $type;
    }

    public function setPaymentMethod(string $method): void
    {
        $this->paymentMethod = $method;
    }

    public function addToCart(int $productId): void
    {
        $product = Product::where('id', $productId)
            ->where('stall_id', $this->adminStallId)
            ->first();
        
        if (!$product || !$product->is_available) {
            Notification::make()
                ->title('Product Unavailable')
                ->body('This product is currently not available.')
                ->danger()
                ->send();
            return;
        }

        // Check stock if stock management is enabled
        if (isset($product->stock_quantity)) {
            $currentQty = collect($this->cart)->where('id', $productId)->sum('quantity');
            if ($currentQty >= $product->stock_quantity) {
                Notification::make()
                    ->title('Insufficient Stock')
                    ->body("Only {$product->stock_quantity} units available.")
                    ->warning()
                    ->send();
                return;
            }
        }

        $existingIndex = collect($this->cart)->search(function ($item) use ($productId) {
            return $item['id'] == $productId;
        });
        
        if ($existingIndex !== false) {
            $this->cart[$existingIndex]['quantity'] += 1;
        } else {
            $this->cart[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price / 100, // Convert from cents
                'quantity' => 1,
                'image' => $product->image_url ?? '🍽️',
            ];
        }
        
        $this->updateCartTotals();
    }

    public function updateCartQuantity(string $key, int $newQuantity): void
    {
        if (!isset($this->cart[$key])) {
            return;
        }

        if ($newQuantity <= 0) {
            $this->removeFromCart($key);
            return;
        }
        
        // Check stock limit
        $product = Product::find($this->cart[$key]['id']);
        if (isset($product->stock_quantity) && $newQuantity > $product->stock_quantity) {
            Notification::make()
                ->title('Stock Limit Reached')
                ->body("Only {$product->stock_quantity} units available.")
                ->warning()
                ->send();
            return;
        }
        
        $this->cart[$key]['quantity'] = $newQuantity;
        $this->updateCartTotals();
    }

    public function removeFromCart(string $key): void
    {
        if (isset($this->cart[$key])) {
            unset($this->cart[$key]);
            $this->cart = array_values($this->cart); // Re-index array
        }
        
        $this->updateCartTotals();
    }

    public function updateCartTotals(): void
{
    $this->cartTotal = (float) collect($this->cart)->sum(function ($item) {
        return (float) $item['price'] * (int) $item['quantity'];
    });
    
    $this->cartCount = (int) collect($this->cart)->sum('quantity');
}

    public function resetCart(): void
    {
        $this->cart = [];
        $this->cartTotal = 0.00;
        $this->cartCount = 0;
        $this->customerName = '';
        $this->showOrderSummary = false;
        $this->showReceipt = false;
        $this->cashReceived = 0.00;
        $this->changeAmount = 0.00;
        $this->showCashInput = false;
    }

    public function generateOrderNumber(): void
    {
        $this->orderNumber = 'POS-' . strtoupper(uniqid());
    }

    public function proceedToCheckout(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->title('Empty Cart')
                ->body('Please add items to cart before placing order.')
                ->warning()
                ->send();
            return;
        }

        $this->showOrderSummary = true;
    }

    public function confirmOrder(): void
    {
        if (empty($this->cart)) return;

        try {
            DB::transaction(function () {
                $user = Auth::user();
                
                // Check stock availability before processing
                foreach ($this->cart as $item) {
                    $product = Product::find($item['id']);
                    if (isset($product->stock_quantity) && $product->stock_quantity < $item['quantity']) {
                        throw new \Exception("Product {$product->name} has insufficient stock. Available: {$product->stock_quantity}, Requested: {$item['quantity']}");
                    }
                }
                
                // Create order group
                $orderGroup = OrderGroup::create([
                    'payer_type' => 'employee',
                    'user_id' => $user->id,
                    'payment_method' => $this->paymentMethod,
                    'payment_status' => 'pending',
                    'amount_total' => $this->cartTotal * 100,
                    'currency' => 'PHP',
                    'billing_contact' => json_encode([
                        'name' => $this->customerName ?: 'Walk-in Customer',
                        'phone' => null,
                        'email' => null,
                    ]),
                    'cart_snapshot' => json_encode($this->cart),
                ]);

                // Create the order - POS orders are always 'onsite' with service_type
                $order = Order::create([
                    'order_group_id' => $orderGroup->id,
                    'vendor_id' => $user->id,
                    'order_number' => $this->orderNumber,
                    'customer_name' => $this->customerName ?: 'Walk-in Customer',
                    'total_amount' => $this->cartTotal,
                    'amount_total' => $this->cartTotal * 100,
                    'amount_subtotal' => $this->cartTotal * 100,
                    'status' => 'pending',
                    'payment_method' => $this->paymentMethod,
                    'payment_status' => 'pending',
                    'order_type' => 'onsite', // POS orders are always onsite
                    'service_type' => $this->orderType, // 'dine-in' or 'take-away'
                    'user_type' => 'employee',
                    'notes' => 'Order created via POS by: ' . $user->name,
                ]);

                // Create order items and update stock
                foreach ($this->cart as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['price'],
                        'line_total' => $item['price'] * $item['quantity'] * 100,
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);
                    
                    // Update stock if stock management is enabled
                    $product = Product::find($item['id']);
                    if (isset($product->stock_quantity)) {
                        $product->decrement('stock_quantity', $item['quantity']);
                    }
                }

                // Store order details for receipt
                $this->lastOrder = [
                    'order_number' => $this->orderNumber,
                    'customer_name' => $this->customerName ?: 'Walk-in Customer',
                    'order_type' => $this->orderType,
                    'payment_method' => $this->paymentMethod,
                    'items' => $this->cart,
                    'total' => $this->cartTotal,
                    'cash_received' => $this->cashReceived, 
                    'change_amount' => $this->changeAmount,
                    'created_at' => now()->format('M d, Y h:i A'),
                ];

                Notification::make()
                    ->title('Order Placed Successfully!')
                    ->body("Order {$this->orderNumber} has been created")
                    ->success()
                    ->send();
            });
            
            // Show receipt
            $this->showOrderSummary = false;
            $this->showReceipt = true;

        } catch (\Exception $e) {
            Notification::make()
                ->title('Order Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function closeOrderSummary(): void
    {
        $this->showOrderSummary = false;
    }

    public function closeReceipt(): void
    {
        $this->showReceipt = false;
        $this->resetCart();
        $this->generateOrderNumber();
        
        // Reload data to update stock quantities
        $this->loadData();
    }

    public function refreshData(): void
    {
        $this->loadData();
        
        Notification::make()
            ->title('Data Refreshed')
            ->body('Product catalog updated')
            ->success()
            ->send();
    }

    // Computed properties for view
    public function getCategoriesProperty()
    {
        return $this->categories;
    }

    public function getFilteredProductsProperty()
    {
        return $this->filteredProducts ?? collect();
    }

    public function getOutOfStockCountProperty()
    {
        return $this->products->filter(function ($product) {
            return isset($product->stock_quantity) && $product->stock_quantity <= 0;
        })->count();
    }

    public function setCashReceived($amount)
{
    $this->cashReceived = (float) ($amount ?? 0);
    $this->calculateChange();
}

    public function calculateChange()
{
    $cashReceived = (float) ($this->cashReceived ?? 0);
    $cartTotal = (float) ($this->cartTotal ?? 0);
    $this->changeAmount = max(0, $cashReceived - $cartTotal);
}

public function showCashInputModal()
{
    if ($this->paymentMethod === 'cash') {
        $this->showCashInput = true;
        $this->cashReceived = 0.00;
        $this->changeAmount = 0.00;
    } else {
        $this->proceedToCheckout(); // For non-cash payments, go directly to order summary
    }
}

public function closeCashInput()
{
    $this->showCashInput = false;
    $this->cashReceived = 0.00;
    $this->changeAmount = 0.00;
}

public function placeOrder(): void
{
    $this->proceedToCheckout();
}

public function processCashPayment()
{
    $cashReceived = (float) ($this->cashReceived ?? 0);
    $cartTotal = (float) ($this->cartTotal ?? 0);
    
    if ($cashReceived >= $cartTotal) {
        $this->calculateChange();
        $this->showCashInput = false;
        $this->proceedToCheckout();
    }
}

public function updatedCashReceived($value)
{
    $this->cashReceived = (float) ($value ?? 0);
    $this->calculateChange();
}

}