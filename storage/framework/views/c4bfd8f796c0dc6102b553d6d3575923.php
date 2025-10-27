<!-- Modal Overlay -->
<div id="comingSoonModal" class="modal-overlay hidden">
  <!-- Modal Content -->
  <div class="modal-content">
    <!-- Coming Soon Icon -->
    <div class="mb-6">
      <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
    </div>

    <!-- Title -->
    <h2 class="text-2xl font-bold mb-2 text-black">Coming Soon!</h2>

    <!-- Description -->
    <p class="text-gray-600 text-sm mb-6">
      This product collection will be available very soon. Stay tuned for more exciting products!
    </p>

    <!-- Product Range Info -->
    <div id="productRangeInfo" class="bg-gray-100 rounded-lg p-4 mb-6">
      <p class="text-xs text-gray-500 font-semibold mb-1">PRODUCT RANGE</p>
      <p id="productRange" class="text-lg font-bold text-black">Coming Soon</p>
    </div>

    <!-- Close Button -->
    <button 
      onclick="closeComingSoonModal()" 
      class="w-full bg-black text-white py-3 rounded-lg font-semibold text-sm hover:bg-gray-800 active:bg-gray-900 transition-all duration-200"
    >
      Got It!
    </button>
  </div>
</div>

<script>
  // Close modal when clicking outside
  document.addEventListener('click', (e) => {
    const modal = document.getElementById('comingSoonModal');
    if (e.target === modal) {
      closeComingSoonModal();
    }
  });

  // Close modal with Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeComingSoonModal();
    }
  });
</script><?php /**PATH C:\GOW\zyashop\resources\views/partials/modal_information_product_coomingsoon.blade.php ENDPATH**/ ?>