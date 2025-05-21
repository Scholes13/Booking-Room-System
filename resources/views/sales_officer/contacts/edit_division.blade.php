@extends('sales_officer.layout')

@section('title', 'Edit Division')
@section('header', 'Edit Division')
@section('description', 'Update division information for ' . $contact->company_name)

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <form action="{{ route('sales_officer.contacts.update_division', $division->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div>
            <h4 class="text-base font-semibold text-gray-800 mb-4">Division Information</h4>
            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Division Name <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $division->name) }}" required class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    @error('name')<div class="text-red-500 text-xs mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div class="flex justify-end">
            <a href="{{ route('sales_officer.contacts.show', $contact->id) }}" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary mr-3">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-primary hover:bg-primary-dark text-white rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Update Division
            </button>
        </div>
    </form>
</div>
@endsection 