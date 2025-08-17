<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        // Get popular products from different categories
        $gameProducts = Product::active()
            ->where('category', 'Game')
            ->orderBy('name')
            ->limit(6)
            ->get();

        $pulsaProducts = Product::active()
            ->where('category', 'Pulsa')
            ->orderBy('name')
            ->limit(6)
            ->get();

        $ppobProducts = Product::active()
            ->where('category', 'PPOB')
            ->orderBy('name')
            ->limit(6)
            ->get();

        // Get all categories for navigation
        $categories = Product::active()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return Inertia::render('welcome', [
            'gameProducts' => $gameProducts,
            'pulsaProducts' => $pulsaProducts,
            'ppobProducts' => $ppobProducts,
            'categories' => $categories,
            'totalProducts' => Product::active()->count(),
        ]);
    }

    /**
     * Search products.
     */
    public function store()
    {
        $query = request()->get('q', '');
        $category = request()->get('category', '');

        $products = Product::active()
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'like', '%' . $query . '%')
                        ->orWhere('brand', 'like', '%' . $query . '%');
            })
            ->when($category, function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->orderBy('name')
            ->paginate(12);

        return Inertia::render('products/search', [
            'products' => $products,
            'query' => $query,
            'category' => $category,
            'categories' => Product::active()->select('category')->distinct()->pluck('category'),
        ]);
    }
}