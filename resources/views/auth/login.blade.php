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

    @if ($errors->any())
      <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="space-y-6">
      @csrf
      
      <!-- Email -->
      <div>
        <label for="email" class="block text-sm text-gray-700 mb-1">Email</label>
        <input type="email" id="email" name="email" required placeholder="example@email.com"
               value="{{ old('email') }}"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition duration-200 @error('email') border-red-500 @enderror" />
        @error('email')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-sm text-gray-700 mb-1">Kata Sandi</label>
        <input type="password" id="password" name="password" required placeholder="••••••••"
               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-black focus:border-black transition duration-200 @error('password') border-red-500 @enderror" />
        @error('password')
          <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
        @enderror
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
