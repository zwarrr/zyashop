<!-- Ajax Loading Overlay -->
<div id="ajaxLoader" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[80] items-center justify-center" style="display: none;">
  <div class="bg-white rounded-xl shadow-2xl p-8 flex flex-col items-center gap-4 max-w-sm mx-4 transform scale-90 opacity-0 transition-all duration-200" id="ajaxLoaderContent">
    <!-- Spinner -->
    <div class="relative w-16 h-16">
      <div class="absolute inset-0 border-4 border-gray-200 rounded-full"></div>
      <div class="absolute inset-0 border-4 border-black border-t-transparent rounded-full animate-spin"></div>
    </div>
    
    <!-- Loading Text -->
    <div class="text-center">
      <h3 id="loaderTitle" class="text-lg font-bold text-black mb-1">Memproses...</h3>
      <p id="loaderMessage" class="text-sm text-gray-600">Mohon tunggu sebentar</p>
    </div>
  </div>
</div>

<style>
  /* Ajax Loader Styles */
  #ajaxLoader {
    transition: opacity 0.2s ease-in-out;
  }
  
  #ajaxLoader.show {
    display: flex !important;
    animation: fadeIn 0.2s ease-in-out;
  }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
    }
    to {
      opacity: 1;
    }
  }
  
  @keyframes spin {
    to {
      transform: rotate(360deg);
    }
  }
  
  .animate-spin {
    animation: spin 1s linear infinite;
  }
</style>

<script>
  /**
   * Show ajax loading overlay
   * @param {string} title - Loading title (default: "Memproses...")
   * @param {string} message - Loading message (default: "Mohon tunggu sebentar")
   */
  function showAjaxLoader(title = 'Memproses...', message = 'Mohon tunggu sebentar') {
    const loader = document.getElementById('ajaxLoader');
    const loaderContent = document.getElementById('ajaxLoaderContent');
    const loaderTitle = document.getElementById('loaderTitle');
    const loaderMessage = document.getElementById('loaderMessage');
    
    if (loaderTitle) loaderTitle.textContent = title;
    if (loaderMessage) loaderMessage.textContent = message;
    
    if (loader && loaderContent) {
      loader.style.display = 'flex';
      loader.classList.remove('hidden');
      
      // Trigger animation
      setTimeout(() => {
        loaderContent.classList.remove('scale-90', 'opacity-0');
        loaderContent.classList.add('scale-100', 'opacity-100');
      }, 10);
      
      document.body.style.overflow = 'hidden'; // Prevent scrolling
    }
  }

  /**
   * Hide ajax loading overlay
   */
  function hideAjaxLoader() {
    const loader = document.getElementById('ajaxLoader');
    const loaderContent = document.getElementById('ajaxLoaderContent');
    
    if (loader && loaderContent) {
      loaderContent.classList.remove('scale-100', 'opacity-100');
      loaderContent.classList.add('scale-90', 'opacity-0');
      
      setTimeout(() => {
        loader.style.display = 'none';
        loader.classList.add('hidden');
        document.body.style.overflow = ''; // Restore scrolling
      }, 200);
    }
  }

  /**
   * Show loader for a fetch request
   * @param {Promise} promise - The fetch promise
   * @param {string} title - Loading title
   * @param {string} message - Loading message
   * @returns {Promise} The original promise
   */
  function ajaxWithLoader(promise, title = 'Memproses...', message = 'Mohon tunggu sebentar') {
    showAjaxLoader(title, message);
    return promise.finally(() => {
      hideAjaxLoader();
    });
  }

  /**
   * Enhanced fetch with automatic loader
   * @param {string} url - URL to fetch
   * @param {object} options - Fetch options
   * @param {string} loaderTitle - Loading title
   * @param {string} loaderMessage - Loading message
   * @returns {Promise}
   */
  function fetchWithLoader(url, options = {}, loaderTitle = 'Memproses...', loaderMessage = 'Mohon tunggu sebentar') {
    showAjaxLoader(loaderTitle, loaderMessage);
    return fetch(url, options)
      .finally(() => {
        hideAjaxLoader();
      });
  }

  /**
   * Initialize page navigation loader
   * This will show loader on all link clicks and form submissions
   */
  document.addEventListener('DOMContentLoaded', function() {
    // Show loader on all link clicks (navigation links)
    document.addEventListener('click', function(e) {
      const target = e.target.closest('a');
      if (target && target.href) {
        const href = target.href;
        const isHashLink = href.includes('#');
        const isExternal = target.target === '_blank';
        const isSameOrigin = href.startsWith(window.location.origin);
        const hasPreventDefault = target.hasAttribute('onclick');
        
        // Show loader for internal navigation links only
        if (!isHashLink && !isExternal && isSameOrigin && !hasPreventDefault) {
          showAjaxLoader('Memuat Halaman', 'Mohon tunggu sebentar...');
        }
      }
    });

    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
      hideAjaxLoader();
    });

    // Hide loader on page show (back/forward navigation)
    window.addEventListener('pageshow', function(event) {
      hideAjaxLoader();
    });

    // Initial hide on DOM ready
    hideAjaxLoader();
  });
</script>

