<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stall;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categoryId = $request->get('category_id');
        $categorySlug = $request->get('category'); // Keep for backward compatibility
        $search = $request->get('search');
        $stallId = $request->get('stall');

        $query = Product::with('stall')->where('is_available', true);

        // Filter by category - support both new (ID) and old (slug) parameters
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        } elseif ($categorySlug) {
            // Backward compatibility: map old slugs to category IDs
            // TODO: Update these IDs after running CategorySeeder to match actual IDs
            $categoryMap = [
                'fresh-meals' => 1,
                'sandwiches' => 2,
                'beverages' => 3,
                'snacks' => 4,
                'desserts' => 5,
            ];
            
            if (isset($categoryMap[$categorySlug])) {
                $query->where('category_id', $categoryMap[$categorySlug]);
                $categoryId = $categoryMap[$categorySlug]; // Set for view consistency
            }
        }

        // Filter by search term
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by stall
        if ($stallId) {
            $query->where('stall_id', $stallId);
        }

        $products = $query->paginate(12);

        // Get all stalls for filter dropdown
        $stalls = Stall::where('is_active', true)->get();

        // Get categories from database
        $categories = Category::where('is_active', true)
                             ->orderBy('sort_order')
                             ->get();

        // Pass both for backward compatibility
        return view('menu.index', compact('products', 'stalls', 'categories', 'categoryId', 'categorySlug', 'search', 'stallId'));
    }

    public function show(Product $product)
    {
        $product->load('stall');

        // Get related products from same stall
        $relatedProducts = Product::where('stall_id', $product->stall_id)
            ->where('id', '!=', $product->id)
            ->where('is_available', true)
            ->limit(4)
            ->get();

        return view('menu.show', compact('product', 'relatedProducts'));
    }
}