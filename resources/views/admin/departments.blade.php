@extends('admin.layout')

@section('title', 'Kelola Departemen')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Kelola Departemen</h2>
        <a href="{{ route('admin.dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Departemen Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Total Departemen</h3>
            <p class="text-4xl font-bold text-blue-600">{{ $departments->count() }}</p>
        </div>

        <!-- Tambah Departemen Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Tambah Departemen</h3>
            
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
            @endif

            <form action="{{ route('admin.departments.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Departemen</label>
                    <input type="text" 
                           name="name" 
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           placeholder="Masukkan Nama Departemen" 
                           required>
                </div>
                <button type="submit" 
                        class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Departemen</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Daftar Departemen Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-xl font-semibold text-gray-800">Daftar Departemen</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Departemen</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($departments as $department)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $department->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $department->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.departments.edit', $department->id) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <form action="{{ route('admin.departments.delete', $department->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Yakin ingin menghapus departemen ini?');" 
                                      class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                        <i class="fas fa-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                            <div class="flex flex-col items-center py-8">
                                <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada departemen</p>
                                <p class="text-sm text-gray-400">Silakan tambahkan departemen baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection