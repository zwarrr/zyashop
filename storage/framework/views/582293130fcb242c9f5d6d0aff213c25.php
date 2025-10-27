<!DOCTYPE html>
<html lang="en" class="bg-white">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo e(isset($pageTitle) ? $pageTitle : (isset($cardTitle) ? $cardTitle : 'Products')); ?> - Zya's Placeshop</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Poppins Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
    .product-card {
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .product-card:active {
      transform: scale(0.98);
    }
    .sticky-header {
      position: sticky;
      top: 0;
      background-color: white;
      z-index: 10;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }
  </style>
</head>
<body class="bg-white text-black">

  <!-- Main Wrapper -->
  <main class="max-w-full sm:max-w-2xl md:max-w-4xl mx-auto px-3 sm:px-4">

    <!-- Include Header Component -->
    <?php echo $__env->make('partials.header', [
      'pageTitle' => $pageTitle ?? ($cardTitle ?? 'Products'),
      'productType' => $productType ?? null
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Products Grid -->
    <section class="grid grid-cols-2 gap-3 sm:gap-6 mb-20">
      
      <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <!-- Product/Card Item -->
      <?php if(isset($isCards) && $isCards): ?>
        <!-- Card Item (from cards table) -->
        <a href="javascript:void(0)" class="product-card rounded-lg overflow-hidden border border-gray-200 hover:border-black transition-colors" data-product="<?php echo e($product->id); ?>">
          <div class="relative bg-gray-300 overflow-hidden" style="aspect-ratio: 1;">
            <?php if($product->image): ?>
              <img src="<?php echo e(asset('storage/cards/' . $product->image)); ?>" alt="<?php echo e($product->title); ?>" class="w-full h-full object-cover">
            <?php else: ?>
              <img src="https://placehold.co/1080x1080?text=<?php echo e(urlencode($product->title)); ?>" alt="<?php echo e($product->title); ?>" class="w-full h-full object-cover">
            <?php endif; ?>
            <div class="absolute top-2 right-2 bg-black text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
              <?php echo e($product->status === 'active' ? 'Tersedia' : 'Tidak Tersedia'); ?>

            </div>
          </div>
          <div class="p-2 sm:p-4">
            <h3 class="font-bold text-sm sm:text-lg mb-1"><?php echo e($product->title); ?></h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-2 line-clamp-2"><?php echo e($product->description ?? '-'); ?></p>
            <div class="flex items-center justify-between">
              <span class="text-xs bg-gray-100 px-2 py-1 rounded"><?php echo e($product->category); ?></span>
            </div>
          </div>
        </a>
      <?php else: ?>
        <!-- Product Item (from products table) -->
        <?php
          $productLink = '#';
          if (isset($productType)) {
            $productLink = $productType === 'shopee' ? ($product->link_shopee ?? '#') : ($product->link_tiktok ?? '#');
          } else {
            $productLink = route('product.show', $product->id);
          }
        ?>
        <a href="<?php echo e($productLink); ?>" 
           target="<?php echo e(isset($productType) ? '_blank' : '_self'); ?>"
           class="product-card rounded-lg overflow-hidden border border-gray-200 hover:border-black transition-colors" 
           data-product="<?php echo e($product->id); ?>">
          <div class="relative bg-gray-300 overflow-hidden" style="aspect-ratio: 1;">
            <?php if($product->image_url): ?>
              <img src="<?php echo e($product->image_url); ?>" alt="<?php echo e($product->title); ?>" class="w-full h-full object-cover">
            <?php else: ?>
              <img src="https://placehold.co/400x400?text=<?php echo e(urlencode($product->title)); ?>" alt="<?php echo e($product->title); ?>" class="w-full h-full object-cover">
            <?php endif; ?>
            <div class="absolute top-2 right-2 bg-black text-white px-2 sm:px-3 py-1 rounded-full text-xs font-semibold">
              <?php if($product->status === 'coming_soon'): ?>
                Coming Soon
              <?php elseif($product->status === 'active'): ?>
                Available
              <?php else: ?>
                Inactive
              <?php endif; ?>
            </div>
          </div>
          <div class="p-2 sm:p-4">
            <h3 class="font-bold text-sm sm:text-lg mb-1"><?php echo e($product->title); ?></h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-2 line-clamp-2"><?php echo e($product->description ?? 'Premium digital content'); ?></p>
            <div class="flex items-center justify-between">
              <?php if($product->card): ?>
              <span class="text-xs bg-gray-100 px-2 py-1 rounded"><?php echo e($product->card->title); ?></span>
              <?php else: ?>
              <span class="text-xs bg-gray-100 px-2 py-1 rounded">No Card</span>
              <?php endif; ?>
              
              <?php if(isset($productType)): ?>
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
              </svg>
              <?php endif; ?>
            </div>
          </div>
        </a>
      <?php endif; ?>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="col-span-2 text-center py-20">
        <p class="text-gray-500 text-lg">Belum ada <?php echo e(isset($isCards) && $isCards ? 'cards' : 'produk'); ?> tersedia</p>
        <a href="/" class="text-blue-600 hover:text-blue-800 mt-4 inline-block">Kembali ke Home</a>
      </div>
      <?php endif; ?>

    </section>

  </main>

  <!-- Load Coming Soon Modal (jika card tidak punya produk) -->
  <?php echo $__env->make('partials.modal_information_product_coomingsoon', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

  <script>
    // Cek apakah card tidak punya produk, jika iya tampilkan modal
    <?php if(isset($hasNoProducts) && $hasNoProducts): ?>
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        const modal = document.getElementById('comingSoonModal');
        const cardTitle = document.getElementById('productRange');
        
        if (modal && cardTitle) {
          cardTitle.textContent = '<?php echo e($cardTitle ?? "This Card"); ?>';
          modal.classList.remove('hidden');
          document.body.style.overflow = 'hidden';
        }
      }, 500);
    });
    <?php endif; ?>

    function closeComingSoonModal() {
      const modal = document.getElementById('comingSoonModal');
      if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        // Redirect ke home setelah modal ditutup
        window.location.href = '/';
      }
    }

    // Search functionality
    const searchInput = document.getElementById('searchProducts');
    const productCards = document.querySelectorAll('[data-product]');

    if (searchInput) {
      searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        productCards.forEach(card => {
          const title = card.querySelector('h3');
          const description = card.querySelector('p');
          if (title && description) {
            if (title.textContent.toLowerCase().includes(searchTerm) || description.textContent.toLowerCase().includes(searchTerm)) {
              card.style.display = '';
            } else {
              card.style.display = 'none';
            }
          }
        });
      });
    }
  </script>

</body>
</html>
<?php /**PATH C:\GOW\zyashop\resources\views/sections/product.blade.php ENDPATH**/ ?>