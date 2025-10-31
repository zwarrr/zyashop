<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Cards - Zya's Placeshop Admin</title>

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
</head>

<body class="bg-gray-50 text-gray-800">

  <!-- Include Modals -->
  @include('admin.partials.delete_confirmation_modal')
  @include('admin.partials.alert_modal')
  @include('partials.ajax_loader')

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
            <h2 class="text-2xl font-bold text-black">Cards</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola cards dan banner untuk toko Zya's Placeshop</p>
          </div>
          <button onclick="openCardModal()" class="flex items-center gap-2 bg-black hover:bg-gray-900 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
            <svg class="feather" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Card
          </button>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" id="searchCard" placeholder="Cari card..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black" />
            <select id="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black">
              <option value="">Status: Semua</option>
              <option value="active">Aktif</option>
              <option value="inactive">Nonaktif</option>
            </select>
          </div>
        </div>

        <!-- Cards Table -->
        <div class="bg-white rounded-xl shadow border border-gray-200">
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Card</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Kategori</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                  <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
              </thead>
              <tbody id="cardsTableBody" class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50 transition duration-200">
                  <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    Belum ada card. <button onclick="openCardModal()" class="text-black font-semibold hover:underline">Buat yang baru</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Card Modal -->
  <div id="cardModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-5xl max-h-[85vh] flex flex-col">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-200 flex-shrink-0">
        <h3 id="modalTitle" class="text-lg font-bold text-black">Tambah Card</h3>
        <button onclick="closeCardModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
          <svg class="feather text-gray-600" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>

      <!-- Modal Body - No Scroll -->
      <form id="cardForm" class="flex-1 overflow-hidden p-5 rounded-b-xl" enctype="multipart/form-data">
        @csrf
        <input type="hidden" id="cardId" name="card_id">
        <input type="hidden" id="formMethod" name="_method" value="POST">

        <div class="grid grid-cols-2 gap-4 h-full">
          <!-- Left Column -->
          <div class="space-y-3.5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Card *</label>
              <input type="text" id="cardTitle" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black" placeholder="Judul card">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori (Cards) *</label>
              <select id="cardCategory" name="category" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black">
                <option value="">Pilih Cards</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
              <select id="cardStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black">
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
              </select>
            </div>
          </div>

          <!-- Right Column - Image -->
          <div class="flex flex-col">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gambar (Opsional, 1080x1080, Max 10MB)</label>
            
            <!-- Image Preview Container -->
            <div id="imagePreviewContainer" class="mb-2 hidden">
              <div class="relative w-full aspect-square bg-gray-100 rounded border border-gray-300 overflow-hidden group">
                <img id="imagePreview" src="" alt="Preview" class="w-full h-full object-contain">
                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                  Preview
                </div>
                <!-- Change/Remove Image Buttons -->
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                  <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('cardImage').click()" class="bg-white text-black px-4 py-2 rounded text-sm font-medium hover:bg-gray-200 transition">
                      <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                      Ganti
                    </button>
                    <button type="button" onclick="removeCardImage()" class="bg-red-600 text-white px-4 py-2 rounded text-sm font-medium hover:bg-red-700 transition">
                      <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                      Hapus
                    </button>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- File Input -->
            <div class="space-y-1.5">
              <input type="file" id="cardImage" name="image" accept="image/*" onchange="previewCardImage(event)" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black">
              <p class="text-gray-500 text-xs">JPG, PNG, GIF (1080x1080, Opsional)</p>
            </div>
          </div>
        </div>
      </form>

      <!-- Modal Footer -->
      <div class="flex gap-3 border-t border-gray-200 p-5 bg-gray-50 flex-shrink-0 justify-end rounded-b-xl">
        <button type="button" onclick="closeCardModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded text-sm font-medium transition duration-200">
          Batal
        </button>
        <button type="submit" form="cardForm" id="submitBtn" class="bg-black hover:bg-gray-900 text-white px-8 py-2 rounded text-sm font-medium transition duration-200">
          Simpan
        </button>
      </div>
    </div>
  </div>

  <script>
    feather.replace();

    // Load cards and categories from database
    let allCards = @json($cards ?? []);
    const allCategories = @json($categories ?? []);

    // Load cards table via AJAX after page load
    document.addEventListener('DOMContentLoaded', function() {
      loadCardsTable();
    });

    function loadCardsTable() {
      const tableBody = document.getElementById('cardsTableBody');
      
      fetch('/cards', {
        headers: { 'Accept': 'application/json' }
      })
      .then(res => res.json())
      .then(data => {
        console.log('Loaded cards:', data.cards);
        
        if (!data.cards || data.cards.length === 0) {
          tableBody.innerHTML = `
            <tr>
              <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                Belum ada card. <button onclick="openCardModal()" class="text-black font-semibold hover:underline">Buat yang baru</button>
              </td>
            </tr>
          `;
          return;
        }

        tableBody.innerHTML = data.cards.map(card => `
          <tr class="hover:bg-gray-50 transition duration-200">
            <td class="px-6 py-4">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0" data-card-id="${card.id}" data-has-image="true">
                  <svg class="feather text-gray-600" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
                </div>
                <div>
                  <p class="font-medium text-black">${card.title}</p>
                  <p class="text-xs text-gray-500">ID: ${card.id}</p>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">${card.category}</td>
            <td class="px-6 py-4">
              <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${
                card.status === 'active' ? 'bg-green-100 text-green-700' :
                'bg-gray-100 text-gray-700'
              }">
                ${card.status.charAt(0).toUpperCase() + card.status.slice(1)}
              </span>
            </td>
            <td class="px-6 py-4 text-center" style="position: relative; z-index: 10;">
              <div class="relative inline-block text-left" style="position: relative; z-index: inherit;">
                <button onclick="toggleCardDropdown(event, ${card.id})" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200" title="Aksi">
                  <svg class="feather text-gray-600" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                </button>
                <div id="dropdown-${card.id}" class="hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-lg shadow-2xl border border-gray-200 z-[9999] will-change-transform" style="position: fixed; min-width: 200px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                  <button type="button" onclick="editCard(${card.id}); toggleCardDropdown(null, ${card.id}); event.stopPropagation();" class="w-full flex items-center gap-2 px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm transition duration-200 text-left rounded-t-lg">
                    <svg class="feather" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit
                  </button>
                  <button type="button" onclick="deleteCard(${card.id}); toggleCardDropdown(null, ${card.id}); event.stopPropagation();" class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 text-sm transition duration-200 text-left rounded-b-lg">
                    <svg class="feather" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    Hapus
                  </button>
                </div>
              </div>
            </td>
          </tr>
        `).join('');

        // Load card thumbnails lazily
        document.querySelectorAll('[data-card-id][data-has-image="true"]').forEach(el => {
          const cardId = el.getAttribute('data-card-id');
          console.log('Loading thumbnail for card:', cardId);
          
          fetch(`/cards/${cardId}`, {
            headers: { 
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(res => {
            console.log('Fetch response status for card ' + cardId + ':', res.status, res.statusText);
            if (!res.ok) {
              throw new Error('Response status: ' + res.status + ' ' + res.statusText);
            }
            return res.text(); // Get as text first
          })
          .then(text => {
            console.log('Response text for card ' + cardId + ' (first 200 chars):', text.substring(0, 200));
            try {
              return JSON.parse(text);
            } catch (e) {
              console.error('Failed to parse JSON for card ' + cardId, e);
              throw e;
            }
          })
          .then(data => {
            console.log('Card data for', cardId, ':', data);
            
            if (data.card?.image) {
              console.log('Image found for card', cardId, '- loading image');
              const img = document.createElement('img');
              img.src = data.card.image;
              img.alt = data.card.title || 'Card';
              img.className = 'w-full h-full object-cover';
              el.innerHTML = '';
              el.appendChild(img);
            } else {
              console.warn('No image found for card', cardId);
            }
          })
          .catch(err => console.error('Error loading thumbnail for card ' + cardId + ':', err));
        });
      })
      .catch(err => {
        console.error('Error loading cards:', err);
        tableBody.innerHTML = `
          <tr>
            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
              Error loading cards. Please refresh the page.
            </td>
          </tr>
        `;
      });
    }

    // Toggle card dropdown
    function toggleCardDropdown(event, id) {
      if (event) {
        event.stopPropagation();
        event.preventDefault();
      }
      
      console.log('toggleCardDropdown called with id:', id);
      const dropdown = document.getElementById(`dropdown-${id}`);
      console.log('Dropdown element:', dropdown);
      
      if (!dropdown) {
        console.error('Dropdown not found for id:', id);
        return;
      }
      
      // Hide all other dropdowns
      document.querySelectorAll('[id^="dropdown-"]').forEach(d => {
        if (d.id !== `dropdown-${id}`) {
          d.classList.add('hidden');
        }
      });
      
      // Toggle current dropdown
      dropdown.classList.toggle('hidden');
      console.log('Dropdown toggled, now hidden:', dropdown.classList.contains('hidden'));
      
      // If showing dropdown, position it properly
      if (!dropdown.classList.contains('hidden')) {
        // Position dropdown near the button
        const button = event?.target?.closest('button');
        if (button) {
          const rect = button.getBoundingClientRect();
          dropdown.style.top = (rect.bottom + window.scrollY) + 'px';
          dropdown.style.left = (rect.right - 200 + window.scrollX) + 'px';
        }
      }
    }
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
      // Only close if click is outside dropdown
      const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
      dropdowns.forEach(dropdown => {
        if (!dropdown.closest('td')?.contains(event.target)) {
          dropdown.classList.add('hidden');
        }
      });
    });

    // Preview image when file selected
    function previewCardImage(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById('imagePreview').src = e.target.result;
          document.getElementById('imagePreviewContainer').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      }
    }

    // Remove card image preview
    function removeCardImage() {
      document.getElementById('imagePreview').src = '';
      document.getElementById('imagePreviewContainer').classList.add('hidden');
      document.getElementById('cardImage').value = '';
    }

    // Load categories from database and populate dropdown
    function populateCategoryDropdown() {
      const categorySelect = document.getElementById('cardCategory');
      
      console.log('All Categories:', allCategories); // Debug log
      
      // Get unique card ranges from categories
      const uniqueCards = [...new Set(allCategories.map(cat => cat.cards))].filter(card => card);
      
      console.log('Unique Cards:', uniqueCards); // Debug log
      
      categorySelect.innerHTML = '<option value="">Pilih Cards</option>';
      uniqueCards.forEach(card => {
        const option = document.createElement('option');
        option.value = card;
        option.textContent = card;
        categorySelect.appendChild(option);
      });
      
      console.log('Category dropdown populated with', uniqueCards.length, 'options'); // Debug log
    }

    // Render cards table
    function renderCardsTable() {
      const tbody = document.getElementById('cardsTableBody');
      
      if (allCards.length === 0) {
        tbody.innerHTML = `
          <tr class="hover:bg-gray-50 transition duration-200">
            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
              Belum ada card. <button onclick="openCardModal()" class="text-black font-semibold hover:underline">Buat yang baru</button>
            </td>
          </tr>
        `;
        return;
      }

      tbody.innerHTML = allCards.map(card => {
        // Handle image path - check multiple possible fields
        let imageSrc = '';
        if (card.image_url) {
          imageSrc = card.image_url;  // Already a data URL or full URL
        } else if (card.image) {
          // Check if it's already a data URL (base64)
          if (card.image.startsWith('data:')) {
            imageSrc = card.image;  // Use as-is
          } else {
            // Old format - filename
            imageSrc = `/storage/${card.image}`;
          }
        }
        
        console.log('Rendering card:', {
          id: card.id,
          title: card.title,
          image: card.image,
          image_url: card.image_url,
          finalSrc: imageSrc
        });
        
        return `
        <tr class="hover:bg-gray-50 transition duration-200 card-row" data-title="${card.title}" data-status="${card.status}">
          <td class="px-6 py-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                ${imageSrc ? 
                  `<img src="${imageSrc}" alt="${card.title}" class="w-full h-full object-cover" 
                        onerror="console.error('Image load error:', '${imageSrc}'); this.parentElement.innerHTML='<svg class=\\'feather text-gray-600\\' viewBox=\\'0 0 24 24\\'><rect x=\\'3\\' y=\\'3\\' width=\\'18\\' height=\\'18\\' rx=\\'2\\' ry=\\'2\\'></rect><circle cx=\\'8.5\\' cy=\\'8.5\\' r=\\'1.5\\'></circle><path d=\\'M21 15l-5-5L5 21\\'></path></svg>';" 
                        onload="console.log('✅ Image loaded:', '${imageSrc}');">` : 
                  `<svg class="feather text-gray-600" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>`
                }
              </div>
              <div>
                <p class="font-medium text-black">${card.title}</p>
                <p class="text-xs text-gray-500">ID: ${card.id}</p>
              </div>
            </div>
          </td>
          <td class="px-6 py-4 text-sm text-gray-900">${card.category}</td>
          <td class="px-6 py-4 text-sm">
            <span class="px-3 py-1 rounded-full text-xs font-medium ${card.status === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
              ${card.status === 'active' ? 'Aktif' : 'Nonaktif'}
            </span>
          </td>
          <td class="px-6 py-4 text-center">
            <div class="inline-block text-left">
              <button onclick="toggleDropdown(${card.id})" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200" title="Aksi">
                <svg class="feather text-gray-600" style="width:20px;height:20px;" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
              </button>
              <div id="dropdown-${card.id}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                <button onclick="editCard(${card.id}); toggleDropdown(${card.id})" class="w-full flex items-center gap-2 px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm transition duration-200 text-left rounded-t-lg">
                  <svg style="width:16px;height:16px;stroke:currentColor;stroke-width:2;fill:none;" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                  Edit
                </button>
                <button onclick="deleteCard(${card.id}); toggleDropdown(${card.id})" class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 text-sm transition duration-200 text-left rounded-b-lg">
                  <svg style="width:16px;height:16px;stroke:currentColor;stroke-width:2;fill:none;" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                  Hapus
                </button>
              </div>
            </div>
          </td>
        </tr>
        `;
      }).join('');
    }

    // Filter functionality
    document.getElementById('searchCard').addEventListener('input', filterCards);
    document.getElementById('filterStatus').addEventListener('change', filterCards);

    function filterCards() {
      const searchText = document.getElementById('searchCard').value.toLowerCase();
      const filterStatus = document.getElementById('filterStatus').value;
      const rows = document.querySelectorAll('.card-row');

      rows.forEach(row => {
        const title = row.getAttribute('data-title').toLowerCase();
        const status = row.getAttribute('data-status');

        const matchSearch = title.includes(searchText);
        const matchStatus = !filterStatus || status === filterStatus;

        row.style.display = (matchSearch && matchStatus) ? '' : 'none';
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

    function openCardModal() {
      const modal = document.getElementById('cardModal');
      const form = document.getElementById('cardForm');
      
      form.reset();
      document.getElementById('cardId').value = '';
      document.getElementById('formMethod').value = 'POST';
      document.getElementById('imagePreviewContainer').classList.add('hidden');
      document.getElementById('imagePreview').src = '';
      document.getElementById('modalTitle').textContent = 'Tambah Card';
      document.getElementById('submitBtn').textContent = 'Simpan';

      // Populate category dropdown setiap kali modal dibuka
      populateCategoryDropdown();

      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeCardModal() {
      const modal = document.getElementById('cardModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function editCard(cardId) {
      // Reset form and set mode
      document.getElementById('cardId').value = cardId;
      document.getElementById('formMethod').value = 'PUT';
      document.getElementById('modalTitle').textContent = 'Edit Card';
      document.getElementById('submitBtn').textContent = 'Memuat data...';
      document.getElementById('submitBtn').disabled = true;
      
      // Clear image preview
      document.getElementById('imagePreviewContainer').classList.add('hidden');
      
      // Open modal first
      const modal = document.getElementById('cardModal');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      
      // Load card data via AJAX with loader
      showAjaxLoader('Memuat Data', 'Mengambil data card...');
      fetch(`/cards/${cardId}/edit`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        }
      })
        .then(response => {
          if (!response.ok) throw new Error('Failed to load card data');
          return response.json();
        })
        .then(data => {
          document.getElementById('cardTitle').value = data.card.title || '';
          document.getElementById('cardCategory').value = data.card.category || '';
          document.getElementById('cardStatus').value = data.card.status || 'active';
          
          // Load image from show() endpoint to get full base64 data
          return fetch(`/cards/${cardId}`, {
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          })
          .then(res => {
            if (!res.ok) throw new Error('Failed to load image');
            return res.json();
          })
          .then(imageData => {
            if (imageData.card?.image) {
              document.getElementById('imagePreview').src = imageData.card.image;
              document.getElementById('imagePreviewContainer').classList.remove('hidden');
            }
          });
        })
        .catch(error => {
          console.error('Error loading card:', error);
          showAlertModal('Error', 'Gagal memuat data card. Silakan coba lagi.', 'error');
          closeCardModal();
        })
        .finally(() => {
          // Restore button state
          hideAjaxLoader();
          document.getElementById('submitBtn').disabled = false;
          document.getElementById('submitBtn').textContent = 'Update';
        });
    }

    function deleteCard(cardId) {
      openDeleteConfirmModal(
        'Apakah Anda yakin ingin menghapus card ini?',
        function() {
          const formData = new FormData();
          formData.append('_method', 'DELETE');
          
          showAjaxLoader('Menghapus Card', 'Sedang menghapus card...');
          fetch(`/cards/${cardId}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            hideAjaxLoader();
            if (data.success) {
              showAlertModal('Berhasil', data.success || 'Card berhasil dihapus!', 'success', () => {
                allCards = allCards.filter(c => c.id !== cardId);
                renderCardsTable();
              });
            } else {
              showAlertModal('Error', data.error || 'Gagal menghapus card', 'error');
            }
          })
          .catch(error => {
            hideAjaxLoader();
            console.error('Error:', error);
            showAlertModal('Error', 'Terjadi kesalahan saat menghapus', 'error');
          });
        }
      );
    }

    // Image preview
    document.getElementById('cardImage').addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        if (file.size > 10485760) {
          closeCardModal();
          setTimeout(() => {
            showAlertModal('Validasi', 'Ukuran file tidak boleh lebih dari 10MB', 'warning', () => {
              openCardModal();
            });
          }, 300);
          e.target.value = '';
          return;
        }
        
        const reader = new FileReader();
        reader.onload = (event) => {
          document.getElementById('imagePreview').src = event.target.result;
          document.getElementById('imagePreviewContainer').classList.remove('hidden');
        };
        reader.readAsDataURL(file);
      }
    });

    document.getElementById('cardForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const cardId = document.getElementById('cardId').value;
      const formData = new FormData(document.getElementById('cardForm'));
      
      // Debug: Log file being sent
      if (formData.has('image')) {
        const imageFile = formData.get('image');
        console.log('📁 File to upload:', {
          name: imageFile.name,
          size: imageFile.size,
          type: imageFile.type
        });
      }
      
      let url = '/cards';

      if (cardId) {
        url = `/cards/${cardId}`;
        formData.append('_method', 'PUT');
      }

      try {
        showAjaxLoader('Menyimpan Card', 'Sedang menyimpan data card...');
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        });

        // Get response text first
        const responseText = await response.text();
        console.log('Response status:', response.status);
        console.log('Response text:', responseText.substring(0, 200));
        
        // Try to parse as JSON regardless of Content-Type
        let data;
        try {
          data = JSON.parse(responseText);
          console.log('✅ Parsed JSON successfully:', data);
          
          // 🔴 IF ERROR, LOG IT PROMINENTLY
          if (data.error) {
            console.error('🔴 UPLOAD ERROR FROM SERVER:', data.error);
          }
        } catch (parseError) {
          console.error('❌ Failed to parse JSON:', parseError);
          console.error('Response was:', responseText.substring(0, 500));
          throw new Error('Server response bukan JSON valid');
        }

        hideAjaxLoader();
        closeCardModal();
        
        setTimeout(() => {
          if (response.ok) {
            showAlertModal('Berhasil', data.success || 'Card berhasil disimpan!', 'success', () => {
              if (cardId) {
                const index = allCards.findIndex(c => c.id === parseInt(cardId));
                if (index !== -1) {
                  allCards[index] = data.card;
                }
              } else {
                allCards.push(data.card);
              }
              
              renderCardsTable();
            });
          } else {
            console.error('❌ SERVER ERROR:', data.error);
            showAlertModal('Error', data.error || 'Terjadi kesalahan', 'error');
          }
        }, 300);
      } catch (error) {
        hideAjaxLoader();
        console.error('Error:', error);
        closeCardModal();
        setTimeout(() => {
          showAlertModal('Error', error.message || 'Terjadi kesalahan saat menyimpan card', 'error');
        }, 300);
      }
    });

    // Close modal when clicking outside
    document.getElementById('cardModal').addEventListener('click', (e) => {
      if (e.target.id === 'cardModal') {
        closeCardModal();
      }
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', () => {
      populateCategoryDropdown();
      renderCardsTable();
    });
  </script>

</body>
</html>
