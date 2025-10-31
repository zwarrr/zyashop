<!DOCTYPE html>
<html lang="en" class="bg-white">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Zya's Placeshop</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Poppins Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    
    /* Modal Overlay - Shared by both modals */
    .modal-overlay {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 50;
      animation: fadeIn 0.3s ease;
    }
    .modal-overlay.hidden {
      display: none;
    }
    
    /* Coming Soon Modal Styling */
    #comingSoonModal {
      align-items: center;
    }
    #comingSoonModal .modal-content {
      background-color: white;
      border-radius: 12px;
      padding: 32px 24px;
      max-width: 400px;
      width: 90%;
      text-align: center;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
      animation: slideUp 0.3s ease;
    }
    
    /* Links Modal Styling */
    #linksModal {
      align-items: flex-end;
    }
    #linksModal .modal-content {
      background-color: white;
      color: black;
      width: 100%;
      max-width: 500px;
      border-radius: 20px 20px 0 0;
      padding: 28px 24px 24px 24px;
      box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3);
      animation: slideUp 0.3s ease;
      max-height: 80vh;
      overflow-y: auto;
      position: relative;
    }
    
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }
    @keyframes slideUp {
      from {
        transform: translateY(100%);
      }
      to {
        transform: translateY(0);
      }
    }
    .close-btn {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      border-radius: 50%;
      background-color: rgba(0, 0, 0, 0.05);
      border: 1px solid rgba(0, 0, 0, 0.1);
      transition: all 0.2s ease;
      color: black;
      padding: 0;
      flex-shrink: 0;
    }
    .close-btn:hover {
      background-color: rgba(0, 0, 0, 0.15);
      border-color: rgba(0, 0, 0, 0.2);
    }
    .link-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 0;
      border-bottom: 1px solid rgba(0, 0, 0, 0.1);
      text-decoration: none;
      color: black;
      transition: opacity 0.2s ease;
    }
    .link-item:last-child {
      border-bottom: none;
    }
    .link-item:hover {
      opacity: 0.7;
    }
    .link-item:active {
      opacity: 0.5;
    }
    .link-icon {
      width: 20px;
      height: 20px;
      flex-shrink: 0;
    }
    .link-text {
      flex: 1;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      font-size: 14px;
      font-weight: 500;
    }
    
    /* Main link styling */
    .main-external-link {
      display: inline-flex !important;
    }
  </style>
