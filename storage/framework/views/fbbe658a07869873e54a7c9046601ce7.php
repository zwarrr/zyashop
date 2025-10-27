<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Laporan - Zya's Placeshop Admin</title>

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

  <?php echo $__env->make('partials.ajax_loader', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <!-- Wrapper -->
  <div class="flex min-h-screen">

    <?php echo $__env->make('admin.bar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col md:ml-64">

      <!-- Content -->
      <main class="flex-1 mt-16 md:mt-20 p-4 md:p-8 space-y-8">
        
        <!-- Header Section -->
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-2xl font-bold text-black">Laporan</h2>
            <p class="text-sm text-gray-500 mt-1">Statistik dan ringkasan data toko Zya's Placeshop</p>
          </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Card 1: Total Produk -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Total Produk</h3>
                <p class="text-3xl font-bold text-black mt-2"><?php echo e(Auth::user()->products()->count()); ?></p>
                <p class="text-xs text-gray-400 mt-2">Produk terdaftar</p>
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
                <p class="text-3xl font-bold text-black mt-2"><?php echo e(Auth::user()->cards()->count()); ?></p>
                <p class="text-xs text-gray-400 mt-2">Cards aktif</p>
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
                <p class="text-3xl font-bold text-black mt-2"><?php echo e(Auth::user()->categories()->count()); ?></p>
                <p class="text-xs text-gray-400 mt-2">Kategori terdaftar</p>
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
                <p class="text-3xl font-bold text-black mt-2"><?php echo e(Auth::user()->products()->where('status', 'active')->count()); ?></p>
                <p class="text-xs text-gray-400 mt-2">Sedang dijual</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Product Status Breakdown -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
          <!-- Produk Aktif -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Status: Active</h3>
                <p class="text-3xl font-bold text-black mt-2"><?php echo e(Auth::user()->products()->where('status', 'active')->count()); ?></p>
                <p class="text-xs text-gray-400 mt-2">Produk aktif</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </div>
            </div>
          </div>

          <!-- Produk Coming Soon -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Status: Coming Soon</h3>
                <p class="text-3xl font-bold text-black mt-2"><?php echo e(Auth::user()->products()->where('status', 'coming_soon')->count()); ?></p>
                <p class="text-xs text-gray-400 mt-2">Segera hadir</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
              </div>
            </div>
          </div>

          <!-- Produk Inactive -->
          <div class="bg-white rounded-xl p-6 shadow hover:shadow-lg transition duration-300 border-l-4 border-black">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="text-sm text-gray-500 font-medium">Status: Inactive</h3>
                <p class="text-3xl font-bold text-black mt-2"><?php echo e(Auth::user()->products()->where('status', 'inactive')->count()); ?></p>
                <p class="text-xs text-gray-400 mt-2">Tidak aktif</p>
              </div>
              <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                <svg class="feather text-black" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Products Table -->
        <div class="bg-white rounded-xl shadow border border-gray-200 overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-black">Daftar Produk Terbaru</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="w-full">
              <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Produk</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Card</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                  <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Tanggal Dibuat</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = Auth::user()->products()->latest()->take(10)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 transition duration-200">
                  <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                      <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0">
                        <?php if($product->image_url): ?>
                          <img src="<?php echo e($product->image_url); ?>" alt="<?php echo e($product->title); ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                          <svg class="feather text-gray-600" style="width:20px;height:20px;" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
                        <?php endif; ?>
                      </div>
                      <span class="text-sm text-gray-700 font-medium"><?php echo e($product->title); ?></span>
                    </div>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($product->card ? $product->card->title : '-'); ?></td>
                  <td class="px-6 py-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                      <?php if($product->status === 'active'): ?> bg-green-100 text-green-700
                      <?php elseif($product->status === 'inactive'): ?> bg-gray-100 text-gray-700
                      <?php else: ?> bg-yellow-100 text-yellow-700
                      <?php endif; ?>">
                      <?php echo e(ucfirst($product->status)); ?>

                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-700"><?php echo e($product->created_at->format('d M Y')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                  <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                    Belum ada produk terdaftar
                  </td>
                </tr>
                <?php endif; ?>
              </tbody>
            </table>
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
<?php /**PATH C:\GOW\zyashop\resources\views/admin/laporan.blade.php ENDPATH**/ ?>