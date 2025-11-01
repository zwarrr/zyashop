<?php

namespace App\Http\Controllers\Users;

use App\Models\Product;
use App\Models\Card;
use App\Models\UserProfile;
use App\Models\UserLink;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Tampilkan halaman home dengan profile dan products
     */
    public function home()
    {
        try {
            // Get user
            $user = auth()->check() ? auth()->user() : User::first();
            
            \Log::info('DEBUG: User ID = ' . ($user ? $user->id : 'null'));
            
            if (!$user) {
                return view('zyashp', [
                    'userProfile' => null,
                    'userLinks' => collect(),
                    'products' => collect(),
                    'cards' => collect()
                ]);
            }

            // Simple fallback data
            $userProfile = null;
            $userLinks = collect();
            $products = collect();
            $cards = collect();

            // 1. Get profile
            try {
                $userProfile = $user->profile;
                if ($userProfile) {
                    $userProfile->makeHidden('profile_image');
                }
                \Log::info('DEBUG: Profile loaded: ' . ($userProfile ? 'yes' : 'no'));
            } catch (\Throwable $e) {
                \Log::error('ERROR fetching profile: ' . $e->getMessage());
            }

            // 2. Get links
            try {
                $userLinks = $user->links()
                                 ->orderBy('order')
                                 ->get(['id', 'user_id', 'title', 'url', 'order']);
                \Log::info('DEBUG: Links count = ' . $userLinks->count());
            } catch (\Throwable $e) {
                \Log::error('ERROR fetching links: ' . $e->getMessage());
            }

            // 3. Get products  
            try {
                $products = $user->products()
                                ->where('status', 'active')
                                ->get(['id', 'user_id', 'card_id', 'title', 'description', 'link_shopee', 'link_tiktok', 'price', 'range', 'stock', 'status', 'specifications', 'created_at', 'updated_at']);
                \Log::info('DEBUG: Active products count = ' . $products->count());
            } catch (\Throwable $e) {
                \Log::error('ERROR fetching products: ' . $e->getMessage());
            }

            // 4. Get cards with products
            try {
                $allCards = $user->cards()->get();
                \Log::info('DEBUG: Total cards = ' . $allCards->count());
                \Log::info('DEBUG: Active cards = ' . $user->cards()->where('status', 'active')->count());
                
                $cards = $user->cards()
                             ->where('status', 'active')
                             ->with('products')
                             ->get(['id', 'user_id', 'title', 'category', 'slug', 'status', 'created_at', 'updated_at']);
                \Log::info('DEBUG: Final cards loaded = ' . $cards->count());
            } catch (\Throwable $e) {
                \Log::error('ERROR fetching cards: ' . $e->getMessage());
            }
            
            return view('zyashp', compact('userProfile', 'userLinks', 'products', 'cards'));
        } catch (\Throwable $e) {
            \Log::error('FATAL ERROR in home: ' . $e->getMessage());
            return view('zyashp', [
                'userProfile' => null,
                'userLinks' => collect(),
                'products' => collect(),
                'cards' => collect()
            ]);
        }
    }

    /**
     * Tampilkan halaman product listing
     */
    public function index()
    {
        $user = auth()->user() ?? User::first();
        
        if (!$user) {
            return redirect()->route('home');
        }

        // EXCLUDE image to avoid payload too large error
        $products = $user->products()
                        ->where('status', '!=', 'coming_soon')
                        ->select('id', 'user_id', 'card_id', 'title', 'description', 'link_shopee', 'link_tiktok', 'price', 'range', 'stock', 'status', 'specifications', 'created_at', 'updated_at')
                        ->paginate(12);
        
        $userProfile = $user->profile;
        
        return view('sections.product', [
            'products' => $products,
            'userProfile' => $userProfile
        ]);
    }

    /**
     * Tampilkan cards berdasarkan kategori (menggunakan product.blade template)
     */
    public function showCards($category)
    {
        $user = auth()->user() ?? User::first();
        
        if (!$user) {
            return redirect()->route('home');
        }

        // Cari cards berdasarkan kategori (cards field)
        // EXCLUDE image to avoid payload too large error
        $products = $user->cards()
                        ->where('category', $category)
                        ->where('status', 'active')
                        ->select('id', 'user_id', 'title', 'category', 'slug', 'status', 'created_at', 'updated_at')
                        ->get();
        
        // Convert to paginator for compatibility
        $perPage = 12;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $itemsForCurrentPage = $products->slice($offset, $perPage)->values();
        
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $products->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        $userProfile = $user->profile;
        
        // Render sections/product.blade dengan data cards (variable diberi nama 'products' agar compatible dengan product.blade)
        return view('sections.product', [
            'products' => $products,
            'userProfile' => $userProfile,
            'isCards' => true  // Flag untuk product.blade agar tahu ini cards, bukan products
        ]);
    }

    /**
     * Tampilkan products berdasarkan card_id (relasi card -> products)
     */
    public function showProductsByCard($cardId)
    {
        $user = auth()->user() ?? User::first();
        
        if (!$user) {
            return redirect()->route('home');
        }

        // Cari card by ID
        $card = Card::findOrFail($cardId);
        
        // Ambil products dengan EXPLICIT select untuk include image
        $products = Product::where('card_id', $cardId)
                          ->where('status', '!=', 'inactive')
                          ->select('id', 'user_id', 'card_id', 'title', 'description', 'image', 'link_shopee', 'link_tiktok', 'price', 'range', 'stock', 'status', 'specifications', 'created_at', 'updated_at')
                          ->get();
        
        \Log::info('showProductsByCard - products fetched with explicit select', [
            'count' => $products->count(),
            'first_product_has_image' => $products->count() > 0 && !empty($products[0]->image),
            'first_product_image_length' => $products->count() > 0 ? strlen($products[0]->image ?? '') : 0
        ]);
        
        // Jika tidak ada products, kirim flag hasNoProducts
        $hasNoProducts = $products->isEmpty();
        
        $userProfile = $user->profile;
        
        // Render sections/product.blade dengan data products dari card
        return view('sections.product', [
            'products' => $products,
            'userProfile' => $userProfile,
            'isCards' => false,  // Ini adalah products, bukan cards
            'cardTitle' => $card->title,  // Kirim judul card untuk header
            'hasNoProducts' => $hasNoProducts,  // Flag untuk menampilkan modal
            'cardInfo' => $card  // Kirim info card untuk modal
        ]);
    }

    /**
     * Tampilkan products berdasarkan type (shopee/tiktok)
     */
    public function showProductsByType($type)
    {
        $user = auth()->user() ?? User::first();
        
        if (!$user) {
            return redirect()->route('home');
        }

        // Validasi type
        if (!in_array($type, ['shopee', 'tiktok'])) {
            return redirect()->route('home');
        }

        // Filter products berdasarkan link yang ada dan user
        $linkField = $type === 'shopee' ? 'link_shopee' : 'link_tiktok';
        
        $products = $user->products()
                          ->where('status', '!=', 'inactive')
                          ->whereNotNull($linkField)
                          ->where($linkField, '!=', '')
                          ->select('id', 'user_id', 'card_id', 'title', 'description', 'image', 'link_shopee', 'link_tiktok', 'price', 'range', 'stock', 'status', 'specifications', 'created_at', 'updated_at')
                          ->get();
        
        // Make image visible untuk public view - iterate directly
        foreach ($products as $product) {
            $product->makeVisible('image');
        }
        
        $userProfile = $user->profile;
        
        // Render sections/product.blade dengan data products by type
        return view('sections.product', [
            'products' => $products,
            'userProfile' => $userProfile,
            'isCards' => false,
            'productType' => $type,  // shopee atau tiktok
            'pageTitle' => $type === 'shopee' ? 'Shopee Products' : 'Tiktok Shop Products'
        ]);
    }
}

