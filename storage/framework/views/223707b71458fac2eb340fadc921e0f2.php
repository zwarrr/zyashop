<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Profile - Zya's Placeshop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
    .feather {
      width: 20px;
      height: 20px;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
    }
    .modal-base { z-index: 50; }
    .modal-confirm { z-index: 60; }
    .modal-alert { z-index: 70; }
  </style>

  <script>
    function openProfileModal() {}
    function closeProfileModal() {}
    function openLinkModal() {}
    function closeLinkModal() {}
    function deleteLink(id) {}
  </script>
</head>
<body class="bg-gray-50">

<!-- Include Bar -->
<?php echo $__env->make('admin.bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!-- Main Content -->
<main class="md:ml-64 pt-16 md:pt-0 min-h-screen">
  <div class="p-4 md:p-8 md:mt-20">
    
    <!-- Profile Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-800">Profile Information</h2>
        <button onclick="openProfileModal()" class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
          Edit Profile
        </button>
      </div>

      <div class="flex items-start gap-6">
        <!-- Profile Image -->
        <div class="flex-shrink-0">
          <img src="<?php echo e($profile?->profile_image ?? 'https://i.pinimg.com/736x/78/0d/08/780d084f353d666f61a0067dbf48bfdd.jpg'); ?>" 
               alt="Profile" 
               class="w-28 h-28 rounded-full object-cover border-4 border-gray-100">
        </div>

        <!-- Profile Info -->
        <div class="flex-1">
          <div class="mb-4">
            <label class="text-xs text-gray-500 font-medium">Username</label>
            <p class="text-sm text-gray-700"><?php echo e('@' . ($profile?->username ?? 'thezyshop')); ?></p>
          </div>

          <div class="mb-4">
            <label class="text-xs text-gray-500 font-medium">Display Name</label>
            <p class="text-lg font-semibold text-gray-800 flex items-center gap-2">
              <?php echo e($profile?->display_name ?? 'User'); ?>

              <?php if($profile?->verified_badge === 'yes'): ?>
              <img src="<?php echo e(asset('img/verift.svg')); ?>" alt="Verified" class="w-5 h-5">
              <?php endif; ?>
            </p>
          </div>

          <div>
            <label class="text-xs text-gray-500 font-medium">Bio</label>
            <p class="text-sm text-gray-700"><?php echo e($profile?->bio ?? 'We sell digital content packages daily!'); ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Links Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">Profile Links</h2>
          <p class="text-xs text-gray-500 mt-1">Manage your external links</p>
        </div>
        <button onclick="openLinkModal('create')" class="px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
          + Tambah Link
        </button>
      </div>

      <!-- Links Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr class="bg-gray-50">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Title</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">URL</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50 transition">
              <td class="px-4 py-4 text-sm font-medium text-gray-900"><?php echo e($link->title); ?></td>
              <td class="px-4 py-4 text-sm text-gray-600">
                <a href="<?php echo e($link->url); ?>" target="_blank" class="text-blue-600 hover:underline"><?php echo e(Str::limit($link->url, 50)); ?></a>
              </td>
              <td class="px-4 py-4 text-sm text-gray-600"><?php echo e($link->order); ?></td>
              <td class="px-4 py-4 text-sm text-center relative">
                <div class="inline-block text-left">
                  <button onclick="toggleDropdown(<?php echo e($link->id); ?>)" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200" title="Aksi">
                    <svg class="feather text-gray-600" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                  </button>
                  <div id="dropdown-<?php echo e($link->id); ?>" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                    <button onclick="openLinkModal('edit', <?php echo e($link->id); ?>); toggleDropdown(<?php echo e($link->id); ?>)" class="w-full flex items-center gap-2 px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm transition duration-200 text-left rounded-t-lg">
                      <svg class="feather" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                      Edit
                    </button>
                    <button onclick="deleteLink(<?php echo e($link->id); ?>); toggleDropdown(<?php echo e($link->id); ?>)" class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 text-sm transition duration-200 text-left rounded-b-lg">
                      <svg class="feather" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                      Hapus
                    </button>
                  </div>
                </div>
              </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="4" class="px-4 py-8 text-center text-gray-500 text-sm">
                Belum ada links. Klik "Tambah Link" untuk menambahkan link baru.
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<!-- Modal Edit Profile -->
<div id="profileModal" class="hidden modal-base fixed inset-0 bg-black/50 flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
      <h3 id="profileModalTitle" class="text-lg font-semibold text-gray-800">Edit Profile</h3>
      <button onclick="closeProfileModal()" class="text-gray-500 hover:text-gray-700">
        <svg class="feather" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>

    <form id="profileForm" class="p-6" enctype="multipart/form-data">
      <?php echo csrf_field(); ?>
      
      <!-- Grid 2 Kolom -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Kolom Kiri -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
            <div class="flex items-center gap-2">
              <span class="text-gray-500">@</span>
              <input type="text" name="username" id="username" value="<?php echo e($profile?->username ?? 'thezyshop'); ?>" required 
                     class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black"
                     placeholder="thezyshop">
            </div>
            <p class="text-xs text-gray-500 mt-1">Hanya huruf, angka, underscore, dan dash</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Display Name *</label>
            <input type="text" name="display_name" id="displayName" value="<?php echo e($profile?->display_name ?? ''); ?>" required 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
            <textarea name="bio" id="bio" rows="4" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black"><?php echo e($profile?->bio ?? ''); ?></textarea>
          </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Verified Badge</label>
            <select name="verified_badge" id="verifiedBadge" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black">
              <option value="no" <?php echo e(($profile?->verified_badge ?? 'no') === 'no' ? 'selected' : ''); ?>>No</option>
              <option value="yes" <?php echo e(($profile?->verified_badge ?? 'no') === 'yes' ? 'selected' : ''); ?>>Yes</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Image (Max 5MB)</label>
            <input type="file" name="profile_image" id="profileImage" accept="image/*" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black">
            <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF</p>
          </div>

          <!-- Image Preview -->
          <div id="imagePreviewContainer" class="hidden">
            <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
            <img id="imagePreview" src="" alt="Preview" class="w-40 h-40 object-cover rounded-lg border-2 border-gray-200">
          </div>
        </div>
      </div>

      <div class="flex gap-3 pt-6 border-t border-gray-200 mt-6">
        <button type="button" onclick="closeProfileModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
          Batal
        </button>
        <button type="submit" class="flex-1 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Add/Edit Link -->
<div id="linkModal" class="hidden modal-base fixed inset-0 bg-black/50 flex items-center justify-center p-4">
  <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
    <div class="flex items-center justify-between p-6 border-b border-gray-200">
      <h3 id="linkModalTitle" class="text-lg font-semibold text-gray-800">Tambah Link</h3>
      <button onclick="closeLinkModal()" class="text-gray-500 hover:text-gray-700">
        <svg class="feather" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
      </button>
    </div>

    <form id="linkForm" class="p-6 space-y-4">
      <?php echo csrf_field(); ?>
      <input type="hidden" id="linkId" name="link_id">
      <input type="hidden" id="linkMethod" value="POST">

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
        <input type="text" name="title" id="linkTitle" required 
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black"
               placeholder="e.g., LinkedIn Profile">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">URL *</label>
        <input type="url" name="url" id="linkUrl" required 
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-black"
               placeholder="https://linkedin.com/in/username">
      </div>

      <div class="flex gap-3 pt-4">
        <button type="button" onclick="closeLinkModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
          Batal
        </button>
        <button type="submit" id="linkSubmitBtn" class="flex-1 px-4 py-2 bg-black text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Include Modals -->
<?php echo $__env->make('admin.partials.delete_confirmation_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('admin.partials.alert_modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
  let currentLinkMode = 'create';
  let currentLinkId = null;

  // Toggle Dropdown
  function toggleDropdown(id) {
    const dropdown = document.getElementById(`dropdown-${id}`);
    document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
      if (d.id !== `dropdown-${id}`) d.classList.add('hidden');
    });
    dropdown.classList.toggle('hidden');
  }

  // Close dropdown when clicking outside
  document.addEventListener('click', function(event) {
    if (!event.target.closest('[onclick^="toggleDropdown"]') && !event.target.closest('[id^="dropdown-"]')) {
      document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
  });

  // Profile Modal Functions
  function openProfileModal() {
    document.getElementById('profileModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

  function closeProfileModal() {
    document.getElementById('profileModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
  }

  // Link Modal Functions
  function openLinkModal(mode, linkId = null) {
    currentLinkMode = mode;
    currentLinkId = linkId;

    document.getElementById('linkForm').reset();
    document.getElementById('linkId').value = '';
    document.getElementById('linkMethod').value = 'POST';

    if (mode === 'create') {
      document.getElementById('linkModalTitle').textContent = 'Tambah Link';
      document.getElementById('linkSubmitBtn').textContent = 'Simpan';
    } else if (mode === 'edit' && linkId) {
      document.getElementById('linkModalTitle').textContent = 'Edit Link';
      document.getElementById('linkSubmitBtn').textContent = 'Perbarui';
      document.getElementById('linkId').value = linkId;
      document.getElementById('linkMethod').value = 'PUT';

      // Load link data via AJAX (simplified - you can add actual fetch here)
      const links = <?php echo json_encode($links, 15, 512) ?>;
      const link = links.find(l => l.id === linkId);
      if (link) {
        document.getElementById('linkTitle').value = link.title;
        document.getElementById('linkUrl').value = link.url;
      }
    }

    document.getElementById('linkModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
  }

  function closeLinkModal() {
    document.getElementById('linkModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
  }

  // Image Preview
  document.getElementById('profileImage')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function(event) {
        document.getElementById('imagePreview').src = event.target.result;
        document.getElementById('imagePreviewContainer').classList.remove('hidden');
      };
      reader.readAsDataURL(file);
    }
  });

  // Profile Form Submit
  document.getElementById('profileForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    const response = await fetch('<?php echo e(route("profile.update")); ?>', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json',
      },
      body: formData
    });

    closeProfileModal();
    
    setTimeout(async () => {
      if (response.ok) {
        showAlertModal('Berhasil', 'Profile berhasil diperbarui!', 'success', () => location.reload());
      } else {
        const errors = await response.json();
        showAlertModal('Error', errors.message || 'Terjadi kesalahan', 'error');
      }
    }, 300);
  });

  // Link Form Submit
  document.getElementById('linkForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const linkId = document.getElementById('linkId').value;
    let url = '<?php echo e(route("profile.links.store")); ?>';
    const formData = new FormData(e.target);
    
    if (currentLinkMode === 'edit' && linkId) {
      url = `/profile/links/${linkId}`;
      formData.append('_method', 'PUT');
    }
    
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'Accept': 'application/json',
      },
      body: formData
    });

    closeLinkModal();
    
    setTimeout(async () => {
      if (response.ok) {
        showAlertModal('Berhasil', 'Link berhasil disimpan!', 'success', () => location.reload());
      } else {
        const errors = await response.json();
        showAlertModal('Error', errors.message || 'Terjadi kesalahan', 'error');
      }
    }, 300);
  });

  // Delete Link
  function deleteLink(linkId) {
    openDeleteConfirmModal(
      'Apakah Anda yakin ingin menghapus link ini?',
      function() {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        
        fetch(`/profile/links/${linkId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showAlertModal('Berhasil', 'Link berhasil dihapus!', 'success', () => location.reload());
          } else {
            showAlertModal('Error', data.error || 'Gagal menghapus link', 'error');
          }
        })
        .catch(error => showAlertModal('Error', 'Terjadi kesalahan saat menghapus', 'error'));
      }
    );
  }
</script>

</body>
</html>
<?php /**PATH C:\GOW\zyashop\resources\views/admin/profile.blade.php ENDPATH**/ ?>