<?php

namespace App\Http\Controllers;

use App\Models\Stall;
use Illuminate\Http\Request;

class StallController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = Stall::with(['products' => function($q) {
            $q->where('is_available', true);
        }])->withCount('products');

        // Filter by search term
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status !== null) {
            $query->where('is_active', $status === 'open');
        }

        $stalls = $query->paginate(9);

        return view('stalls.index', compact('stalls', 'search', 'status'));
    }

    public function show(Stall $stall)
    {
        $stall->load(['products' => function($q) {
            $q->where('is_available', true);
        }]);

        // Get categories of products in this stall
        $categories = $stall->products->pluck('category')->unique()->filter();

        return view('stalls.show', compact('stall', 'categories'));
    }
}