</head>
<body class="bg-white text-black">

  @include('partials.ajax_loader')

  <!-- Main Wrapper -->
  <main class="max-w-md mx-auto px-4 pt-4">

    <!-- Share Icon -->
    <div class="flex justify-end mb-6">
      <button 
        id="shareBtn"
        class="p-2 rounded-lg hover:bg-gray-100 active:bg-gray-200 transition-all duration-300 hover:scale-110 hover:brightness-75 active:scale-95 cursor-pointer"
        title="Share this page"
        onclick="shareThisPage()"
      >
        <img src="{{ asset('img/share.svg') }}" alt="Share" class="w-5 h-5">
      </button>
    </div>

    <!-- Profile Section -->
    <section class="text-center mb-6">
      @php
        $profileImage = $userProfile?->profile_image;
        $imageSrc = 'https://i.pinimg.com/736x/78/0d/08/780d084f353d666f61a0067dbf48bfdd.jpg';
        
        if (!empty($profileImage)) {
          // Check if it's base64 data
          if (strpos($profileImage, 'data:') === 0) {
            $imageSrc = $profileImage;
          } else if (strpos($profileImage, '/storage/') === 0) {
            // Old file path format
            $imageSrc = asset($profileImage);
          }
        }
      @endphp
      <img src="{{ $imageSrc }}" alt="Profile" class="w-28 h-28 mx-auto rounded-full bg-gray-300 mb-4" />

      <h1 class="text-[20px] font-bold leading-tight">{{ $userProfile?->display_name ?? 'User' }}</h1>
      <p class="text-gray-700 text-sm">
  {{ '@' . ($userProfile?->username ?? 'thezyshop') }}
  @if($userProfile?->verified_badge === 'yes')
  <img src="{{ asset('img/verift.svg') }}" alt="Verified" class="inline w-4 h-4 ml-1 mb-[2px]">
  @endif
      </p>
      <p class="text-gray-500 text-sm mt-[6px]">{{ $userProfile?->bio ?? 'We sell digital content packages daily!' }}</p>

      <!-- External Links Container -->
      @if($userLinks && $userLinks->count() > 0)
      <div class="mt-3 flex items-center justify-center">
        <!-- Main Link Button (with "and X more" text) -->
        <button id="mainLinkBtn" class="main-external-link flex items-center gap-1 px-3 py-2 rounded-lg text-sm font-medium text-black transition-colors border-none bg-transparent cursor-pointer" style="display: none;">
          <img src="{{ asset('img/link.svg') }}" alt="Link" class="w-4 h-4 flex-shrink-0" />
          <span class="flex items-center gap-0.5 whitespace-nowrap">
            <span class="link-display">{{ $userLinks->first()->url }}</span>
            <span id="moreLinksIndicator" class="text-black"></span>
          </span>
        </button>
      </div>
      
      <!-- Hidden container with all links data -->
      <div id="allLinksContainer" style="display: none;">
        @foreach($userLinks as $link)
        <a href="{{ $link->url }}" class="external-link">{{ $link->title }}</a>
        @endforeach
      </div>
      @endif
    </section>

    <!-- Divider Line -->
    <div class="border-t border-black pb-10"></div>

    <!-- Grid Cards - Fully Dynamic from Database -->
    @if($cards->count() > 0)
    <section class="grid grid-cols-2 gap-x-4 gap-y-5 mb-20">
      @foreach($cards as $card)
      <!-- Dynamic Card from Database -->
      <a href="javascript:void(0)" 
         onclick="handleCardClick({{ $card->id }}, '{{ $card->title }}', {{ $card->products()->where('status', '!=', 'inactive')->count() }})" 
         class="card-link relative rounded-lg overflow-hidden group cursor-pointer" 
         data-card-id="{{ $card->id }}" 
         data-has-products="{{ $card->products()->where('status', '!=', 'inactive')->count() > 0 ? 'true' : 'false' }}"
         title="{{ $card->title }}">
        @php
          // Get image from card object (now included in query)
          $cardImage = $card->image ?? null;
          $cardImageSrc = 'https://placehold.co/1080x1080?text=' . urlencode($card->title);
          
          if (!empty($cardImage)) {
            if (strpos($cardImage, 'data:') === 0) {
              // Base64 image
              $cardImageSrc = $cardImage;
            } else {
              // File path
              $cardImageSrc = asset('storage/' . $cardImage);
            }
          }
        @endphp
        <img src="{{ $cardImageSrc }}" 
             alt="{{ $card->title }}" 
             class="w-full h-full object-cover"
             onerror="this.src='https://placehold.co/1080x1080?text={{ urlencode($card->title) }}'">
        <div class="absolute bottom-0 w-full bg-black/80 text-white text-xs text-center py-2 group-hover:bg-black/90 transition-all">{{ $card->title }}</div>
      </a>
      @endforeach
    </section>
    @else
    <!-- No Cards Available -->
    <section class="text-center py-12 mb-20">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-3 text-gray-400">
        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
        <line x1="9" y1="9" x2="15" y2="9"></line>
        <line x1="9" y1="15" x2="15" y2="15"></line>
      </svg>
      <p class="text-gray-600 text-sm">Belum ada cards tersedia</p>
    </section>
    @endif

  </main>

  <!-- Load Coming Soon Modal -->
  @include('partials.modal_information_product_coomingsoon')
  
  <!-- Load Links Modal -->
  @include('partials.modal_link_profile')

  <script>
    let linksModalReady = false;
    let moreLinksExpanded = false;

    // Handle card click - cek apakah card punya products
    function handleCardClick(cardId, cardTitle, productsCount) {
      if (productsCount > 0) {
        // Ada products, redirect ke halaman product
        window.location.href = `/card/${cardId}/products`;
      } else {
        // Tidak ada products, tampilkan modal di halaman ini
        openComingSoonModal(cardTitle);
      }
    }

    // Open coming soon modal
    function openComingSoonModal(cardTitle) {
      const modal = document.getElementById('comingSoonModal');
      const rangeElement = document.getElementById('productRange');
      
      if (rangeElement) {
        rangeElement.textContent = cardTitle;
      }
      
      if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
      }
    }

    // Close coming soon modal
    function closeComingSoonModal() {
      const modal = document.getElementById('comingSoonModal');
      if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
    }

    // Get all external links from HTML
    function getAllLinks() {
      const container = document.getElementById('allLinksContainer');
      const links = [];
      
      if (container) {
        const linkElements = container.querySelectorAll('a.external-link');
        linkElements.forEach(el => {
          links.push({
            url: el.href,
            text: el.textContent.trim()
          });
        });
      }
      
      return links;
    }

    // Toggle more links visibility
    function toggleMoreLinks() {
      console.log('toggleMoreLinks called, linksModalReady:', linksModalReady);
      
      if (!linksModalReady) {
        console.log('Modal not ready yet, waiting...');
        setTimeout(() => toggleMoreLinks(), 100);
        return;
      }
      
      const allLinks = getAllLinks();
      console.log('Opening links modal with:', allLinks);
      
      // Open links modal
      if (typeof openLinksModal === 'function') {
        openLinksModal(allLinks);
        console.log('✓ openLinksModal called');
      } else {
        console.error('✗ openLinksModal is not a function!');
      }
    }

    // Initialize more links visibility
    function initializeMoreLinks() {
      console.log('Initializing more links...');
      
      const allLinks = getAllLinks();
      console.log('All links count:', allLinks.length);
      
      // Display first link
      if (allLinks.length > 0) {
        const mainLinkBtn = document.getElementById('mainLinkBtn');
        const linkDisplay = document.querySelector('.link-display');
        const moreLinksIndicator = document.getElementById('moreLinksIndicator');
        
        if (mainLinkBtn && linkDisplay) {
          const firstLink = allLinks[0];
          // Use URL instead of text
          linkDisplay.textContent = firstLink.url;
          
          // Calculate how many more links there are (total - 1 for main link)
          const additionalLinksCount = allLinks.length - 1;
          
          // Show "and X more" in the same button
          if (additionalLinksCount > 0) {
            moreLinksIndicator.textContent = `and ${additionalLinksCount} more`;
          }
          
          // Click handler to open modal
          mainLinkBtn.addEventListener('click', () => {
            console.log('Main link clicked');
            if (linksModalReady && typeof openLinksModal === 'function') {
              openLinksModal(allLinks);
            }
          });
          
          mainLinkBtn.style.display = 'flex';
          console.log('✓ Main link displayed');
        }
      }
    }

    // Open links modal
    function openLinksModal(linksData = []) {
      const modal = document.getElementById('linksModal');
      const container = document.getElementById('linksContainer');
      
      if (!modal || !container) {
        console.error('Modal or container not found');
        return;
      }
      
      // Clear container
      container.innerHTML = '';
      
      // Add links
      linksData.forEach(link => {
        const linkItem = document.createElement('a');
        linkItem.href = link.url;
        linkItem.target = '_blank';
        linkItem.className = 'link-item';
        linkItem.innerHTML = `
          <img src="{{ asset('img/link.svg') }}" alt="Link" class="link-icon" />
          <span class="link-text">${link.text}</span>
        `;
        container.appendChild(linkItem);
      });
      
      modal.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeLinksModal() {
      const modal = document.getElementById('linksModal');
      if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
      }
    }

    // Initialize links modal
    function initializeLinksModal() {
      setTimeout(() => {
        const modal = document.getElementById('linksModal');
        if (modal) {
          linksModalReady = true;
          console.log('✓ Links modal ready');
        }
      }, 100);
    }

    function shareThisPage() {
      const pageTitle = document.title;
      const pageUrl = window.location.href;
      
      // Check if Web Share API is available
      if (navigator.share) {
        navigator.share({
          title: pageTitle,
          text: 'Check out this amazing content!',
          url: pageUrl
        }).catch(err => console.log('Error sharing:', err));
      } else {
        // Fallback: Copy to clipboard
        const textToCopy = `${pageTitle}\n${pageUrl}`;
        navigator.clipboard.writeText(textToCopy).then(() => {
          alert('Link copied to clipboard!');
        }).catch(err => {
          console.log('Error copying to clipboard:', err);
          // Final fallback: Show alert with URL
          alert(`Share this link:\n${pageUrl}`);
        });
      }
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
      initializeLinksModal();
      initializeMoreLinks();
      
      // Close modal when clicking outside
      document.addEventListener('click', (e) => {
        const modal = document.getElementById('linksModal');
        if (modal && e.target === modal) {
          closeLinksModal();
        }
      });

      // Close modal with Escape key
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
          const modal = document.getElementById('linksModal');
          if (modal && !modal.classList.contains('hidden')) {
            closeLinksModal();
          }
        }
      });
    });
  </script>

</body>
</html>
