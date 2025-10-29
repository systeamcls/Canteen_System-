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
        // ✅ Get REAL popular items based on order count
        $topFoods = Product::with('stall')
            ->where('is_available', true)
            ->where('is_published', true)
            ->withCount('orderItems') // Count how many times ordered
            ->orderBy('order_items_count', 'desc') // Most ordered first!
            ->limit(4)
            ->get();
        
        // Fallback to newest if no orders yet
        if ($topFoods->isEmpty() || $topFoods->sum('order_items_count') === 0) {
            $topFoods = Product::with('stall')
                ->where('is_available', true)
                ->where('is_published', true)
                ->latest()
                ->limit(4)
                ->get();
        }

        // ✅ Get popular stalls (by total orders of their products)
        $popularStalls = Stall::where('is_active', true)
            ->with('products')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(3)
            ->get();

        // ✅ Get featured items (also by popularity)
        $featuredItems = Product::with('stall')
            ->where('is_available', true)
            ->where('is_published', true)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit(6)
            ->get();
        
        // Fallback for featured items
        if ($featuredItems->isEmpty() || $featuredItems->sum('order_items_count') === 0) {
            $featuredItems = Product::with('stall')
                ->where('is_available', true)
                ->where('is_published', true)
                ->latest()
                ->limit(6)
                ->get();
        }

        // Featured stalls with available products count
        $featuredStalls = Stall::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_available', true);
            }])
            ->take(3)
            ->get();

        // ✅ Trending products (last 7 days orders)
        $trendingProducts = Product::where('is_available', true)
            ->where('is_published', true)
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

        // Search in products
        $products = Product::with('stall')
            ->where('is_available', true)
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