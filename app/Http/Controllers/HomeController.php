<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stall;
use Illuminate\Http\Request;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        // ✅ Get REAL popular items based on order count WITH STOCK CHECK
        $topFoods = Product::with('stall')
            ->where('is_available', true)
            ->where('is_published', true)
            ->where('stock_quantity', '>', 0) // ⭐ Only show in-stock items
            ->withCount('orderItems') // Count how many times ordered
            ->orderBy('order_items_count', 'desc') // Most ordered first!
            ->limit(4)
            ->get();
        
        // Fallback to newest if no orders yet
        if ($topFoods->isEmpty() || $topFoods->sum('order_items_count') === 0) {
            $topFoods = Product::with('stall')
                ->where('is_available', true)
                ->where('is_published', true)
                ->where('stock_quantity', '>', 0) // ⭐ Only show in-stock items
                ->latest()
                ->limit(4)
                ->get();
        }

        // ✅ Get popular stalls (by total orders of their products) WITH STOCK CHECK
        $popularStalls = Stall::where('is_active', true)
            ->with('products')
            ->withCount(['products' => function ($query) {
                $query->where('is_available', true)
                      ->where('stock_quantity', '>', 0); // ⭐ Only count in-stock products
            }])
            ->having('products_count', '>', 0) // ⭐ Only show stalls with available products
            ->orderBy('products_count', 'desc')
            ->limit(3)
            ->get();

        // ✅ Get featured items (also by popularity) WITH STOCK CHECK
        $featuredItems = Product::with('stall')
            ->where('is_available', true)
            ->where('is_published', true)
            ->where('stock_quantity', '>', 0) // ⭐ Only show in-stock items
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(6)
            ->get();
        
        // Fallback for featured items
        if ($featuredItems->isEmpty() || $featuredItems->sum('order_items_count') === 0) {
            $featuredItems = Product::with('stall')
                ->where('is_available', true)
                ->where('is_published', true)
                ->where('stock_quantity', '>', 0) // ⭐ Only show in-stock items
                ->latest()
                ->limit(6)
                ->get();
        }

        // Featured stalls with available products count WITH STOCK CHECK
        $featuredStalls = Stall::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_available', true)
                      ->where('stock_quantity', '>', 0); // ⭐ Only count in-stock products
            }])
            ->having('products_count', '>', 0) // ⭐ Only show stalls with available products
            ->take(3)
            ->get();

        // ✅ Trending products (last 7 days orders) WITH STOCK CHECK
        $trendingProducts = Product::where('is_available', true)
            ->where('is_published', true)
            ->where('stock_quantity', '>', 0) // ⭐ Only show in-stock items
            ->with('stall')
            ->withCount([
                'orderItems' => function ($query) {
                    $query->whereHas('order', function ($q) {
                        $q->where('created_at', '>=', now()->subDays(7));
                    });
                }
            ])
            ->orderBy('order_items_count', 'desc')
            ->take(6)
            ->get();
        
        // Fallback for trending
        if ($trendingProducts->isEmpty() || $trendingProducts->sum('order_items_count') === 0) {
            $trendingProducts = Product::where('is_available', true)
                ->where('is_published', true)
                ->where('stock_quantity', '>', 0) // ⭐ Only show in-stock items
                ->with('stall')
                ->latest()
                ->take(6)
                ->get();
        }

        // Get categories for homepage
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('home.index', compact(
            'topFoods', 
            'popularStalls', 
            'featuredItems',
            'featuredStalls',
            'trendingProducts',
            'categories'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('menu.index');
        }

        // Search in products WITH STOCK CHECK
        $products = Product::with('stall')
            ->where('is_available', true)
            ->where('stock_quantity', '>', 0) // ⭐ Only show in-stock items
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->paginate(12);

        // Search in stalls
        $stalls = Stall::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->get();

        return view('search.results', compact('products', 'stalls', 'query'));
    }
}