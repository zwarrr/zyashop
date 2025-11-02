<!-- Header & Category Tabs Component -->
<div id="headerContainer" class="sticky-header pb-4 pt-4 -mx-3 sm:-mx-4 px-3 sm:px-4">
  <!-- Header with Back & Share Button -->
  <div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-2">
      <a href="/" class="p-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-all duration-300 cursor-pointer" title="Back to home">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
      </a>
      <h1 class="text-xl sm:text-2xl font-bold">{{ $pageTitle ?? 'Products' }}</h1>
    </div>
    <button id="shareBtn" class="p-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-all duration-300 hover:scale-110 active:scale-95 cursor-pointer" title="Share this page" onclick="shareThisPage()">
      <img src="{{ asset('img/share.svg') }}" alt="Share" class="w-5 h-5">
    </button>
  </div>

  <!-- Search Bar -->
  <div class="mb-4">
    <input type="text" id="searchProducts" placeholder="Cari Produk" class="w-full px-4 py-3 bg-gray-200 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-black transition-all">
  </div>

  <!-- Category Tabs -->
  <div id="categoryTabs" class="flex justify-center gap-6 pb-4 border-b border-gray-200">
    <button class="category-tab-btn {{ !isset($productType) || $productType === 'shopee' ? 'active text-red-600 border-red-600' : 'text-gray-600 border-transparent' }} font-semibold text-sm whitespace-nowrap pb-2 border-b-2 hover:text-black transition-colors" 
            data-type="shopee" 
            onclick="window.location.href='{{ route('products.type', ['cardId' => $cardId ?? $card->id ?? '', 'type' => 'shopee']) }}'">
      Shopee
    </button>
    <button class="category-tab-btn {{ isset($productType) && $productType === 'tiktok' ? 'active text-red-600 border-red-600' : 'text-gray-600 border-transparent' }} font-semibold text-sm whitespace-nowrap pb-2 border-b-2 hover:text-black transition-colors" 
            data-type="tiktok"
            onclick="window.location.href='{{ route('products.type', ['cardId' => $cardId ?? $card->id ?? '', 'type' => 'tiktok']) }}'">
      Tiktok Shop
    </button>
  </div>
</div>

<script>
  function shareThisPage() {
    const pageTitle = document.title;
    const pageUrl = window.location.href;
    if (navigator.share) {
      navigator.share({title: pageTitle, text: 'Check out our amazing products!', url: pageUrl}).catch(err => console.log('Error sharing:', err));
    } else {
      const textToCopy = `${pageTitle}\n${pageUrl}`;
      navigator.clipboard.writeText(textToCopy).then(() => {
        alert('Link copied to clipboard!');
      }).catch(err => {
        console.log('Error copying to clipboard:', err);
        alert(`Share this link:\n${pageUrl}`);
      });
    }
  }
</script>