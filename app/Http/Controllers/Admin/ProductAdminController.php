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
            ]);

            // Validasi: Minimal salah satu link harus diisi
            if (empty($validated['link_shopee']) && empty($validated['link_tiktok'])) {
                return response()->json([
                    'error' => 'Minimal salah satu link (Shopee atau Tiktok) harus diisi!'
                ], 422);
            }

            $validated['user_id'] = auth()->id();
            
            // Handle image upload with custom filename
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                
                // Check if image is valid
                if (!$image->isValid()) {
                    return response()->json(['error' => 'File gambar tidak valid'], 422);
                }
                
                // Ensure products directory exists - use /tmp in production
                $basePath = app()->environment('production') ? '/tmp/storage' : storage_path('app/public');
                $productsDir = $basePath . '/products';
                if (!file_exists($productsDir)) {
                    if (!mkdir($productsDir, 0755, true)) {
                        return response()->json(['error' => 'Tidak dapat membuat direktori untuk menyimpan gambar'], 500);
                    }
                }
                
                $slug = Str::slug($validated['title']);
                $extension = $image->getClientOriginalExtension();
                $filename = 'product-' . $slug . '.' . $extension;
                
                // Check if file exists, add counter if needed
                $counter = 1;
                while (\Storage::disk('public')->exists('products/' . $filename)) {
                    $filename = 'product-' . $slug . '-' . $counter . '.' . $extension;
                    $counter++;
                }
                
                $path = $image->storeAs('products', $filename, 'public');
                $validated['image_url'] = $path; // Store as "products/filename.ext"
            }
            
            $product = Product::create($validated);
            
            // Add full image URL to response
            $productData = $product->toArray();
            if ($product->image_url) {
                $productData['image_url_full'] = asset('storage/' . $product->image_url);
            }

            return response()->json([
                'success' => 'Produk berhasil ditambahkan!',
                'product' => $productData
            ], 200)->header('Content-Type', 'application/json');
            
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

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'card_id' => 'required|exists:cards,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
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
            // Delete old image if exists
            if ($product->image_url) {
                $oldPath = str_replace('/storage/', '', $product->image_url);
                \Storage::disk('public')->delete($oldPath);
            }
            
            $image = $request->file('image');
            $slug = Str::slug($validated['title']);
            $extension = $image->getClientOriginalExtension();
            $filename = 'product-' . $slug . '.' . $extension;
            
            // Check if file exists, add counter if needed
            $counter = 1;
            $oldFilename = $product->image_url ? basename(str_replace('/storage/', '', $product->image_url)) : '';
            while (\Storage::disk('public')->exists('products/' . $filename) && $filename !== $oldFilename) {
                $filename = 'product-' . $slug . '-' . $counter . '.' . $extension;
                $counter++;
            }
            
            $path = $image->storeAs('products', $filename, 'public');
            $validated['image_url'] = '/storage/' . $path;
        }

        $product->update($validated);

        return response()->json(['success' => 'Produk berhasil diperbarui!'], 200)
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


