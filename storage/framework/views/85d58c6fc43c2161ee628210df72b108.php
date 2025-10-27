<!-- Delete Confirmation Modal -->
<div id="deleteConfirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-[60] flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-lg w-full max-w-sm">
    <!-- Modal Header -->
    <div class="flex items-center justify-between p-5 border-b border-gray-200">
      <h3 class="text-lg font-bold text-black">Konfirmasi Hapus</h3>
      <button onclick="closeDeleteConfirmModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
        <svg class="feather text-gray-600" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="p-5">
      <p id="deleteConfirmMessage" class="text-gray-700">Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.</p>
    </div>

    <!-- Modal Footer -->
    <div class="flex gap-3 border-t border-gray-200 p-5 bg-gray-50 justify-end rounded-b-xl">
      <button type="button" onclick="closeDeleteConfirmModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded text-sm font-medium transition duration-200">
        Batal
      </button>
      <button type="button" id="confirmDeleteBtn" onclick="confirmDelete()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded text-sm font-medium transition duration-200">
        Hapus
      </button>
    </div>
  </div>
</div>

<script>
  let deleteCallback = null;

  function openDeleteConfirmModal(message, callback) {
    document.getElementById('deleteConfirmMessage').textContent = message || 'Apakah Anda yakin ingin menghapus item ini? Tindakan ini tidak dapat dibatalkan.';
    deleteCallback = callback;
    const modal = document.getElementById('deleteConfirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeDeleteConfirmModal() {
    const modal = document.getElementById('deleteConfirmModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    deleteCallback = null;
  }

  function confirmDelete() {
    if (deleteCallback && typeof deleteCallback === 'function') {
      deleteCallback();
    }
    closeDeleteConfirmModal();
  }

  // Close modal when clicking outside
  document.getElementById('deleteConfirmModal').addEventListener('click', (e) => {
    if (e.target.id === 'deleteConfirmModal') {
      closeDeleteConfirmModal();
    }
  });
</script>
<?php /**PATH C:\GOW\zyashop\resources\views/admin/partials/delete_confirmation_modal.blade.php ENDPATH**/ ?>