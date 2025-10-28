<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductAdminController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of products (Admin)
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // If AJAX request, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            $products = $user->products()->get();
            return response()->json(['products' => $products]);
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
        try {
            $validated = $request->validate([
            'title' => 'required|string|max:255',
            'card_id' => 'required|exists:cards,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'status' => 'required|in:active,inactive,coming_soon',
            'link_shopee' => 'nullable|url',
            'link_tiktok' => 'nullable|url',
        ]);            // Validasi: Minimal salah satu link harus diisi
            if (empty($validated['link_shopee']) && empty($validated['link_tiktok'])) {
                return response()->json([
                    'error' => 'Minimal salah satu link (Shopee atau Tiktok) harus diisi!'
                ], 422);
            }

            $validated['user_id'] = auth()->id();
            
            // Handle image upload - store as base64 for Vercel compatibility
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                \Log::info('Store - Product image upload detected', [
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType()
                ]);
                
                // Check if image is valid
                if (!$image->isValid()) {
                    return response()->json(['error' => 'File gambar tidak valid'], 422);
                }
                
                // Store image in base64 format in database for Vercel compatibility
                $imageContent = file_get_contents($image->getRealPath());
                $base64Image = base64_encode($imageContent);
                $mimeType = $image->getMimeType();
                
                $validated['image_url'] = 'data:' . $mimeType . ';base64,' . $base64Image;
                
                \Log::info('Store - Product image converted to base64', [
                    'base64_length' => strlen($validated['image_url']),
                    'mime_type' => $mimeType
                ]);
            }
            
            $product = Product::create($validated);
            
            \Log::info('Product created successfully', [
                'product_id' => $product->id,
                'has_image_url' => !empty($product->image_url),
                'image_url_length' => $product->image_url ? strlen($product->image_url) : 0,
                'image_url_starts_with' => $product->image_url ? substr($product->image_url, 0, 30) : 'null'
            ]);
            
            return response()->json([
                'success' => 'Produk berhasil ditambahkan!',
                'product' => $product
            ], 200);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422)->header('Content-Type', 'application/json');
        } catch (\Exception $e) {
            \Log::error('Product store error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500)->header('Content-Type', 'application/json');
        }
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
        
        // Debug: Log semua data yang diterima
        \Log::info('Update - Request received', [
            'product_id' => $id,
            'has_file' => $request->hasFile('image'),
            'all_files' => $request->allFiles(),
            'all_input' => $request->except(['image']),
            'content_type' => $request->header('Content-Type')
        ]);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'card_id' => 'required|exists:cards,id',
            'description' => 'nullable|string',
            'image' => 'nullable|file|max:10240', // Simplified validation - just check if it's a file
            'status' => 'required|in:active,inactive,coming_soon',
            'link_shopee' => 'nullable|url',
            'link_tiktok' => 'nullable|url',
        ]);

        // Validasi: Minimal salah satu link harus diisi
        if (empty($validated['link_shopee']) && empty($validated['link_tiktok'])) {
            return response()->json([
                'error' => 'Minimal salah satu link (Shopee atau Tiktok) harus diisi!'
            ], 422);
        }

        // Handle image upload with custom filename
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            
            \Log::info('Update - Product image upload detected', [
                'original_name' => $image->getClientOriginalName(),
                'size' => $image->getSize(),
                'mime' => $image->getMimeType()
            ]);
            
            // Store image in base64 format in database for Vercel compatibility
            $imageContent = file_get_contents($image->getRealPath());
            $base64Image = base64_encode($imageContent);
            $mimeType = $image->getMimeType();
            
            $validated['image_url'] = 'data:' . $mimeType . ';base64,' . $base64Image;
            
            \Log::info('Update - Product image converted to base64', [
                'base64_length' => strlen($validated['image_url']),
                'mime_type' => $mimeType
            ]);
        }

        $product->update($validated);
        
        // Reload product to get fresh data from database
        $product->refresh();
        
        \Log::info('Product updated successfully', [
            'product_id' => $product->id,
            'has_image_url' => !empty($product->image_url),
            'image_url_length' => $product->image_url ? strlen($product->image_url) : 0,
            'image_url_starts_with' => $product->image_url ? substr($product->image_url, 0, 50) : 'null',
            'title' => $product->title
        ]);

        return response()->json(['success' => 'Produk berhasil diperbarui!', 'product' => $product])
            ->header('Content-Type', 'application/json');
    }

    /**
     * Delete the specified product
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        
        $product->delete();

        return response()->json(['success' => 'Produk berhasil dihapus!'], 200)
            ->header('Content-Type', 'application/json');
    }
}


