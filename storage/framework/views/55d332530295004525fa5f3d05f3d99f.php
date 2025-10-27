<!-- Sidebar -->
<aside class="w-64 bg-white shadow-lg hidden md:block fixed left-0 top-0 h-screen overflow-y-auto border-r border-gray-200 z-50">
  <div class="p-6 border-b border-gray-200">
    <h2 class="text-2xl font-bold text-black">Zya's Shop</h2>
    <p class="text-xs text-gray-500 mt-1">Admin Panel</p>
  </div>
  <nav class="p-4 space-y-2">
    <a href="<?php echo e(route('dashboard')); ?>" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 <?php echo e(request()->routeIs('dashboard') ? 'bg-gray-100 text-black font-semibold border-l-4 border-black' : 'text-gray-600 hover:bg-gray-100 hover:text-black hover:border-l-4 hover:border-gray-300'); ?>">
      <svg class="feather" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
      <span>Dashboard</span>
    </a>
    <a href="<?php echo e(route('produk')); ?>" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 <?php echo e(request()->routeIs('produk') ? 'bg-gray-100 text-black font-semibold border-l-4 border-black' : 'text-gray-600 hover:bg-gray-100 hover:text-black hover:border-l-4 hover:border-gray-300'); ?>">
      <svg class="feather" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
      <span>Produk</span>
    </a>
    <a href="<?php echo e(route('laporan')); ?>" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 <?php echo e(request()->routeIs('laporan') ? 'bg-gray-100 text-black font-semibold border-l-4 border-black' : 'text-gray-600 hover:bg-gray-100 hover:text-black hover:border-l-4 hover:border-gray-300'); ?>">
      <svg class="feather" viewBox="0 0 24 24"><polyline points="23 6 13.5 15.5 8.5 10.5 1 17"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
      <span>Laporan</span>
    </a>
    <a href="<?php echo e(route('kategori')); ?>" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 <?php echo e(request()->routeIs('kategori') ? 'bg-gray-100 text-black font-semibold border-l-4 border-black' : 'text-gray-600 hover:bg-gray-100 hover:text-black hover:border-l-4 hover:border-gray-300'); ?>">
      <svg class="feather" viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
      <span>Kategori</span>
    </a>
    <a href="<?php echo e(route('cards')); ?>" class="nav-link flex items-center gap-3 px-4 py-3 rounded-lg transition duration-200 <?php echo e(request()->routeIs('cards') ? 'bg-gray-100 text-black font-semibold border-l-4 border-black' : 'text-gray-600 hover:bg-gray-100 hover:text-black hover:border-l-4 hover:border-gray-300'); ?>">
      <svg class="feather" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
      <span>Cards</span>
    </a>
  </nav>
</aside>

<!-- Mobile Header -->
<header class="fixed md:hidden top-0 left-0 right-0 bg-white shadow px-4 py-3 flex items-center justify-between border-b border-gray-200 z-40">
  <h1 class="text-lg font-semibold text-black">Zya's Shop</h1>
  <button class="p-2 hover:bg-gray-100 rounded-lg">
    <svg class="feather" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
  </button>
</header>

<!-- Desktop Header -->
<header class="fixed hidden md:flex top-0 left-64 right-0 bg-white shadow px-6 py-4 items-center justify-between border-b border-gray-200 z-40">
  <div>
    <h1 class="text-xl font-semibold text-black"><?php echo e(request()->routeIs('dashboard') ? 'Dashboard' : (request()->routeIs('produk') ? 'Produk' : (request()->routeIs('laporan') ? 'Laporan' : (request()->routeIs('kategori') ? 'Kategori' : (request()->routeIs('cards') ? 'Cards' : (request()->routeIs('profile') ? 'Profile' : 'Admin')))))); ?></h1>
    <p class="text-xs text-gray-500 mt-1"><?php echo e(request()->routeIs('dashboard') ? 'Kelola toko Zya\'s Placeshop' : (request()->routeIs('produk') ? 'Kelola semua produk toko Zya\'s Placeshop' : (request()->routeIs('laporan') ? 'Analisis dan pantau performa toko Zya\'s Placeshop' : (request()->routeIs('kategori') ? 'Kelola kategori produk' : (request()->routeIs('cards') ? 'Kelola cards dan banner' : (request()->routeIs('profile') ? 'Kelola profile, foto, bio, dan links' : 'Admin Panel')))))); ?></p>
  </div>
  <div class="flex items-center gap-4">
    <div class="relative group">
      <button class="flex items-center gap-3 p-2 hover:bg-gray-100 rounded-lg transition duration-200">
        <div class="text-right">
          <p class="text-sm font-medium text-gray-800"><?php echo e(Auth::user()->name); ?></p>
          <p class="text-xs text-gray-500"><?php echo e(Auth::user()->email); ?></p>
        </div>
        <div class="w-10 h-10 rounded-full bg-black flex items-center justify-center text-white font-semibold text-sm">
          <?php echo e(substr(Auth::user()->name, 0, 1)); ?>

        </div>
      </button>
      <!-- Dropdown Menu -->
      <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200 z-50">
        <div class="px-4 py-3 border-b border-gray-200">
          <p class="text-sm font-medium text-gray-800"><?php echo e(Auth::user()->name); ?></p>
          <p class="text-xs text-gray-500"><?php echo e(Auth::user()->email); ?></p>
        </div>
        <a href="<?php echo e(route('profile')); ?>" class="flex items-center gap-2 px-4 py-3 text-gray-700 hover:bg-gray-50 text-sm transition duration-200">
          <svg class="feather" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          Profile
        </a>
        <form method="POST" action="<?php echo e(route('logout')); ?>">
          <?php echo csrf_field(); ?>
          <button type="submit" class="w-full flex items-center gap-2 px-4 py-3 text-red-600 hover:bg-red-50 text-sm transition duration-200">
            <svg class="feather" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Logout
          </button>
        </form>
      </div>
    </div>
  </div>
</header>
<?php /**PATH C:\GOW\zyashop\resources\views/admin/bar.blade.php ENDPATH**/ ?>