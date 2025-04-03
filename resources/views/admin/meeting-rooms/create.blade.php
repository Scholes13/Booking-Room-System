@extends('admin.layout')

@section('title', 'Tambah Ruang Meeting')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Tambah Ruang Meeting</h1>
        <a href="{{ route('admin.meeting_rooms') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-secondary text-dark gap-2 text-sm font-bold leading-normal tracking-[0.015em]">
            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                <path d="M224,128a8,8,0,0,1-8,8H59.31l58.35,58.34a8,8,0,0,1-11.32,11.32l-72-72a8,8,0,0,1,0-11.32l72-72a8,8,0,0,1,11.32,11.32L59.31,120H216A8,8,0,0,1,224,128Z"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="flex flex-col gap-6 bg-white rounded-lg p-6">
        <form action="{{ route('admin.meeting_rooms.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-dark">Nama Ruang</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="w-full p-3 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary" 
                        placeholder="Masukkan nama ruang meeting"
                        required
                    />
                    @error('name')
                        <p class="text-xs text-danger">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-2">
                    <label for="capacity" class="block text-sm font-medium text-dark">Kapasitas</label>
                    <input 
                        type="number" 
                        id="capacity" 
                        name="capacity" 
                        min="1"
                        class="w-full p-3 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary" 
                        placeholder="Jumlah kapasitas orang"
                    />
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="description" class="block text-sm font-medium text-dark">Deskripsi</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    class="w-full p-3 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary" 
                    placeholder="Deskripsi ruangan, fasilitas, dll"
                ></textarea>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-dark">Fasilitas</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex items-center">
                        <input id="projector" type="checkbox" name="facilities[]" value="projector" class="h-4 w-4 text-primary focus:ring-primary">
                        <label for="projector" class="ml-2 text-sm text-dark">Proyektor</label>
                    </div>
                    <div class="flex items-center">
                        <input id="whiteboard" type="checkbox" name="facilities[]" value="whiteboard" class="h-4 w-4 text-primary focus:ring-primary">
                        <label for="whiteboard" class="ml-2 text-sm text-dark">Papan Tulis</label>
                    </div>
                    <div class="flex items-center">
                        <input id="ac" type="checkbox" name="facilities[]" value="ac" class="h-4 w-4 text-primary focus:ring-primary">
                        <label for="ac" class="ml-2 text-sm text-dark">AC</label>
                    </div>
                    <div class="flex items-center">
                        <input id="wifi" type="checkbox" name="facilities[]" value="wifi" class="h-4 w-4 text-primary focus:ring-primary">
                        <label for="wifi" class="ml-2 text-sm text-dark">Wi-Fi</label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="flex items-center justify-center rounded-full px-6 py-3 bg-primary text-white font-bold">
                    Simpan Ruang Meeting
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 