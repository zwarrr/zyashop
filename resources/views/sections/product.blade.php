<!DOCTYPE html>
<html lang="en" class="bg-white">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ isset($pageTitle) ? $pageTitle : (isset($cardTitle) ? $cardTitle : 'Products') }} - Zya's Placeshop</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Poppins Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .product-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .product-card:active {
      transform: scale(0.98);
    }
    .sticky-header {
      position: sticky;
      top: 0;
      background-color: white;
      z-index: 10;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
  </style>
</head>
<body class="bg-white text-black">

  <!-- Main Wrapper -->
  <main class="max-w-full sm:max-w-2xl md:max-w-4xl mx-auto px-3 sm:px-4">

    <!-- Include Header Component -->
    @include('partials.header', [
      'pageTitle' => $pageTitle ?? ($cardTitle ?? 'Products'),
      'productType' => $productType ?? null
    ])

    <!-- Products Grid -->
    <section class="grid grid-cols-2 gap-3 sm:gap-6 mb-20">
      
      @forelse($products as $product)
      <!-- Product/Card Item -->
      @if(isset($isCards) && $isCards)
        <!-- Card Item (from cards table) -->
        <a href="javascript:void(0)" class="product-card rounded-lg overflow-hidden border border-gray-200 hover:border-black transition-colors" data-product="{{ $product->id }}">
          <div class="relative bg-gray-300 overflow-hidden" style="aspect-ratio: 1;">
            @if($product->image)
              <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" class="w-full h-full object-cover">
            @else
              <img src="https://placehold.co/1080x1080?text={{ urlencode($product->title) }}" alt="{{ $product->title }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute top-2 right-2 bg-black text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
              {{ $product->status === 'active' ? 'Tersedia' : 'Tidak Tersedia' }}
            </div>
          </div>
          <div class="p-2 sm:p-4">
            <h3 class="font-bold text-sm sm:text-lg mb-1">{{ $product->title }}</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-2 line-clamp-2">{{ $product->description ?? '-' }}</p>
            <div class="flex items-center justify-between">
              <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->category }}</span>
            </div>
          </div>
        </a>
      @else
        <!-- Product Item (from products table) -->
        @php
          // Tentukan link berdasarkan prioritas atau type
          $productLink = '#';
          if (isset($productType)) {
            // Jika dari filter type (shopee/tiktok)
            $productLink = $productType === 'shopee' ? ($product->link_shopee ?? '#') : ($product->link_tiktok ?? '#');
          } else {
            // Default: gunakan link yang tersedia (prioritas shopee)
            $productLink = $product->link_shopee ?? $product->link_tiktok ?? '#';
          }
        @endphp
        <a href="{{ $productLink }}" 
           target="_blank"
           rel="noopener noreferrer"
           class="product-card rounded-lg overflow-hidden border border-gray-200 hover:border-black transition-colors" 
           data-product="{{ $product->id }}">
          <div class="relative bg-gray-300 overflow-hidden" style="aspect-ratio: 1;">
            @if($product->image)
              <img src="{{ $product->image }}" alt="{{ $product->title }}" class="w-full h-full object-cover">
            @else
              <img src="https://placehold.co/400x400?text={{ urlencode($product->title) }}" alt="{{ $product->title }}" class="w-full h-full object-cover">
            @endif
            <div class="absolute top-2 right-2 bg-black text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
              @if($product->status === 'coming_soon')
                Coming Soon
              @elseif($product->status === 'active')
                Available
              @else
                Inactive
              @endif
            </div>
          </div>
          <div class="p-2 sm:p-4">
            <h3 class="font-bold text-sm sm:text-lg mb-1">{{ $product->title }}</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-2 line-clamp-2">{{ $product->description ?? 'Premium digital content' }}</p>
            <div class="flex items-center justify-between">
              @if($product->card)
              <span class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->card->title }}</span>
              @else
              <span class="text-xs bg-gray-100 px-2 py-1 rounded">No Card</span>
              @endif
              
              @if(isset($productType))
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
              </svg>
              @endif
            </div>
          </div>
        </a>
      @endif
      @empty
      <div class="col-span-2 text-center py-20">
        <p class="text-gray-500 text-lg">Belum ada {{ isset($isCards) && $isCards ? 'cards' : 'produk' }} tersedia</p>
        <a href="/" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">Kembali ke Home</a>
      </div>
      @endforelse

    </section>

  </main>

  <!-- Load Coming Soon Modal (jika card tidak punya produk) -->
  @include('partials.modal_information_product_coomingsoon')

  <script>
    // Cek apakah card tidak punya produk, jika iya tampilkan modal
    @if(isset($hasNoProducts) && $hasNoProducts)
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        const modal = document.getElementById('comingSoonModal');
        const cardTitle = document.getElementById('productRange');
        
        if (modal && cardTitle) {
          cardTitle.textContent = '{{ $cardTitle ?? "This Card" }}';
          modal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        }
      }, 500);
    });
    @endif

    function closeComingSoonModal() {
      const modal = document.getElementById('comingSoonModal');
      if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        // Redirect ke home setelah modal ditutup
        window.location.href = '/';
      }
    }

    // Search functionality
    const searchInput = document.getElementById('searchProducts');
    const productCards = document.querySelectorAll('[data-product]');

    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        productCards.forEach(card => {
          const title = card.querySelector('h3');
          const description = card.querySelector('p');
          if (title && description) {
            if (title.textContent.toLowerCase().includes(searchTerm) || description.textContent.toLowerCase().includes(searchTerm)) {
              card.style.display = '';
            } else {
              card.style.display = 'none';
            }
          }
        });
      });
    }
  </script>

</body>
</html>
