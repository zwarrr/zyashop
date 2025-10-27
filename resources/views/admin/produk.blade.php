<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Produk - Zya's Placeshop Admin</title>

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
    #productModal > div { z-index: 60; }
  </style>

  <script>
    // Function stubs - will be defined below
    function openProductModal() {}
    function closeProductModal() {}
    function deleteProduct(id) {}
  </script>
</head>

<body class="bg-gray-50 text-gray-800">

  <!-- Include Modals -->
  @include('admin.partials.delete_confirmation_modal')
  @include('admin.partials.alert_modal')

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
            <h2 class="text-2xl font-bold text-black">Daftar Produk</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dan pantau semua produk yang tersedia</p>
          </div>
          <button onclick="openProductModal()" class="flex items-center gap-2 bg-black hover:bg-gray-900 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
            <svg class="feather" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Produk
          </button>
        </div>

        <!-- Search and Filter -->
        <div class="bg-white rounded-xl p-6 shadow border border-gray-200">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" placeholder="Cari produk..." class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black" />
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black">
              <option>Semua Kategori</option>
              <option>Elektronik</option>
              <option>Fashion</option>
              <option>Makanan</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black">
              <option>Status: Semua</option>
              <option>Aktif</option>
              <option>Nonaktif</option>
            </select>
          </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-xl shadow border border-gray-200">
          <div class="table-wrapper">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Produk</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Card</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                  <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50 transition duration-200">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                        @if($product->image_url)
                          <img src="{{ $product->image_url }}" alt="{{ $product->title }}" class="w-full h-full object-cover">
                        @else
                          <svg class="feather text-gray-600" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
                        @endif
                      </div>
                      <div>
                        <p class="font-medium text-black">{{ $product->title }}</p>
                        <p class="text-xs text-gray-500">ID: {{ $product->id }}</p>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700">{{ $product->card ? $product->card->title : '-' }}</td>
                  <td class="px-6 py-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                      @if($product->status === 'active') bg-green-100 text-green-700
                      @elseif($product->status === 'inactive') bg-gray-100 text-gray-700
                      @else bg-yellow-100 text-yellow-700
                      @endif">
                      {{ ucfirst($product->status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                    <div class="relative inline-block text-left">
                      <button onclick="toggleDropdown({{ $product->id }})" class="p-2 hover:bg-gray-100 rounded-lg transition duration-200" title="Aksi">
                        <svg class="feather text-gray-600" viewBox="0 0 24 24"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
                      </button>
                      <div id="dropdown-{{ $product->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                        <button onclick="openProductModal('edit', {{ $product->id }}); toggleDropdown({{ $product->id }})" class="w-full flex items-center gap-2 px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm transition duration-200 text-left rounded-t-lg">
                          <svg class="feather" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                          Edit
                        </button>
                        <button onclick="deleteProduct({{ $product->id }}); toggleDropdown({{ $product->id }})" class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 text-sm transition duration-200 text-left rounded-b-lg">
                          <svg class="feather" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                          Hapus
                        </button>
                      </div>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    Belum ada produk. <a href="{{ route('produk.create') }}" class="text-black font-semibold hover:underline">Buat produk baru</a>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

      </main>
    </div>
  </div>

  <!-- Product Modal -->
  <div id="productModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-5xl max-h-[90vh] flex flex-col">
      <!-- Modal Header -->
      <div class="flex items-center justify-between p-5 border-b border-gray-200 flex-shrink-0">
        <h3 id="modalTitle" class="text-lg font-bold text-black">Tambah Produk Baru</h3>
        <button onclick="closeProductModal()" class="p-2 hover:bg-gray-100 rounded-lg transition">
          <svg class="feather text-gray-600" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
      </div>

      <!-- Modal Body - Scrollable -->
      <form id="productForm" class="flex-1 overflow-y-auto p-5">
        @csrf
        <input type="hidden" id="productId" name="product_id">
        <input type="hidden" id="formMethod" name="_method" value="POST">

        <div class="grid grid-cols-3 gap-4">
          <!-- Left Column -->
          <div class="space-y-3.5">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Produk *</label>
              <input type="text" id="productTitle" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black" placeholder="Nama produk">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Card *</label>
              <select id="productCard" name="card_id" required class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black">
                <option value="">Pilih Card</option>
                @foreach($cards as $card)
                  <option value="{{ $card->id }}">{{ $card->title }} - {{ $card->category }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
              <select id="productStatus" name="status" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black">
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Link Shopee</label>
              <input type="url" id="productLinkShopee" name="link_shopee" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black" placeholder="https://shopee.co.id/...">
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Link Tiktok Shop</label>
              <input type="url" id="productLinkTiktok" name="link_tiktok" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black" placeholder="https://vt.tiktok.com/...">
            </div>
          </div>

          <!-- Middle Column -->
          <div class="space-y-3.5 flex flex-col">
            <div class="flex-1 flex flex-col">
              <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
              <textarea id="productDescription" name="description" class="w-full flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black resize-none" placeholder="Deskripsi produk..."></textarea>
            </div>
          </div>

          <!-- Right Column - Image -->
          <div class="flex flex-col">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gambar Produk (Opsional, Max 10MB)</label>
            <div id="imagePreviewContainer" class="mb-2 hidden">
              <div class="relative w-full aspect-square bg-gray-100 rounded border border-gray-300 overflow-hidden">
                <img id="imagePreview" src="" alt="Preview" class="w-full h-full object-contain">
                <div class="absolute top-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                  Preview
                </div>
              </div>
            </div>
            <div class="space-y-1.5">
              <input type="file" id="productImage" name="image" accept="image/*" onchange="previewProductImage(event)" class="w-full px-3 py-2 border border-gray-300 rounded text-sm focus:outline-none focus:ring-2 focus:ring-black">
              <p class="text-gray-500 text-xs">JPG, PNG, GIF (Opsional)</p>
            </div>
          </div>
        </div>
      </form>

      <!-- Modal Footer -->
      <div class="flex gap-3 border-t border-gray-200 p-5 bg-gray-50 flex-shrink-0 justify-end rounded-b-xl">
        <button type="button" onclick="closeProductModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded text-sm font-medium transition duration-200">
          Batal
        </button>
        <button type="submit" form="productForm" id="submitBtn" class="bg-black hover:bg-gray-900 text-white px-8 py-2 rounded text-sm font-medium transition duration-200">
          Simpan
        </button>
      </div>
    </div>
  </div>

  <script>
    feather.replace();

    let currentMode = 'create';
    let allCards = {!! json_encode($cards) !!};

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

    // Preview image when file selected
    function previewProductImage(event) {
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

    function openProductModal(mode = 'create', productId = null) {
      currentMode = mode;
      const modal = document.getElementById('productModal');
      const form = document.getElementById('productForm');
      
      // Clear form
      form.reset();
      document.getElementById('productId').value = '';
      document.getElementById('formMethod').value = 'POST';
      document.getElementById('imagePreviewContainer').classList.add('hidden');
      document.getElementById('imagePreview').src = '';

      if (mode === 'create') {
        document.getElementById('modalTitle').textContent = 'Tambah Produk Baru';
        document.getElementById('submitBtn').textContent = 'Simpan Produk';
      } else if (mode === 'edit' && productId) {
        document.getElementById('modalTitle').textContent = 'Edit Produk';
        document.getElementById('submitBtn').textContent = 'Perbarui Produk';
        document.getElementById('productId').value = productId;
        document.getElementById('formMethod').value = 'PUT';
        
        // Show loading state
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').textContent = 'Memuat data...';
        
        // Load product data via AJAX
        fetch(`/produk/${productId}/edit`, {
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
          }
        })
          .then(response => {
            if (!response.ok) throw new Error('Failed to load product data');
            return response.json();
          })
          .then(data => {
            document.getElementById('productTitle').value = data.product.title || '';
            document.getElementById('productCard').value = data.product.card_id || '';
            document.getElementById('productStatus').value = data.product.status || 'active';
            document.getElementById('productDescription').value = data.product.description || '';
            document.getElementById('productLinkShopee').value = data.product.link_shopee || '';
            document.getElementById('productLinkTiktok').value = data.product.link_tiktok || '';
            
            // Show existing image if available
            if (data.product.image_url) {
              document.getElementById('imagePreview').src = data.product.image_url;
              document.getElementById('imagePreviewContainer').classList.remove('hidden');
            }
          })
          .catch(error => {
            console.error('Error loading product:', error);
            showAlertModal('Error', 'Gagal memuat data produk. Silakan coba lagi.', 'error');
            closeProductModal();
          })
          .finally(() => {
            // Restore button state
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('submitBtn').textContent = 'Perbarui Produk';
          });
      }

      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }

    function closeProductModal() {
      const modal = document.getElementById('productModal');
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }

    function deleteProduct(productId) {
      openDeleteConfirmModal(
        'Apakah Anda yakin ingin menghapus produk ini?',
        function() {
          const formData = new FormData();
          formData.append('_method', 'DELETE');
          
          fetch(`/produk/${productId}`, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success || response.ok) {
              showAlertModal('Berhasil', 'Produk berhasil dihapus!', 'success', () => {
                location.reload();
              });
            } else {
              showAlertModal('Error', data.error || 'Gagal menghapus produk', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showAlertModal('Error', 'Terjadi kesalahan saat menghapus', 'error');
          });
        }
      );
    }

    // Image preview
    document.getElementById('productImage').addEventListener('change', (e) => {
      const file = e.target.files[0];
      if (file) {
        // Validate file size (10MB = 10485760 bytes)
        if (file.size > 10485760) {
          closeProductModal();
          setTimeout(() => {
            showAlertModal('Validasi', 'Ukuran file tidak boleh lebih dari 10MB', 'warning', () => {
              openProductModal(currentMode, document.getElementById('productId').value || null);
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

    document.getElementById('productForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      
      const productId = document.getElementById('productId').value;
      let url = '{{ route("produk.store") }}';
      
      const formData = new FormData(document.getElementById('productForm'));
      
      if (currentMode === 'edit' && productId) {
        url = `/produk/${productId}`;
        formData.append('_method', 'PUT');
      }
      
      try {
        const response = await fetch(url, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Accept': 'application/json',
          },
          body: formData
        });

        closeProductModal();
        
        setTimeout(async () => {
          if (response.ok) {
            showAlertModal('Berhasil', 'Produk berhasil disimpan!', 'success', () => {
              location.reload();
            });
          } else {
            const errors = await response.json();
            showAlertModal('Error', errors.message || 'Terjadi kesalahan', 'error');
          }
        }, 300);
      } catch (error) {
        console.error('Error:', error);
        closeProductModal();
        setTimeout(() => {
          showAlertModal('Error', 'Terjadi kesalahan saat menyimpan produk', 'error');
        }, 300);
      }
    });

    // Close modal when clicking outside
    document.getElementById('productModal').addEventListener('click', (e) => {
      if (e.target.id === 'productModal') {
        closeProductModal();
      }
    });
  </script>

</body>
</html>
