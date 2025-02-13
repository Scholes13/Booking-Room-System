@extends('admin.layout')

@section('title', 'Kelola Ruang Meeting')

@section('content')
<div class="space-y-6">
    <!-- Header Section with Back Button -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Kelola Ruang Meeting</h2>
        <a href="{{ route('admin.dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-6">Tambah Ruang Meeting Baru</h3>
        <form action="{{ route('admin.meeting_rooms.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Ruang</label>
                <input type="text" 
                       name="name" 
                       class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                       placeholder="Nama Ruang Meeting" 
                       required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" 
                          class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                          rows="3" 
                          placeholder="Fasilitas / Deskripsi Ruangan"></textarea>
            </div>
            <div>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus"></i>
                    <span>Tambah Ruang</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Ruang</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($rooms as $room)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $room->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $room->description }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <form action="{{ route('admin.meeting_rooms.delete', $room->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Yakin hapus ruang meeting ini?')" 
                                  class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    <i class="fas fa-trash"></i>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection