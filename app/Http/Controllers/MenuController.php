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
        $search = $request->get('search');
        $stallId = $request->get('stall');

        // Query products with relationships
        $query = Product::with(['stall', 'category'])
            ->where('is_available', true)
            ->where('is_published', true);

        // Filter by category (using database category_id)
        if ($categoryId) {
            $query->where('category_id', $categoryId);
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

        // Get all active stalls for filter dropdown
        $stalls = Stall::where('is_active', true)->get();

        // Get all active categories from database
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('menu.index', compact(
            'products',
            'stalls',
            'categories',
            'categoryId',
            'search',
            'stallId'
        ));
    }
}