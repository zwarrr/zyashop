<!DOCTYPE html>
<html lang="en" class="bg-white">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cards - Zya's Placeshop</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Poppins Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .card-item {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-item:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .card-item:active {
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

    <!-- Header Section -->
    <div class="sticky-header pb-4 pt-4">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <a href="/" class="p-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-all duration-300 cursor-pointer" title="Kembali ke home">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
          </a>
          <h1 class="text-xl sm:text-2xl font-bold">Kategori: {{ $category }}</h1>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="mb-4">
        <input 
          type="text" 
          id="searchCards" 
          placeholder="Cari Cards" 
          class="w-full px-4 py-3 bg-gray-200 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-black transition-all"
        >
      </div>
    </div>

    <!-- Cards Grid -->
    <section class="grid grid-cols-2 gap-3 sm:gap-6 mb-20">
      @forelse($cards as $card)
      <!-- Card Item -->
      <div class="card-item rounded-lg overflow-hidden border border-gray-200 hover:border-black transition-colors" data-card="{{ $card->id }}">
        <div class="relative bg-gray-300 overflow-hidden" style="aspect-ratio: 1;">
          <!-- Placeholder while image loads -->
          <img 
            src="https://placehold.co/1080x1080?text={{ urlencode($card->title) }}" 
            alt="{{ $card->title }}" 
            class="w-full h-full object-cover"
            loading="lazy"
            onerror="this.onerror=null; this.src='https://placehold.co/1080x1080?text={{ urlencode($card->title) }}';"
          >
          <div class="absolute top-2 right-2 bg-black text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
            {{ $card->status === 'active' ? 'Tersedia' : 'Tidak Tersedia' }}
          </div>
        </div>
        <div class="p-3 sm:p-4">
          <h3 class="font-bold text-sm sm:text-base line-clamp-2">{{ $card->title }}</h3>
          @if($card->description)
          <p class="text-gray-600 text-xs sm:text-sm mt-1 line-clamp-2">{{ $card->description }}</p>
          @endif
        </div>
      </div>
      @empty
      <!-- No Cards Found -->
      <div class="col-span-2 text-center py-12">
        <p class="text-gray-600">Belum ada cards dalam kategori ini.</p>
        <a href="/" class="inline-block mt-4 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition">
          Kembali ke Home
        </a>
      </div>
      @endforelse
    </section>

    <!-- Pagination -->
    @if($cards->hasPages())
    <div class="flex justify-center gap-2 mb-8">
      @if($cards->onFirstPage())
      <span class="px-3 py-2 rounded bg-gray-200 text-gray-500 text-sm">← Sebelumnya</span>
      @else
      <a href="{{ $cards->previousPageUrl() }}" class="px-3 py-2 rounded bg-black text-white hover:bg-gray-800 transition text-sm">← Sebelumnya</a>
      @endif

      @foreach($cards->getUrlRange(1, $cards->lastPage()) as $page => $url)
        @if($page == $cards->currentPage())
        <span class="px-3 py-2 rounded bg-black text-white text-sm font-medium">{{ $page }}</span>
        @else
        <a href="{{ $url }}" class="px-3 py-2 rounded bg-gray-200 hover:bg-gray-300 transition text-sm">{{ $page }}</a>
        @endif
      @endforeach

      @if($cards->hasMorePages())
      <a href="{{ $cards->nextPageUrl() }}" class="px-3 py-2 rounded bg-black text-white hover:bg-gray-800 transition text-sm">Selanjutnya →</a>
      @else
      <span class="px-3 py-2 rounded bg-gray-200 text-gray-500 text-sm">Selanjutnya →</span>
      @endif
    </div>
    @endif

  </main>

  <script>
    // Search functionality
    document.getElementById('searchCards').addEventListener('input', (e) => {
      const searchText = e.target.value.toLowerCase();
      const cards = document.querySelectorAll('[data-card]');
      
      cards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        card.style.display = title.includes(searchText) ? '' : 'none';
      });
    });

    function shareThisPage() {
      const pageTitle = document.title;
      const pageUrl = window.location.href;
      
      if (navigator.share) {
        navigator.share({
          title: pageTitle,
          text: 'Check out this amazing content!',
          url: pageUrl
        }).catch(err => console.log('Error sharing:', err));
      } else {
        navigator.clipboard.writeText(`${pageTitle}\n${pageUrl}`).then(() => {
          alert('Link copied to clipboard!');
        }).catch(err => {
          alert(`Share this link:\n${pageUrl}`);
        });
      }
    }
  </script>

</body>
</html>
