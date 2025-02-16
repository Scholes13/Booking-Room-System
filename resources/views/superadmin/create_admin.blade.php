<!DOCTYPE html>
<html>
<head>
  <title>Add New Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-gray-900 to-blue-900 min-h-screen text-white">

  <div class="container mx-auto p-4 mt-10">
    <h2 class="text-2xl font-bold mb-4">Add New Admin</h2>

    <!-- Pesan sukses -->
    @if(session('success'))
      <div class="mb-4 bg-green-600/20 p-3 rounded">
        {{ session('success') }}
      </div>
    @endif

    <!-- Form pembuatan admin -->
    <form action="{{ route('superadmin.storeAdmin') }}" method="POST">
      @csrf
      <div class="mb-4">
        <label class="block mb-1">Name</label>
        <input 
          type="text" 
          name="name" 
          class="w-full p-2 rounded text-black" 
          required
        >
      </div>

      <div class="mb-4">
        <label class="block mb-1">Email</label>
        <input 
          type="email" 
          name="email" 
          class="w-full p-2 rounded text-black" 
          required
        >
      </div>

      <div class="mb-4">
        <label class="block mb-1">Password</label>
        <input 
          type="password" 
          name="password" 
          class="w-full p-2 rounded text-black" 
          required
        >
      </div>

      <button 
        type="submit" 
        class="bg-green-600 px-4 py-2 rounded hover:bg-green-500 transition"
      >
        Save Admin
      </button>
    </form>
  </div>

</body>
</html>
