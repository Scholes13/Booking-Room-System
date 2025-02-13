<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 to-blue-900 flex items-center justify-center min-h-screen">
  <div class="bg-white/10 backdrop-blur-lg p-8 rounded-xl shadow-xl w-full max-w-md">
    <h1 class="text-2xl font-bold text-center text-white mb-6">
      🔒 Admin Login
    </h1>

    @if(session('error'))
      <div class="mb-4 text-red-500 bg-red-500/20 p-3 rounded-md text-center">
        ⚠️ {{ session('error') }}
      </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST">
      @csrf

      <!-- Username -->
      <div class="mb-4">
        <label class="block mb-1 text-gray-300">Username</label>
        <div class="relative">
          <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          <input type="text" name="username" class="w-full pl-10 pr-3 py-2 bg-white/20 text-white border border-white/30 rounded focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Enter your username" required>
        </div>
      </div>

      <!-- Password -->
      <div class="mb-6">
        <label class="block mb-1 text-gray-300">Password</label>
        <div class="relative">
          <i class="fas fa-lock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          <input type="password" name="password" class="w-full pl-10 pr-3 py-2 bg-white/20 text-white border border-white/30 rounded focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Enter your password" required>
        </div>
      </div>

      <!-- Remember Me & Forgot Password -->
      <div class="flex items-center justify-between text-gray-300 text-sm mb-4">
        <label class="flex items-center">
          <input type="checkbox" name="remember" class="mr-2 rounded">
          Remember me
        </label>
      </div>

      <!-- Submit Button -->
      <button type="submit" class="w-full bg-gradient-to-r from-yellow-500 to-orange-500 text-white py-2 rounded-lg hover:scale-105 transition transform duration-300">
        Login
      </button>

      <!-- Back to Home Button -->
      <a href="/" class="block text-center w-full mt-4 text-white bg-gradient-to-r from-gray-700 to-gray-900 py-2 rounded-lg flex items-center justify-center space-x-2 hover:scale-105 transition transform duration-300">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Home</span>
      </a>
    </form>
  </div>

  <!-- Font Awesome for Icons -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
</body>
</html>
