<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>WG OpsCenter - Login</title>
  
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
  <link
    rel="stylesheet"
    as="style"
    onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
  />
  
  <!-- Favicon -->
  <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#26458e',
            secondary: '#F4EFE6',
            dark: '#1C160C',
            accent: '#26458e',
            danger: '#26458e',
          }
        }
      }
    }
  </script>
  
  <!-- Custom styles -->
  <style>
    body {
      font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;
      background-color: #f5f8ff;
      background-image: radial-gradient(circle at 80% 20%, rgba(38, 69, 142, 0.08), transparent 40%), 
                        radial-gradient(circle at 20% 80%, rgba(38, 69, 142, 0.05), transparent 40%);
      background-attachment: fixed;
    }
    
    .login-card {
      background-color: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border: 1px solid rgba(255, 255, 255, 0.8);
      border-radius: 8px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
      width: 100%;
      max-width: 440px;
      padding: 2rem;
    }
    
    .form-input {
      background-color: rgba(245, 247, 250, 0.9);
      border: 1px solid rgba(38, 69, 142, 0.2);
      color: #333;
      border-radius: 4px;
      padding: 0.5rem 0.75rem 0.5rem 2.5rem;
      width: 100%;
      transition: all 0.2s;
    }
    
    .form-input::placeholder {
      color: rgba(100, 116, 139, 0.6);
    }
    
    .form-input:focus {
      outline: none;
      border-color: rgba(38, 69, 142, 0.5);
      box-shadow: 0 0 0 2px rgba(38, 69, 142, 0.2);
    }
    
    .btn {
      padding: 0.5rem 1rem;
      border-radius: 4px;
      font-weight: 500;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .btn-primary {
      background-color: #26458e;
      color: white;
      width: 100%;
    }
    
    .btn-primary:hover {
      background-color: #1c3a7a;
    }
    
    .input-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: rgba(38, 69, 142, 0.6);
      font-size: 0.875rem;
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen">
  <div class="login-card">
    <div class="text-center mb-8">
      <!-- Logo -->
      <div class="flex justify-center">
        <img src="{{ asset('images/logo.png') }}" alt="WG OpsCenter Logo" class="w-16 h-16 mb-2">
      </div>
      <h1 class="text-2xl font-bold text-gray-800">WG OpsCenter</h1>
      <p class="text-gray-600 text-sm mt-1">Login to access your dashboard</p>
    </div>

    @if(session('error'))
      <div class="mb-6 text-red-600 bg-red-100 p-3 rounded text-sm text-center border border-red-200">
        <i class="fas fa-exclamation-circle mr-1"></i>{{ session('error') }}
      </div>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST">
      @csrf

      <!-- Username / Email -->
      <div class="mb-4">
        <label class="block mb-2 text-gray-700 text-sm">Username / Email</label>
        <div class="relative">
          <i class="fas fa-user input-icon"></i>
          <input 
            type="text" 
            name="login" 
            class="form-input" 
            placeholder="Enter your username or email" 
            required
          >
        </div>
      </div>

      <!-- Password -->
      <div class="mb-5">
        <label class="block mb-2 text-gray-700 text-sm">Password</label>
        <div class="relative">
          <i class="fas fa-lock input-icon"></i>
          <input 
            type="password" 
            name="password" 
            class="form-input" 
            placeholder="Enter your password" 
            required
          >
        </div>
      </div>

      <!-- Remember Me -->
      <div class="flex items-center mb-6">
        <input 
          type="checkbox" 
          name="remember" 
          id="remember" 
          class="rounded border-gray-300 text-primary focus:ring-primary"
        >
        <label for="remember" class="text-gray-700 text-sm ml-2 cursor-pointer">Remember me</label>
      </div>

      <!-- Login Button -->
      <div>
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-sign-in-alt mr-2"></i> Login
        </button>
      </div>
    </form>
    
    <div class="mt-8 text-center text-gray-500 text-xs">
      &copy; {{ date('Y') }} WG OpsCenter. All rights reserved.
    </div>
  </div>

  <!-- Font Awesome for Icons -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>
</body>
</html>
