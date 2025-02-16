<!DOCTYPE html>
<html>
<head>
  <title>Superadmin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 to-blue-900 min-h-screen text-white">

  <!-- Header / Navbar (opsional) -->
  <nav class="bg-white/10 backdrop-blur-md p-4 mb-6">
    <div class="container mx-auto flex justify-between items-center">
      <h1 class="text-xl font-bold">Superadmin Dashboard</h1>
      <!-- Logout Button -->
      <form action="{{ route('admin.logout') }}" method="GET">
        @csrf
        <button type="submit" class="bg-red-600 px-4 py-2 rounded">
          Logout
        </button>
      </form>
    </div>
  </nav>

  <div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Welcome, Superadmin!</h2>
    <p class="mb-4">Anda memiliki akses khusus untuk membuat Admin baru.</p>

    <!-- Tombol "Add Admin" -->
    <a 
      href="{{ route('superadmin.createAdmin') }}" 
      class="inline-block bg-green-600 px-4 py-2 rounded hover:bg-green-500 transition"
    >
      ➕ Add Admin
    </a>
  </div>

</body>
</html>
