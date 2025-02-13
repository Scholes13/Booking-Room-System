@extends('admin.layout')

@section('title', 'Edit Karyawan')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Edit Karyawan</h2>
        <a href="{{ route('admin.employees') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali</span>
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center gap-2">
            <i class="fas fa-exclamation-circle text-red-500"></i>
            <ul class="text-red-700">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.employees.update', $employee->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Karyawan</label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $employee->name) }}"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select name="gender" 
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                            required>
                        <option value="L" {{ old('gender', $employee->gender) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender', $employee->gender) === 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                    <select name="department_id" 
                            class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                            required>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" 
                                    {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                    <input type="text" 
                           name="position" 
                           value="{{ old('position', $employee->position) }}"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.employees') }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times"></i>
                    <span>Batal</span>
                </a>
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection