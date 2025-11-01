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
            // Ambil user pertama atau user yang login
            $user = auth()->user() ?? User::first();
            
            if (!$user) {
                return view('zyashp', [
                    'userProfile' => null,
                    'userLinks' => [],
                    'products' => [],
                    'cards' => []
                ]);
            }

            // EXCLUDE profile_image from userProfile to avoid payload too large
            $userProfile = $user->profile;
            if ($userProfile) {
                $userProfile->makeHidden('profile_image');
            }
            
            // Only select necessary fields from links
            $userLinks = $user->links()
                             ->select('id', 'user_id', 'title', 'url', 'order')
                             ->orderBy('order')
                             ->get();
            
            // EXCLUDE image from home view to avoid payload too large error
            $products = $user->products()
                            ->where('status', 'active')
                            ->select('id', 'user_id', 'card_id', 'title', 'description', 'link_shopee', 'link_tiktok', 'price', 'range', 'stock', 'status', 'specifications', 'created_at', 'updated_at')
                            ->get();
            
            // Eager load products untuk setiap card
            // EXCLUDE image from cards to reduce payload
            $cards = $user->cards()
                         ->where('status', 'active')
                         ->select('id', 'user_id', 'title', 'category', 'slug', 'status', 'created_at', 'updated_at')
                         ->with(['products' => function($query) {
                             $query->where('status', '!=', 'inactive')
                                   ->select('id', 'user_id', 'card_id', 'title', 'description', 'link_shopee', 'link_tiktok', 'price', 'range', 'stock', 'status', 'specifications', 'created_at', 'updated_at');
                         }])
                         ->get();
            
            return view('zyashp', [
                'userProfile' => $userProfile,
                'userLinks' => $userLinks,
                'products' => $products,
                'cards' => $cards
            ]);
        } catch (\Exception $e) {
            \Log::error('Home page error: ' . $e->getMessage());
            return view('zyashp', [
                'userProfile' => null,
                'userLinks' => [],
                'products' => [],
                'cards' => []
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

