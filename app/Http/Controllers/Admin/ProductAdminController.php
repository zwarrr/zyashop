<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductAdminController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of products (Admin)
     */
    public function index()
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $products = $user->products()->paginate(10);
        $cards = $user->cards()->where('status', 'active')->get();
        
        return view('admin.produk', [
            'products' => $products,
            'cards' => $cards
        ]);
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $user = auth()->user();
        $products = $user ? $user->products()->paginate(10) : collect([]);
        $cards = $user ? $user->cards()->where('status', 'active')->get() : collect([]);

        return view('admin.produk', [
            'mode' => 'create',
            'product' => null,
            'products' => $products,
            'cards' => $cards
        ]);
    }

    /**
     * Store a newly created product in database
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'card_id' => 'required|exists:cards,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'status' => 'required|in:active,inactive,coming_soon',
            'link_shopee' => 'nullable|url',
            'link_tiktok' => 'nullable|url',
        ]);

        $validated['user_id'] = auth()->id();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }
        
        Product::create($validated);

        return response()->json(['success' => 'Produk berhasil ditambahkan!'], 200);
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        
        // Check if user owns this product
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $user = auth()->user();
        $cards = $user ? $user->cards()->where('status', 'active')->get() : collect([]);
        
        // Return JSON for AJAX modal
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'card_id' => $product->card_id,
                    'status' => $product->status,
                    'description' => $product->description,
                    'link_shopee' => $product->link_shopee,
                    'link_tiktok' => $product->link_tiktok,
                    'image_url' => $product->image_url,
                ],
                'cards' => $cards
            ]);
        }
        
        $products = $user ? $user->products()->paginate(10) : collect([]);

        return view('admin.produk', [
            'mode' => 'edit',
            'product' => $product,
            'products' => $products,
            'cards' => $cards
        ]);
    }

    /**
     * Update the specified product in database
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        // Check if user owns this product
        if ($product->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'card_id' => 'required|exists:cards,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'status' => 'required|in:active,inactive,coming_soon',
            'link_shopee' => 'nullable|url',
            'link_tiktok' => 'nullable|url',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $product->update($validated);

        return response()->json(['success' => 'Produk berhasil diperbarui!'], 200);
    }

    /**
     * Delete the specified product
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        $product->delete();

        return response()->json(['success' => 'Produk berhasil dihapus!'], 200);
    }
}

