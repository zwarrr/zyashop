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

        $userProfile = $user->profile;
        $userLinks = $user->links()->orderBy('order')->get();
        $products = $user->products()->where('status', 'active')->get();
        
        // Eager load products untuk setiap card (untuk cek jumlah products)
        $cards = $user->cards()
                     ->where('status', 'active')
                     ->with(['products' => function($query) {
                         $query->where('status', '!=', 'inactive');
                     }])
                     ->get();
        
        return view('zyashp', [
            'userProfile' => $userProfile,
            'userLinks' => $userLinks,
            'products' => $products,
            'cards' => $cards
        ]);
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

        $products = $user->products()
                        ->where('status', '!=', 'coming_soon')
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
        $products = $user->cards()
                        ->where('category', $category)
                        ->where('status', 'active')
                        ->get()
                        ->map(function($card) {
                            // Add image_url for frontend display
                            if ($card->image && strpos($card->image, 'data:') === 0) {
                                $card->image_url = route('card.image', ['id' => $card->id]);
                            } else if ($card->image) {
                                $card->image_url = asset('storage/' . $card->image);
                            } else {
                                $card->image_url = null;
                            }
                            return $card;
                        });
        
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
        
        // Ambil products yang memiliki card_id ini
        $products = Product::where('card_id', $cardId)
                          ->where('status', '!=', 'inactive')
                          ->get();
        
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
                          ->get();
        
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

