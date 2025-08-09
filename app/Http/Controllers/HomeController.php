<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stall;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured/popular products (keeping your existing logic)
        $topFoods = Product::with('stall')
            ->where('is_available', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Get popular stalls (keeping your existing logic)
        $popularStalls = Stall::where('is_active', true)
            ->with('products')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(3)
            ->get();

        // Get featured items (keeping your existing logic)
        $featuredItems = Product::with('stall')
            ->where('is_available', true)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        // Additional data for the new home page design
        $featuredStalls = Stall::where('is_active', true)
            ->withCount(['products' => function ($query) {
                $query->where('is_available', true);
            }])
            ->take(3)
            ->get();

        $trendingProducts = Product::where('is_available', true)
            ->with('stall')
            ->take(6)
            ->get();

        return view('home.index', compact(
            'topFoods', 
            'popularStalls', 
            'featuredItems',
            'featuredStalls',
            'trendingProducts'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query) {
            return redirect()->route('menu.index');
        }

        // Search in products (keeping your existing logic)
        $products = Product::with('stall')
            ->where('is_available', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->paginate(12);

        // Add stalls search
        $stalls = Stall::where('is_active', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->get();

        return view('search.results', compact('products', 'stalls', 'query'));
    }
}