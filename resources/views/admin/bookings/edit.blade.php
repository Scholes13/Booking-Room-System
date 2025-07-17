@php
    $layout = isset($isSuperAdmin) && $isSuperAdmin ? 'superadmin.layout' : 'admin.layout';
@endphp

@extends($layout)

@section('title', 'Edit Booking')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Edit Booking</h1>
            <p class="text-gray-500 mt-1">Update booking details</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-gray-500 text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-gray-600 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Bookings
        </a>
    </div>

    <div class="bg-white rounded-lg p-6 shadow-sm">
        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <ul class="text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        
        <form method="POST" action="{{ route('admin.bookings.update', $booking->id) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemohon</label>
                    <input type="text" 
                           name="nama" 
                           value="{{ old('nama', $booking->nama) }}"
                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                           required>
                </div>

                <!-- Departemen -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Departemen</label>
                    <select name="department" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                            required>
                        @foreach($departments as $department)
                            <option value="{{ $department->name }}" {{ old('department', $booking->department) == $department->name ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Ruang Meeting -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ruang Meeting</label>
                    <select name="meeting_room_id" 
                            class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                            required>
                        @foreach($meetingRooms as $room)
                            <option value="{{ $room->id }}" {{ old('meeting_room_id', $booking->meeting_room_id) == $room->id ? 'selected' : '' }}>
                                {{ $room->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal</label>
                    <input type="date" 
                           name="date" 
                           value="{{ old('date', $booking->date) }}"
                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           required>
                </div>
            </div>

            <!-- Waktu -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Mulai</label>
                    <input type="time" 
                           name="start_time" 
                           value="{{ old('start_time', substr($booking->start_time, 0, 5)) }}"
                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jam Selesai</label>
                    <input type="time" 
                           name="end_time" 
                           value="{{ old('end_time', substr($booking->end_time, 0, 5)) }}"
                           class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           required>
                </div>
            </div>

            <!-- Booking Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Booking</label>
                <select name="booking_type" 
                        id="booking_type"
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        required>
                    <option value="internal" {{ old('booking_type', $booking->booking_type) == 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="external" {{ old('booking_type', $booking->booking_type) == 'external' ? 'selected' : '' }}>Eksternal</option>
                </select>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea name="description" 
                          rows="4"
                          class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                          placeholder="Masukkan deskripsi atau agenda meeting...">{{ old('description', $booking->description) }}</textarea>
            </div>

            <!-- External Description -->
            <div id="external_description_container" class="{{ old('booking_type', $booking->booking_type) == 'external' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Eksternal</label>
                <textarea name="external_description" 
                          id="external_description"
                          rows="3"
                          class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                          placeholder="Silakan isi detail terkait booking eksternal...">{{ old('external_description', $booking->external_description) }}</textarea>
            </div>

            <!-- Status (Admin only) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status" 
                        class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900">
                    <option value="pending" {{ old('status', $booking->status ?? 'approved') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ old('status', $booking->status ?? 'approved') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ old('status', $booking->status ?? 'approved') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end gap-4 pt-6">
                <a href="{{ route('admin.bookings.index') }}" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-[#24448c] text-white rounded-lg font-medium hover:bg-[#1c3670] transition-colors focus:outline-none focus:ring-2 focus:ring-[#24448c]">
                    Update Booking
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle booking type change to show/hide external description
        const bookingTypeSelect = document.getElementById('booking_type');
        const externalDescContainer = document.getElementById('external_description_container');
        const externalDescTextarea = document.getElementById('external_description');
        
        bookingTypeSelect.addEventListener('change', function() {
            if (this.value === 'external') {
                externalDescContainer.classList.remove('hidden');
                externalDescTextarea.setAttribute('required', true);
            } else {
                externalDescContainer.classList.add('hidden');
                externalDescTextarea.removeAttribute('required');
            }
        });
    });
</script>
@endpush