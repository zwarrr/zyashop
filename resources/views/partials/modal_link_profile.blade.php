<!-- Modal Overlay -->
<div id="linksModal" class="modal-overlay hidden">
  <!-- Modal Content -->
  <div class="modal-content">
    <!-- Title with Close Button -->
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-lg font-bold">More Links</h2>
      <button class="close-btn" onclick="closeLinksModal()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
    </div>

    <!-- Links Container -->
    <div id="linksContainer" class="flex flex-col gap-0">
      <!-- Links will be injected here -->
    </div>
  </div>
</div>
