<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Aplikasi Anda</title>

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Google Font: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />

  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

  <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-8 sm:p-10">
    <div class="mb-8 text-center">
      <h1 class="text-3xl font-semibold text-gray-800 mb-2">Selamat Datang</h1>
      <p class="text-sm text-gray-500">Silakan masuk untuk melanjutkan</p>
    </div>

    <?php if($errors->any()): ?>
      <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
        <?php echo e($errors->first()); ?>

      </div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('login.post')); ?>" class="space-y-6">
      <?php echo csrf_field(); ?>
      
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" required placeholder="example@email.com"
               value="<?php echo e(old('email')); ?>"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition duration-200 <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm text-gray-700 mb-1">Kata Sandi</label>
        <input type="password" id="password" name="password" required placeholder="••••••••"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition duration-200 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" />
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <!-- Tombol Login -->
      <div>
        <button type="submit"
                class="w-full bg-black text-white py-3 rounded-lg font-medium hover:bg-gray-900 transition duration-200">
          Login
        </button>
      </div>
    </form>
  </div>

</body>
</html>
<?php /**PATH C:\GOW\zyashop\resources\views/auth/login.blade.php ENDPATH**/ ?>