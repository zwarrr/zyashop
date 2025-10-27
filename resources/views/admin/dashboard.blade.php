<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard - Zya's Placeshop Admin</title>

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
  </style>
</head>

<body class="bg-gray-50 text-gray-800">

  <!-- Wrapper -->
  <div class="flex min-h-screen">

    @include('admin.bar')

    <!-- Main Content -->
    <div class="flex-1 flex flex-col md:ml-64">

      <!-- Content -->
      <main class="flex-1 mt-16 md:mt-20 p-4 md:p-8 space-y-8">
        
        <!-- Welcome Section -->
        <div class="bg-black rounded-xl p-6 md:p-8 text-white shadow-lg">
          <h2 class="text-3xl font-bold mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h2>
          <p class="text-gray-300">Kelola semua aspek toko Zya's Placeshop dari dashboard admin ini.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Card 1: Total Produk -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Total Produk</h3>
                <p class="text-3xl font-bold text-black mt-2">{{ $totalProducts }}</p>
                <p class="text-xs text-gray-400 mt-2">Produk tersedia</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
              </div>
            </div>
          </div>

          <!-- Card 2: Total Cards -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Total Cards</h3>
                <p class="text-3xl font-bold text-black mt-2">{{ $totalCards }}</p>
                <p class="text-xs text-gray-400 mt-2">Cards terdaftar</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
              </div>
            </div>
          </div>

          <!-- Card 3: Total Kategori -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Total Kategori</h3>
                <p class="text-3xl font-bold text-black mt-2">{{ $totalCategories }}</p>
                <p class="text-xs text-gray-400 mt-2">Kategori aktif</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
              </div>
            </div>
          </div>

          <!-- Card 4: Produk Aktif -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Produk Aktif</h3>
                <p class="text-3xl font-bold text-black mt-2">{{ $activeProducts }}</p>
                <p class="text-xs text-gray-400 mt-2">Sedang dijual</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Additional Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <!-- Produk Coming Soon -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Produk Coming Soon</h3>
                <p class="text-3xl font-bold text-black mt-2">{{ $comingSoonProducts }}</p>
                <p class="text-xs text-gray-400 mt-2">Segera hadir</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
            </div>
          </div>

          <!-- Produk Tidak Aktif -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Produk Tidak Aktif</h3>
                <p class="text-3xl font-bold text-black mt-2">{{ $inactiveProducts }}</p>
                <p class="text-xs text-gray-400 mt-2">Tidak dijual</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Info Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Info -->
          <div class="lg:col-span-2 bg-white p-6 rounded-xl shadow border border-gray-200">
            <div class="flex items-center gap-2 mb-4">
              <svg class="feather text-black" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"></path><path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"></path></svg>
              <h2 class="text-lg font-bold text-black">Informasi Toko</h2>
            </div>
            <div class="space-y-4">
              <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <span class="text-gray-600">Nama Toko</span>
                <span class="font-semibold text-black">Zya's Placeshop</span>
              </div>
              <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                <span class="text-gray-600">Admin</span>
                <span class="font-semibold text-black">{{ Auth::user()->name }}</span>
              </div>
              <div class="flex items-center justify-between pb-4">
                <span class="text-gray-600">Email</span>
                <span class="font-semibold text-black">{{ Auth::user()->email }}</span>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white p-6 rounded-xl shadow border border-gray-200">
            <div class="flex items-center gap-2 mb-4">
              <svg class="feather text-black" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
              <h2 class="text-lg font-bold text-black">Aksi Cepat</h2>
            </div>
            <div class="space-y-3">
              <a href="{{ route('produk') }}" class="flex items-center justify-center gap-2 w-full bg-black hover:bg-gray-900 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="feather" viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                Kelola Produk
              </a>
              <a href="{{ route('cards') }}" class="flex items-center justify-center gap-2 w-full bg-gray-200 hover:bg-gray-300 text-black px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="feather" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
                Kelola Cards
              </a>
              <a href="{{ route('kategori') }}" class="flex items-center justify-center gap-2 w-full bg-gray-200 hover:bg-gray-300 text-black px-4 py-2 rounded-lg font-medium transition duration-200">
                <svg class="feather" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                Kelola Kategori
              </a>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>

  <script>
    feather.replace();
  </script>

</body>
</html>
