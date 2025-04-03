@extends('layouts.app')

@section('title', 'Booking Ruangan')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-lg mx-auto bg-white/20 backdrop-blur-lg shadow-2xl rounded-lg p-8">
        <h2 class="text-3xl font-semibold text-center text-gray-200 mb-6">
            Booking Ruang Meeting
        </h2>

        <!-- Error Validation -->
        @if($errors->any())
        <div class="bg-red-500/20 text-red-400 p-3 rounded-md mb-4">
            <ul>
                @foreach($errors->all() as $error)
                <li>⚠️ {{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Success Message -->
        @if(session('success'))
        <div class="bg-green-500/20 text-green-400 p-3 rounded-md mb-4">
            ✅ {{ session('success') }}
        </div>
        @endif

        <!-- Loading Animation -->
        <div id="loading-container" class="hidden absolute top-0 left-0 w-full h-full bg-gray-800/50 flex justify-center items-center z-50">
            <dotlottie-player src="https://lottie.host/f8da24b0-9bfa-4bbe-86ae-a95fa7707066/eLfiJIDCGi.lottie" background="transparent" speed="1" style="width: 200px; height: 200px;" loop autoplay></dotlottie-player>
        </div>

        <!-- Form -->
        <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1">Nama</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-user text-gray-400 mr-2"></i>
                    <select name="nama" 
                            id="employee_select"
                            class="w-full bg-transparent border-none outline-none text-gray-900"
                            required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->name }}" 
                                    data-department="{{ $employee->department->name }}">
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Departemen -->
            <div>
                <label for="department" class="block text-gray-300 font-medium text-sm">Departemen</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-building text-gray-400 mr-2"></i>
                    <select id="department" 
                            name="department" 
                            class="w-full bg-transparent border-none outline-none text-gray-900" 
                            required 
                            readonly>
                        <option value="" class="text-gray-900">Pilih Departemen</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->name }}" {{ old('department') == $department->name ? 'selected' : '' }} class="text-black">
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tanggal -->
            <div>
                <label for="booking_date" class="block text-gray-300 font-medium text-sm">Tanggal</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                    <input type="date" 
                           id="booking_date" 
                           name="date" 
                           min="{{ date('Y-m-d') }}"
                           value="{{ old('date', date('Y-m-d')) }}" 
                           class="w-full bg-transparent border-none outline-none text-gray-900 placeholder-gray-400" 
                           required>
                </div>
            </div>

            <!-- Ruang Meeting -->
            <div>
                <label for="meeting_room" class="block text-gray-300 font-medium text-sm">Ruang Meeting</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-door-open text-gray-400 mr-2"></i>
                    <select id="meeting_room" 
                            name="meeting_room_id" 
                            class="w-full bg-transparent border-none outline-none text-gray-900" 
                            required>
                        <option value="" class="text-gray-900">Pilih Ruangan</option>
                        @foreach($meetingRooms as $room)
                        <option value="{{ $room->id }}" {{ old('meeting_room_id') == $room->id ? 'selected' : '' }} class="text-black">
                            {{ $room->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Time Selection Container -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Jam Booking (Mulai) -->
                <div>
                    <label for="start_time_select" class="block text-gray-300 font-medium text-sm">Jam Booking (Mulai)</label>
                    <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        <select id="start_time_select" 
                                name="start_time" 
                                class="w-full bg-transparent border-none outline-none text-gray-900" 
                                required>
                            <option value="">Pilih Waktu Mulai</option>
                        </select>
                    </div>
                </div>

                <!-- Jam Selesai Booking -->
                <div>
                    <label for="end_time_select" class="block text-gray-300 font-medium text-sm">Jam Selesai Booking</label>
                    <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>
                        <select id="end_time_select" 
                                name="end_time" 
                                class="w-full bg-transparent border-none outline-none text-gray-900" 
                                required>
                            <option value="">Pilih Waktu Selesai</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-gray-300 font-medium text-sm">Alasan / Deskripsi</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-comment-dots text-gray-400 mr-2"></i>
                    <textarea id="description" 
                              name="description" 
                              class="w-full bg-transparent border-none outline-none text-gray-900 placeholder-gray-400" 
                              rows="3" 
                              required>{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Booking Type -->
            <div>
                <label for="booking_type" class="block text-gray-300 font-medium text-sm">Type</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-tag text-gray-400 mr-2"></i>
                    <select id="booking_type" 
                            name="booking_type" 
                            class="w-full bg-transparent border-none outline-none text-gray-900" 
                            required>
                        <option value="internal" {{ old('booking_type', 'internal') == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="external" {{ old('booking_type') == 'external' ? 'selected' : '' }}>Eksternal</option>
                    </select>
                </div>
            </div>

            <!-- External Description (Hidden by default) -->
            <div id="external_description_container" class="hidden">
                <label for="external_description" class="block text-gray-300 font-medium text-sm">Deskripsi Eksternal</label>
                <div class="flex items-center bg-white/30 backdrop-blur-md rounded-md p-3 shadow-md border border-white/20">
                    <i class="fas fa-building text-gray-400 mr-2"></i>
                    <textarea id="external_description" 
                              name="external_description" 
                              class="w-full bg-transparent border-none outline-none text-gray-900 placeholder-gray-400" 
                              rows="3" 
                              placeholder="Silakan isi detail terkait booking eksternal...">{{ old('external_description') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-blue-800 text-white p-3 rounded-md shadow-lg transform hover:scale-105 transition duration-300">
                <i class="fas fa-check-circle mr-2"></i> Submit Booking
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- DotLottie Player -->
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

<!-- Custom Style -->
<style>
    select {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"><polygon points="0,0 12,0 6,6" fill="%23999"/></svg>');
        background-repeat: no-repeat;
        background-position: right 10px center;
        background-size: 12px;
    }
    option.booked {
        color: #EF4444;
        font-weight: 600;
        background-color: rgba(239, 68, 68, 0.1);
    }
    option[disabled] {
        color: #6B7280;
        background-color: #F3F4F6;
    }
    select[readonly] {
        pointer-events: none;
        opacity: 0.7;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
class BookingTimeManager {
    constructor() {
        this.initializeElements();
        this.bookedIntervals = []; // Menyimpan interval waktu yang sudah dibooking
        if (this.elementsExist()) {
            this.initializeEventListeners();
            this.handleDateChange();
        } else {
            console.warn('Missing elements:', this.getMissingElements());
        }
    }
    initializeElements() {
        this.startTimeSelect = document.getElementById('start_time_select');
        this.endTimeSelect   = document.getElementById('end_time_select');
        this.dateInput       = document.getElementById('booking_date');
        this.roomSelect      = document.getElementById('meeting_room');
        this.loadingContainer= document.getElementById('loading-container');
        this.employeeSelect  = document.getElementById('employee_select');
        this.departmentSelect= document.getElementById('department');
        this.bookingForm     = document.getElementById('bookingForm');
    }
    getMissingElements() {
        const elements = {
            startTimeSelect: 'start_time_select',
            endTimeSelect: 'end_time_select',
            dateInput: 'booking_date',
            roomSelect: 'meeting_room',
            loadingContainer: 'loading-container',
            employeeSelect: 'employee_select',
            departmentSelect: 'department',
            bookingForm: 'bookingForm'
        };
        return Object.entries(elements)
            .filter(([_, id]) => !document.getElementById(id))
            .map(([name, id]) => `${name} (ID: ${id})`);
    }
    elementsExist() {
        return (
            this.startTimeSelect &&
            this.endTimeSelect &&
            this.dateInput &&
            this.roomSelect &&
            this.loadingContainer &&
            this.employeeSelect &&
            this.departmentSelect &&
            this.bookingForm
        );
    }
    initializeEventListeners() {
        this.dateInput.addEventListener('change', () => this.handleDateChange());
        this.roomSelect.addEventListener('change', () => this.fetchBookedIntervals());
        this.startTimeSelect.addEventListener('change', () => this.updateEndTimeOptions());
        this.employeeSelect.addEventListener('change', () => this.handleEmployeeChange());
        
        // Jika terdapat error sebelumnya, reset form
        if (document.querySelector('.bg-red-500\\/20')) {
            this.resetFormOnError();
        }
    }
    handleEmployeeChange() {
        const selectedOption = this.employeeSelect.options[this.employeeSelect.selectedIndex];
        if (selectedOption.value) {
            const department = selectedOption.dataset.department;
            Array.from(this.departmentSelect.options).forEach(option => {
                if (option.value === department) {
                    option.selected = true;
                }
            });
            // Set department menjadi readonly agar nilainya tetap terkirim
            this.departmentSelect.setAttribute('readonly', 'readonly');
        } else {
            this.departmentSelect.removeAttribute('readonly');
            this.departmentSelect.value = '';
        }
    }
    resetFormOnError() {
        // Reset form kecuali pesan error
        this.bookingForm.reset();
        // Set ulang tanggal ke hari ini
        this.dateInput.value = new Date().toISOString().split('T')[0];
        // Reset department select
        this.departmentSelect.removeAttribute('readonly');
        // Refresh slot waktu
        this.handleDateChange();
    }
    handleDateChange() {
        const selectedDate = new Date(this.dateInput.value);
        const today = new Date();
        selectedDate.setHours(0, 0, 0, 0);
        today.setHours(0, 0, 0, 0);
        if (selectedDate.getTime() === today.getTime()) {
            this.generateTimeSlotsFromCurrentTime();
        } else {
            this.fetchBookedIntervals();
        }
    }
    generateTimeSlotsFromCurrentTime() {
        const now = new Date();
        const currentHour = now.getHours();
        const currentMinute = now.getMinutes();
        // Round up ke interval 30 menit berikutnya
        let startMinute = currentMinute >= 30 ? 0 : 30;
        let startHour   = currentMinute >= 30 ? currentHour + 1 : currentHour;
        // Jika sudah melewati jam kerja (misal 17:00)
        if (currentHour >= 17) {
            this.populateTimeSelect(this.startTimeSelect, [], []);
            this.populateTimeSelect(this.endTimeSelect, [], []);
            return;
        }
        const startTime = `${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}`;
        const slots = this.generateTimeSlots(startTime, '17:00', 30);
        this.populateTimeSelect(this.startTimeSelect, slots, []);
        this.updateEndTimeOptions();
    }
    async fetchBookedIntervals() {
        const date = this.dateInput.value;
        const room = this.roomSelect.value;
        if (!date || !room) return;
        this.loadingContainer.classList.remove('hidden');
        try {
            const response = await fetch(`{{ route('available.times') }}?date=${date}&meeting_room_id=${room}`);
            const data = await response.json();
            const bookedIntervals = data.map(interval => [
                interval.start.substring(0, 5),
                interval.end.substring(0, 5)
            ]);
            // Simpan booked intervals secara global
            this.bookedIntervals = bookedIntervals;
            this.updateTimeSelects(bookedIntervals);
        } catch (error) {
            console.error('Error fetching booked intervals:', error);
            this.showError('Failed to fetch booking times. Please try again.');
        } finally {
            this.loadingContainer.classList.add('hidden');
        }
    }
    generateTimeSlots(startTime, endTime, interval = 30) {
        const slots = [];
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute]     = endTime.split(':').map(Number);
        let current = new Date();
        current.setHours(startHour, startMinute, 0);
        const end = new Date();
        end.setHours(endHour, endMinute, 0);
        while (current <= end) {
            const hours = String(current.getHours()).padStart(2, '0');
            const minutes = String(current.getMinutes()).padStart(2, '0');
            slots.push(`${hours}:${minutes}`);
            current.setMinutes(current.getMinutes() + interval);
        }
        return slots;
    }
    isSlotBooked(slot, bookedIntervals) {
        return bookedIntervals.some(([start, end]) => {
            return slot >= start && slot <= end;
        });
    }
    updateTimeSelects(bookedIntervals) {
        const selectedDate = new Date(this.dateInput.value);
        const today = new Date();
        const isToday = selectedDate.toDateString() === today.toDateString();
        let startTime = '08:00';
        if (isToday) {
            const currentHour = today.getHours();
            const currentMinute = today.getMinutes();
            let startMinute = currentMinute >= 30 ? 0 : 30;
            let startHour   = currentMinute >= 30 ? currentHour + 1 : currentHour;
            if (currentHour < 8) {
                startTime = '08:00';
            } else {
                startTime = `${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}`;
            }
        }
        const slots = this.generateTimeSlots(startTime, '17:00', 30);
        this.populateTimeSelect(this.startTimeSelect, slots, bookedIntervals);
        this.updateEndTimeOptions();
    }
    updateEndTimeOptions() {
        const startTime = this.startTimeSelect.value;
        if (!startTime) {
            this.endTimeSelect.innerHTML = '<option value="">Pilih Waktu Selesai</option>';
            return;
        }
        const endSlots = this.generateTimeSlots(startTime, '17:00', 30).slice(1);
        const currentEndTime = this.endTimeSelect.value;
        // Tandai slot yang sudah dibooking pada waktu selesai
        this.populateTimeSelect(this.endTimeSelect, endSlots, this.bookedIntervals || []);
        if (currentEndTime && endSlots.includes(currentEndTime)) {
            this.endTimeSelect.value = currentEndTime;
        }
    }
    populateTimeSelect(select, slots, bookedIntervals) {
        select.innerHTML = `<option value="">${select === this.startTimeSelect ? 'Pilih Waktu Mulai' : 'Pilih Waktu Selesai'}</option>`;
        slots.forEach(slot => {
            const option = document.createElement('option');
            option.value = slot;
            option.textContent = slot;
            if (bookedIntervals && this.isSlotBooked(slot, bookedIntervals)) {
                option.classList.add('booked');
                option.textContent = `${slot} (Booked)`;
                option.disabled = true;
            }
            select.appendChild(option);
        });
    }
    showError(message) {
        const errorDiv = document.createElement('div');
        errorDiv.className = 'bg-red-500/20 text-red-400 p-3 rounded-md mb-4';
        errorDiv.textContent = message;
        const existingError = document.querySelector('.bg-red-500\\/20');
        if (existingError) {
            existingError.replaceWith(errorDiv);
        } else {
            this.bookingForm.insertBefore(errorDiv, this.bookingForm.firstChild);
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    try {
        const bookingManager = new BookingTimeManager();
        window.bookingManager = bookingManager;
    } catch (error) {
        console.error('Error initializing BookingTimeManager:', error);
    }
});
</script>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const bookingTypeSelect = document.getElementById('booking_type');
    const externalDescContainer = document.getElementById('external_description_container');
    const externalDescTextarea = document.getElementById('external_description');

    // Show/hide external description based on booking type
    function toggleExternalDescription() {
        if (bookingTypeSelect.value === 'external') {
            externalDescContainer.classList.remove('hidden');
        } else {
            externalDescContainer.classList.add('hidden');
            externalDescTextarea.value = ''; // Clear the value when hidden
        }
    }

    // Initial state
    toggleExternalDescription();

    // Listen for changes
    bookingTypeSelect.addEventListener('change', toggleExternalDescription);
});
</script>
@endpush
