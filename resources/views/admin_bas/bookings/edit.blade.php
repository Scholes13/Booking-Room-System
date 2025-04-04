@extends('admin_bas.layout')

@section('title', 'Edit Booking')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-800">Edit Booking</h2>
        <a href="{{ route('bas.dashboard') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Dashboard</span>
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
        <form action="{{ route('bas.bookings.update', $booking->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <!-- Nama -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
              <select name="nama" 
                      id="employee_select"
                      class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                      required>
                  <option value="">Pilih Karyawan</option>
                  @foreach($employees as $employee)
                      <option value="{{ $employee->name }}" 
                              data-department="{{ $employee->department->name }}"
                              {{ old('nama', $booking->nama) === $employee->name ? 'selected' : '' }}>
                          {{ $employee->name }}
                      </option>
                  @endforeach
              </select>
          </div>

            <!-- Departemen -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Departemen</label>
                <select name="department" 
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        required>
                    @foreach($departments as $department)
                        <option value="{{ $department->name }}" 
                                {{ old('department', $booking->department) == $department->name ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Ruang Meeting -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ruang Meeting</label>
                <select name="meeting_room_id" 
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        required>
                    @foreach($meetingRooms as $room)
                        <option value="{{ $room->id }}"
                                {{ old('meeting_room_id', $booking->meeting_room_id) == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tanggal -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" 
                       name="date" 
                       value="{{ old('date', $booking->date) }}"
                       class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                       required>
            </div>

            <!-- Waktu -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai</label>
                    <input type="time" 
                           name="start_time" 
                           value="{{ old('start_time', substr($booking->start_time, 0, 5)) }}"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai</label>
                    <input type="time" 
                           name="end_time" 
                           value="{{ old('end_time', substr($booking->end_time, 0, 5)) }}"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900" 
                           required>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" 
                          rows="3"
                          class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900">{{ old('description', $booking->description) }}</textarea>
            </div>

            <!-- Booking Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="booking_type" 
                        id="booking_type"
                        class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        required>
                    <option value="internal" {{ old('booking_type', $booking->booking_type) == 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="external" {{ old('booking_type', $booking->booking_type) == 'external' ? 'selected' : '' }}>Eksternal</option>
                </select>
            </div>

            <!-- External Description -->
            <div id="external_description_container" class="{{ old('booking_type', $booking->booking_type) == 'external' ? '' : 'hidden' }}">
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Eksternal</label>
                <textarea name="external_description" 
                          id="external_description"
                          rows="3"
                          class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                          placeholder="Silakan isi detail terkait booking eksternal...">{{ old('external_description', $booking->external_description) }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" 
                        class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="date"]');
    const roomSelect = document.querySelector('select[name="meeting_room_id"]');
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    const submitButton = document.querySelector('button[type="submit"]');
    const currentBookingId = {{ $booking->id }};
    let timeoutId;

    // Function untuk mengecek ketersediaan waktu
    async function checkAvailability() {
        if (!dateInput.value || !roomSelect.value || !startTimeInput.value || !endTimeInput.value) return;

        clearTimeout(timeoutId);
        timeoutId = setTimeout(async () => {
            try {
                const response = await fetch(`/bas/bookings/available-times?date=${dateInput.value}&meeting_room_id=${roomSelect.value}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const bookings = await response.json();

                // Reset style input waktu dan button
                startTimeInput.style.borderColor = '';
                endTimeInput.style.borderColor = '';
                submitButton.disabled = false;
                
                // Cek setiap booking yang ada
                const currentStart = startTimeInput.value;
                const currentEnd = endTimeInput.value;
                
                let hasConflict = false;
                
                bookings.forEach(booking => {
                    // Skip pengecekan untuk booking yang sedang diedit
                    if (booking.id && booking.id == currentBookingId) return;
                    
                    const bookingStart = booking.start;
                    const bookingEnd = booking.end;
                    
                    if (currentStart < bookingEnd && currentEnd > bookingStart) {
                        hasConflict = true;
                    }
                });
                
                if (hasConflict) {
                    startTimeInput.style.borderColor = '#EF4444'; // red-500
                    endTimeInput.style.borderColor = '#EF4444';
                    submitButton.disabled = true;
                    
                    // Tampilkan pesan error
                    showError('Waktu yang dipilih bertabrakan dengan jadwal yang sudah ada!');
                } else {
                    hideError();
                }
            } catch (error) {
                console.error('Error checking availability:', error);
            }
        }, 300); // Delay untuk menghindari terlalu banyak request
    }

    // Function untuk menampilkan pesan error
    function showError(message) {
        let errorDiv = document.getElementById('time-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'time-error';
            errorDiv.className = 'mt-2 text-red-600 text-sm';
            endTimeInput.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
    }

    // Function untuk menghilangkan pesan error
    function hideError() {
        const errorDiv = document.getElementById('time-error');
        if (errorDiv) errorDiv.remove();
    }
    
    // Event listeners
    dateInput.addEventListener('change', checkAvailability);
    roomSelect.addEventListener('change', checkAvailability);
    startTimeInput.addEventListener('change', checkAvailability);
    endTimeInput.addEventListener('change', checkAvailability);

    // Initial check
    checkAvailability();
});

document.addEventListener('DOMContentLoaded', () => {
    const bookingTypeSelect = document.getElementById('booking_type');
    const externalDescContainer = document.getElementById('external_description_container');
    const externalDescTextarea = document.getElementById('external_description');

    // Function to toggle external description based on booking type
    function toggleExternalDescription() {
        if (bookingTypeSelect.value === 'external') {
            externalDescContainer.classList.remove('hidden');
            externalDescTextarea.setAttribute('required', true);
        } else {
            externalDescContainer.classList.add('hidden');
            externalDescTextarea.removeAttribute('required');
        }
    }

    // Event listener for booking type change
    bookingTypeSelect.addEventListener('change', toggleExternalDescription);

    // Set initial state
    toggleExternalDescription();
});

// Auto-populate department when employee is selected
document.addEventListener('DOMContentLoaded', () => {
    const employeeSelect = document.getElementById('employee_select');
    const departmentSelect = document.querySelector('select[name="department"]');

    employeeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption && selectedOption.dataset.department) {
            // Find and select the matching department option
            for (let i = 0; i < departmentSelect.options.length; i++) {
                if (departmentSelect.options[i].value === selectedOption.dataset.department) {
                    departmentSelect.selectedIndex = i;
                    break;
                }
            }
        }
    });
});
</script>
@endpush 