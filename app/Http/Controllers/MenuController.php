<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stall;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $search = $request->get('search');
        $stallId = $request->get('stall');

        $query = Product::with('stall')->where('is_available', true);

        // Filter by category if provided
        if ($category) {
            $query->where('category', $category);
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

        // Get food categories as specified in requirements
        $categories = [
            'pizza' => 'Pizza',
            'fast-food' => 'Fast Food',
            'noodle' => 'Noodle',
            'dessert' => 'Dessert',
            'sea-food' => 'Sea Food',
            'sushi' => 'Sushi',
            'ramen' => 'Ramen'
        ];

        return view('menu', compact('products', 'stalls', 'categories', 'category', 'search', 'stallId'));
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