<!-- Alert Modal -->
<div id="alertModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[70] flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-sm">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-5 border-b border-gray-200">
      <h3 id="alertTitle" class="text-lg font-bold text-black">Notifikasi</h3>
      <button onclick="closeAlertModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
        <svg class="feather text-gray-600" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5">
      <div id="alertContent" class="flex items-start gap-3">
        <div id="alertIcon" class="flex-shrink-0"></div>
        <p id="alertMessage" class="text-gray-700"></p>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="flex gap-3 border-t border-gray-200 p-5 bg-gray-50 justify-end rounded-b-xl">
      <button type="button" onclick="closeAlertModal()" id="alertBtn" class="bg-black hover:bg-gray-900 text-white px-6 py-2 rounded text-sm font-medium transition duration-200">
        OK
      </button>
    </div>
  </div>
</div>

<script>
  function showAlertModal(title, message, type = 'info', callback = null) {
    document.getElementById('alertTitle').textContent = title || 'Notifikasi';
    document.getElementById('alertMessage').textContent = message;
    
    const alertIcon = document.getElementById('alertIcon');
    const alertBtn = document.getElementById('alertBtn');
    
    // Clear previous icon
    alertIcon.innerHTML = '';
    
    // Set icon and button color based on type
    if (type === 'success') {
      alertIcon.innerHTML = '<svg class="feather text-green-600" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>';
      alertBtn.className = 'bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded text-sm font-medium transition duration-200';
    } else if (type === 'error') {
      alertIcon.innerHTML = '<svg class="feather text-red-600" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>';
      alertBtn.className = 'bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded text-sm font-medium transition duration-200';
    } else if (type === 'warning') {
      alertIcon.innerHTML = '<svg class="feather text-yellow-600" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
      alertBtn.className = 'bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded text-sm font-medium transition duration-200';
    } else {
      alertIcon.innerHTML = '<svg class="feather text-blue-600" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
      alertBtn.className = 'bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded text-sm font-medium transition duration-200';
    }
    
    // Store callback for when OK is clicked
    window.alertCallback = callback;
    
    const modal = document.getElementById('alertModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeAlertModal() {
    const modal = document.getElementById('alertModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    
    // Execute callback if exists
    if (window.alertCallback && typeof window.alertCallback === 'function') {
      window.alertCallback();
      window.alertCallback = null;
    }
  }

  // Close modal when clicking outside
  document.getElementById('alertModal').addEventListener('click', (e) => {
    if (e.target.id === 'alertModal') {
      closeAlertModal();
    }
  });

  // Close modal with Enter key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !document.getElementById('alertModal').classList.contains('hidden')) {
      closeAlertModal();
    }
  });
</script>
<?php /**PATH C:\GOW\zyashop\resources\views/admin/partials/alert_modal.blade.php ENDPATH**/ ?>