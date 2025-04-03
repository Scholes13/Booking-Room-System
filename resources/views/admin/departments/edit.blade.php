@extends('admin.layout')

@section('title', 'Edit Departemen')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Edit Departemen</h1>
        <a href="{{ route('admin.departments') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-secondary text-dark gap-2 text-sm font-bold leading-normal tracking-[0.015em]">
            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                <path d="M224,128a8,8,0,0,1-8,8H59.31l58.35,58.34a8,8,0,0,1-11.32,11.32l-72-72a8,8,0,0,1,0-11.32l72-72a8,8,0,0,1,11.32,11.32L59.31,120H216A8,8,0,0,1,224,128Z"></path>
            </svg>
            Kembali
        </a>
    </div>

    <div class="flex flex-col gap-6 bg-white rounded-lg p-6">
        <form action="{{ route('admin.departments.update', $department->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-2">
                <label for="name" class="block text-sm font-medium text-dark">Nama Departemen</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ $department->name }}"
                    class="w-full p-3 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary" 
                    placeholder="Masukkan nama departemen"
                    required
                />
                @error('name')
                    <p class="text-xs text-danger">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="flex items-center justify-center rounded-full px-6 py-3 bg-primary text-white font-bold">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 