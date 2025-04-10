@extends('admin_bas.layout')

@section('title', 'Edit Aktivitas')

@section('content')
<div class="flex flex-col h-full">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Edit Aktivitas</h1>
        <a href="{{ route('bas.activities.index') }}" class="inline-flex items-center justify-center text-bas bg-secondary hover:bg-opacity-90 py-2 px-4 rounded-md font-semibold text-sm">
            <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-border p-6">
        <form action="{{ route('bas.activities.update', $activity->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="mb-6">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Pembuat <span class="text-red-500">*</span></label>
                <select name="name" id="name" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" required>
                    <option value="">Pilih Pembuat</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->name }}" {{ old('name', $activity->name) == $employee->name ? 'selected' : '' }}>{{ $employee->name }}</option>
                    @endforeach
                </select>
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="start_datetime" class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_datetime" id="start_datetime" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('start_datetime') border-red-500 @enderror" value="{{ old('start_datetime', $activity->start_datetime ? date('Y-m-d\TH:i', strtotime($activity->start_datetime)) : '') }}" required>
                    @error('start_datetime')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="end_datetime" class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="end_datetime" id="end_datetime" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('end_datetime') border-red-500 @enderror" value="{{ old('end_datetime', $activity->end_datetime ? date('Y-m-d\TH:i', strtotime($activity->end_datetime)) : '') }}" required>
                    @error('end_datetime')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                    <select name="department_id" id="department_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('department_id') border-red-500 @enderror">
                        <option value="">Pilih Departemen</option>
                        @foreach(App\Models\Department::all() as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $activity->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Aktivitas</label>
                    <select name="activity_type" id="activity_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('activity_type') border-red-500 @enderror">
                        <option value="">Pilih Jenis Aktivitas</option>
                        @foreach($activityTypes as $type)
                            <option value="{{ $type->name }}" {{ old('activity_type', $activity->activity_type) == $type->name ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                    @error('activity_type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div id="other_activity_type_container" style="{{ old('activity_type', $activity->activity_type) == 'Other' ? 'display: block;' : 'display: none;' }}">
                    <label for="other_activity_type" class="block text-sm font-medium text-gray-700 mb-1">Jelaskan Jenis Aktivitas Lainnya</label>
                    <input type="text" name="other_activity_type" id="other_activity_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('other_activity_type', $activity->other_activity_type ?? '') }}">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="province" class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                    <select name="province" id="province" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('province') border-red-500 @enderror">
                        <option value="">Pilih Provinsi</option>
                        @foreach($provinces as $province_option)
                            <option value="{{ $province_option }}" {{ old('province', $activity->province) == $province_option ? 'selected' : '' }}>{{ $province_option }}</option>
                        @endforeach
                    </select>
                    @error('province')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                    <select name="city" id="city" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('city') border-red-500 @enderror">
                        <option value="">Pilih Kota</option>
                        @foreach($cities as $city_option)
                            <option value="{{ $city_option }}" {{ old('city', $activity->city) == $city_option ? 'selected' : '' }}>{{ $city_option }}</option>
                        @endforeach
                    </select>
                    @error('city')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" id="description" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror">{{ old('description', $activity->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-bas hover:bg-opacity-90 text-white py-2 px-6 rounded-md font-semibold">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const activityTypeSelect = document.getElementById('activity_type');
        const otherActivityTypeContainer = document.getElementById('other_activity_type_container');
        const otherActivityTypeInput = document.getElementById('other_activity_type');
        
        // Function to toggle the visibility of the other activity type field
        function toggleOtherActivityType() {
            if (activityTypeSelect.value === 'Other') {
                otherActivityTypeContainer.style.display = 'block';
                otherActivityTypeInput.setAttribute('required', 'required');
            } else {
                otherActivityTypeContainer.style.display = 'none';
                otherActivityTypeInput.removeAttribute('required');
            }
        }
        
        // Initial check
        toggleOtherActivityType();
        
        // Add event listener for changes
        activityTypeSelect.addEventListener('change', toggleOtherActivityType);
    });
</script>
@endpush 