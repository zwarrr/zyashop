<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Kategori - Zya's Placeshop Admin</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Feather Icons from CDN -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.0/feather.min.js"></script>

  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
  
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .nav-link.active {
      @apply text-black font-semibold bg-gray-100;
    }
    .feather {
      width: 20px;
      height: 20px;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      fill: none;
      display: inline-block;
    }
    /* Modal hierarchy */
    .modal-base { z-index: 50; }
    .modal-confirm { z-index: 60; }
    .modal-alert { z-index: 70; }
  </style>

  <script>
    // Function stubs - will be defined below
    function openCategoryModal() {}
    function closeCategoryModal() {}
    function editCategory(index) {}
    function deleteCategory(id) {}
  </script>
</head>

<body class="bg-gray-50 text-gray-800">

  <!-- Wrapper -->
  <div class="flex min-h-screen">

    @include('admin.bar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col md:ml-64">

      <!-- Content -->
      <main class="flex-1 mt-16 md:mt-20 p-4 md:p-8 space-y-8">
        
        <!-- Header Section -->
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-black">Kategori</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola kategori produk untuk toko Zya's Placeshop</p>
          </div>
          <button onclick="openCategoryModal()" class="flex items-center gap-2 bg-black hover:bg-gray-900 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
            <svg class="feather" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Kategori
          </button>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" id="searchText" placeholder="Cari berdasarkan teks..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black" />
            <select id="filterCards" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black">
              <option value="">Filter Cards: Semua</option>
              <option value="001-250">001-250</option>
              <option value="250-500">250-500</option>
              <option value="500-750">500-750</option>
              <option value="750-1000">750-1000</option>
              <option value="1000-1250">1000-1250</option>
              <option value="1250-1500">1250-1500</option>
              <option value="1500-1750">1500-1750</option>
              <option value="1750-2000">1750-2000</option>
            </select>
            <select id="filterType" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black">
              <option value="">Filter Type: Semua</option>
              <option value="tiktok">TikTok</option>
              <option value="shopee">Shopee</option>
            </select>
          </div>
        </div>

        <!-- Categories Table -->
        <div class="bg-white rounded-xl shadow border border-gray-200">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Nama</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Cards</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Type</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                  <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
              </thead>
              <tbody id="categoryTableBody" class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50 transition duration-200">
                  <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    Belum ada kategori. <button onclick="openCategoryModal()" class="text-black font-semibold hover:underline">Buat yang baru</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Include Modals -->
  @include('admin.partials.delete_confirmation_modal')
  @include('admin.partials.alert_modal')

  <!-- Category Modal -->
  <div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-sm max-h-[85vh] flex flex-col">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-200 flex-shrink-0">
        <h3 id="modalTitle" class="text-lg font-bold text-black">Tambah Kategori</h3>
        <button onclick="closeCategoryModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
          <svg class="feather text-gray-600" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>

      <!-- Modal Body - No Scroll -->
      <form id="categoryForm" class="flex-1 overflow-hidden p-5 rounded-b-xl">
        @csrf
        <input type="hidden" id="categoryId" name="category_id">
        <input type="hidden" id="formMethod" name="_method" value="POST">

        <div class="grid grid-cols-1 gap-4 max-w-md">
          <!-- Cards Dropdown -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Cards *</label>
            <select id="categoryCards" name="cards" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black">
              <option value="">Pilih Card</option>
              <option value="001-250">001-250</option>
              <option value="250-500">250-500</option>
              <option value="500-750">500-750</option>
              <option value="750-1000">750-1000</option>
              <option value="1000-1250">1000-1250</option>
              <option value="1250-1500">1250-1500</option>
              <option value="1500-1750">1500-1750</option>
              <option value="1750-2000">1750-2000</option>
            </select>
          </div>

          <!-- Type Dropdown -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Type *</label>
            <div class="space-y-2">
              <label class="flex items-center">
                <input type="checkbox" id="typeTiktok" name="type_tiktok" value="tiktok" class="w-4 h-4 border border-gray-300 rounded focus:ring-2 focus:ring-black">
                <span class="ml-2 text-sm text-gray-700">TikTok</span>
              </label>
              <label class="flex items-center">
                <input type="checkbox" id="typeShopee" name="type_shopee" value="shopee" class="w-4 h-4 border border-gray-300 rounded focus:ring-2 focus:ring-black">
                <span class="ml-2 text-sm text-gray-700">Shopee</span>
              </label>
            </div>
          </div>
        </div>
      </form>

      <!-- Modal Footer -->
      <div class="flex gap-3 border-t border-gray-200 p-5 bg-gray-50 flex-shrink-0 justify-end rounded-b-xl">
        <button type="button" onclick="closeCategoryModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded text-sm font-medium transition duration-200">
          Batal
        </button>
        <button type="submit" form="categoryForm" id="submitBtn" class="bg-black hover:bg-gray-900 text-white px-8 py-2 rounded text-sm font-medium transition duration-200">
          Simpan
        </button>
      </div>
    </div>
  </div>

  <script>
    feather.replace();

    // Load categories from database
    let categories = {!! json_encode($categories) !!};

    // Render table on page load
    document.addEventListener('DOMContentLoaded', renderTable);

    function renderTable() {
      const tbody = document.getElementById('categoryTableBody');
      
      if (categories.length === 0) {
        tbody.innerHTML = `
          <tr class="hover:bg-gray-50 transition duration-200">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
              Belum ada kategori. <button onclick="openCategoryModal()" class="text-black font-semibold hover:underline">Buat yang baru</button>
            </td>
          </tr>
        `;
        return;
      }

      let html = '';
      for (let i = 0; i < categories.length; i++) {
        const cat = categories[i];
        const categoryId = 'CTR' + cat.cards.replace(/-/g, '_');
        let typesDisplay = '';
        if (cat.types && Array.isArray(cat.types)) {
          let typesList = [];
          for (let j = 0; j < cat.types.length; j++) {
            typesList.push(cat.types[j].charAt(0).toUpperCase() + cat.types[j].slice(1));
          }
          typesDisplay = typesList.join('|');
        }
        
        html += `
        <tr class="hover:bg-gray-50 transition duration-200">
          <td class="px-6 py-4 text-sm text-gray-900 font-medium">${categoryId}</td>
          <td class="px-6 py-4 text-sm text-gray-900">${cat.cards}</td>
          <td class="px-6 py-4 text-sm text-gray-900 capitalize">${typesDisplay}</td>
          <td class="px-6 py-4 text-sm"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Aktif</span></td>
          <td class="px-6 py-4 text-center">
            <div class="inline-block text-left">
              <button onclick="toggleDropdown(${cat.id})" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200" title="Aksi">
                <svg class="feather text-gray-600" style="width:20px;height:20px;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
              </button>
              <div id="dropdown-${cat.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                <button onclick="editCategory(${cat.id}); toggleDropdown(${cat.id})" class="w-full flex items-center gap-2 px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm transition duration-200 text-left rounded-t-lg">
                  <svg style="width:16px;height:16px;stroke:currentColor;stroke-width:2;fill:none;" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  Edit
                </button>
                <button onclick="deleteCategory(${cat.id}); toggleDropdown(${cat.id})" class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 text-sm transition duration-200 text-left rounded-b-lg">
                  <svg style="width:16px;height:16px;stroke:currentColor;stroke-width:2;fill:none;" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  Hapus
                </button>
              </div>
            </div>
          </td>
        </tr>
        `;
      }
      tbody.innerHTML = html;
    }

    // Filter functionality
    document.getElementById('searchText').addEventListener('input', filterTable);
    document.getElementById('filterCards').addEventListener('change', filterTable);
    document.getElementById('filterType').addEventListener('change', filterTable);

    function filterTable() {
      const searchText = document.getElementById('searchText').value.toLowerCase();
      const filterCards = document.getElementById('filterCards').value;
      const filterType = document.getElementById('filterType').value;
      const rows = document.querySelectorAll('#categoryTableBody tr');

      rows.forEach(row => {
        if (row.querySelector('td[colspan]')) return; // Skip "belum ada" message row

        const cardCell = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
        const typeCell = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
        const allText = row.textContent.toLowerCase();

        const matchSearch = allText.includes(searchText);
        const matchCards = !filterCards || cardCell === filterCards.toLowerCase();
        const matchType = !filterType || typeCell.includes(filterType.toLowerCase());

        row.style.display = (matchSearch && matchCards && matchType) ? '' : 'none';
      });
    }

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

    function openCategoryModal() {
      const modal = document.getElementById('categoryModal');
      const form = document.getElementById('categoryForm');
      
      form.reset();
      document.getElementById('categoryId').value = '';
      document.getElementById('formMethod').value = 'POST';
      document.getElementById('modalTitle').textContent = 'Tambah Kategori';
      document.getElementById('submitBtn').textContent = 'Simpan';

      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeCategoryModal() {
      const modal = document.getElementById('categoryModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function editCategory(categoryId) {
      // Reset form and set mode
      document.getElementById('categoryId').value = categoryId;
      document.getElementById('formMethod').value = 'PUT';
      document.getElementById('modalTitle').textContent = 'Edit Kategori';
      document.getElementById('submitBtn').textContent = 'Memuat data...';
      document.getElementById('submitBtn').disabled = true;
      
      // Open modal first
      const modal = document.getElementById('categoryModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      
      // Load category data via AJAX
      fetch(`/kategori/${categoryId}/edit`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
        .then(response => {
          if (!response.ok) throw new Error('Failed to load category data');
          return response.json();
        })
        .then(data => {
          document.getElementById('categoryCards').value = data.category.cards || '';
          
          // Clear checkboxes first
          document.getElementById('typeTiktok').checked = false;
          document.getElementById('typeShopee').checked = false;
          
          // Check appropriate type checkboxes based on category.types
          if (data.category.types && Array.isArray(data.category.types)) {
            if (data.category.types.includes('tiktok')) {
              document.getElementById('typeTiktok').checked = true;
            }
            if (data.category.types.includes('shopee')) {
              document.getElementById('typeShopee').checked = true;
            }
          }
        })
        .catch(error => {
          console.error('Error loading category:', error);
          showAlertModal('Error', 'Gagal memuat data kategori. Silakan coba lagi.', 'error');
          closeCategoryModal();
        })
        .finally(() => {
          // Restore button state
          document.getElementById('submitBtn').disabled = false;
          document.getElementById('submitBtn').textContent = 'Update';
        });
    }

    function deleteCategory(categoryId) {
      openDeleteConfirmModal(
        'Apakah Anda yakin ingin menghapus kategori ini?',
        function() {
          const formData = new FormData();
          formData.append('_method', 'DELETE');
          
          fetch(`/kategori/${categoryId}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showAlertModal('Berhasil', 'Kategori berhasil dihapus!', 'success', () => {
                location.reload();
              });
            } else {
              showAlertModal('Error', data.error || 'Gagal menghapus kategori', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showAlertModal('Error', 'Terjadi kesalahan saat menghapus', 'error');
          });
        }
      );
    }

    document.getElementById('categoryForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const categoryId = document.getElementById('categoryId').value;
      const cards = document.getElementById('categoryCards').value;
      const typeTiktok = document.getElementById('typeTiktok').checked;
      const typeShopee = document.getElementById('typeShopee').checked;

      if (!cards || (!typeTiktok && !typeShopee)) {
        closeCategoryModal();
        setTimeout(() => {
          showAlertModal('Validasi', 'Pilih Cards dan minimal 1 Type!', 'warning');
        }, 300);
        return;
      }

      const formData = new FormData();
      formData.append('cards', cards);
      if (typeTiktok) formData.append('type_tiktok', '1');
      if (typeShopee) formData.append('type_shopee', '1');

      let url = '/kategori';

      if (categoryId) {
        url = `/kategori/${categoryId}`;
        formData.append('_method', 'PUT');
      }

      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
          },
          body: formData
        });

        const data = await response.json();

        closeCategoryModal();
        
        setTimeout(() => {
          if (response.ok) {
            showAlertModal('Berhasil', data.success || 'Kategori berhasil disimpan!', 'success', () => {
              location.reload();
            });
          } else {
            showAlertModal('Error', data.error || 'Terjadi kesalahan', 'error');
          }
        }, 300);
      } catch (error) {
        console.error('Error:', error);
        closeCategoryModal();
        setTimeout(() => {
          showAlertModal('Error', 'Terjadi kesalahan saat menyimpan', 'error');
        }, 300);
      }
    });

    // Close modal when clicking outside
    document.getElementById('categoryModal').addEventListener('click', (e) => {
      if (e.target.id === 'categoryModal') {
        closeCategoryModal();
      }
    });
  </script>

</body>
</html>
