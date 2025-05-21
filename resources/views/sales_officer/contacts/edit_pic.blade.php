@extends('sales_officer.layout')

@section('title', 'Edit Contact Person')
@section('header', 'Edit Contact Person')
@section('description', 'Update contact person information')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h4 class="text-base font-semibold text-gray-800">
                Edit Contact Person for {{ $contact->company_name }}
            </h4>
            <p class="text-sm text-gray-500 mt-1">
                Update information for {{ $pic->title }} {{ $pic->name }}
            </p>
        </div>
        <a href="{{ route('sales_officer.contacts.show', $contact->id) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
            Back to Contact
        </a>
    </div>
    
    <form action="{{ route('sales_officer.contacts.update_pic', $pic->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <select name="title" id="title" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="Mr." {{ $pic->title == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                    <option value="Mrs." {{ $pic->title == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                    <option value="Ms." {{ $pic->title == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                    <option value="Dr." {{ $pic->title == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                </select>
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="division_id" class="block text-sm font-medium text-gray-700 mb-1">Division</label>
                <select name="division_id" id="division_id" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">General (No Division)</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ $pic->division_id == $division->id ? 'selected' : '' }}>
                            {{ $division->name }}
                        </option>
                    @endforeach
                </select>
                @error('division_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $pic->name) }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" required>
            @error('name')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
            <input type="text" name="position" id="position" value="{{ old('position', $pic->position) }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            @error('position')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $pic->phone_number) }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                @error('phone_number')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $pic->email) }}" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                @error('email')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="flex items-center">
            <input type="checkbox" name="is_primary" id="is_primary" value="1" {{ $pic->is_primary ? 'checked' : '' }} class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
            <label for="is_primary" class="ml-2 block text-sm text-gray-700">Set as primary contact</label>
            @error('is_primary')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div class="pt-4 border-t border-gray-200 flex justify-end space-x-3">
            <a href="{{ route('sales_officer.contacts.show', $contact->id) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-sm font-medium">
                Update Contact Person
            </button>
        </div>
    </form>
</div>
@endsection 