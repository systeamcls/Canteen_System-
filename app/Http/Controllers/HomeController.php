<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stall;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get featured/popular products
        $topFoods = Product::with('stall')
            ->where('is_available', true)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        // Get popular stalls
        $popularStalls = Stall::where('is_active', true)
            ->with('products')
            ->withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(3)
            ->get();

        // Get featured items
        $featuredItems = Product::with('stall')
            ->where('is_available', true)
            ->inRandomOrder()
            ->limit(6)
            ->get();

        return view('home', compact('topFoods', 'popularStalls', 'featuredItems'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $products = Product::with('stall')
            ->where('is_available', true)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%");
            })
            ->paginate(12);

        return view('search-results', compact('products', 'query'));
    }
}