@extends('layouts.app')

@section('title', 'Booking Ruangan')

@section('content')
<div class="min-h-screen flex items-center justify-center">
    <div class="w-full max-w-lg mx-auto bg-white/15 backdrop-blur-lg shadow-2xl rounded-lg p-8 border border-white/20">
        <h2 class="text-3xl font-semibold text-center text-white mb-6">
            Booking Ruang Meeting
        </h2>

        <!-- Loading Animation -->
        <div id="loading-container" class="hidden absolute top-0 left-0 w-full h-full bg-gray-800/50 flex justify-center items-center z-50">
            <dotlottie-player src="https://lottie.host/f8da24b0-9bfa-4bbe-86ae-a95fa7707066/eLfiJIDCGi.lottie" background="transparent" speed="1" style="width: 200px; height: 200px;" loop autoplay></dotlottie-player>
        </div>

        <!-- Form -->
        <form id="bookingForm" action="{{ route('booking.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Nama -->
            <div>
                <label class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-user mr-2 opacity-80"></i>
                    Nama
                </label>
                <div class="relative">
                    <select 
                        name="nama" 
                        id="employee_select"
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm"
                        required>
                        <option value="">Pilih Karyawan</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->name }}" 
                                    data-department="{{ $employee->department->name }}">
                                {{ $employee->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- Departemen -->
            <div>
                <label for="department" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-building mr-2 opacity-80"></i>
                    Departemen
                </label>
                <div class="relative">
                    <select 
                        id="department" 
                        name="department" 
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm" 
                        required 
                        readonly>
                        <option value="">Pilih Departemen</option>
                        @foreach($departments as $department)
                        <option value="{{ $department->name }}" {{ old('department') == $department->name ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- Tanggal -->
            <div>
                <label for="booking_date" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-calendar-alt mr-2 opacity-80"></i>
                    Tanggal
                </label>
                <div class="relative" onclick="document.getElementById('booking_date').showPicker()">
                    <input 
                        type="date" 
                        id="booking_date" 
                        name="date" 
                        min="{{ date('Y-m-d') }}"
                        value="{{ old('date', date('Y-m-d')) }}" 
                        class="form-input rounded-md pl-4 pr-10 py-2.5 w-full text-sm cursor-pointer" 
                        required>
                </div>
            </div>

            <!-- Ruang Meeting -->
            <div>
                <label for="meeting_room" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-door-open mr-2 opacity-80"></i>
                    Ruang Meeting
                </label>
                <div class="relative">
                    <select 
                        id="meeting_room" 
                        name="meeting_room_id" 
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm" 
                        required>
                        <option value="">Pilih Ruangan</option>
                        @foreach($meetingRooms as $room)
                        <option value="{{ $room->id }}" {{ old('meeting_room_id') == $room->id ? 'selected' : '' }}>
                            {{ $room->name }}
                        </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- Time Selection Container -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Jam Booking (Mulai) -->
                <div>
                    <label for="start_time_select" class="block text-sm font-medium text-white mb-2 flex items-center">
                        <i class="fas fa-clock mr-2 opacity-80"></i>
                        Jam Mulai
                    </label>
                    <div class="relative">
                        <select 
                            id="start_time_select" 
                            name="start_time" 
                            class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm" 
                            required>
                            <option value="">Pilih Waktu Mulai</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                            <i class="fas fa-chevron-down text-xs opacity-80"></i>
                        </div>
                    </div>
                </div>

                <!-- Jam Selesai Booking -->
                <div>
                    <label for="end_time_select" class="block text-sm font-medium text-white mb-2 flex items-center">
                        <i class="fas fa-clock mr-2 opacity-80"></i>
                        Jam Selesai
                    </label>
                    <div class="relative">
                        <select 
                            id="end_time_select" 
                            name="end_time" 
                            class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm" 
                            required>
                            <option value="">Pilih Waktu Selesai</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                            <i class="fas fa-chevron-down text-xs opacity-80"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Type -->
            <div>
                <label for="booking_type" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-tag mr-2 opacity-80"></i>
                    Type
                </label>
                <div class="relative">
                    <select 
                        id="booking_type" 
                        name="booking_type" 
                        class="form-select appearance-none w-full rounded-md pl-4 pr-10 py-2.5 text-sm" 
                        required>
                        <option value="internal" {{ old('booking_type', 'internal') == 'internal' ? 'selected' : '' }}>Internal</option>
                        <option value="external" {{ old('booking_type') == 'external' ? 'selected' : '' }}>Eksternal</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                        <i class="fas fa-chevron-down text-xs opacity-80"></i>
                    </div>
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="description" class="block text-sm font-medium text-white mb-2 flex items-center">
                    <i class="fas fa-comment-dots mr-2 opacity-80"></i>
                    Alasan / Deskripsi
                </label>
                <div class="relative">
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-input rounded-md pl-4 pr-4 py-2.5 w-full text-sm" 
                        rows="3" 
                        required>{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <button 
                type="submit" 
                class="w-full bg-white text-primary p-3 rounded-md shadow-md transform hover:scale-105 transition duration-300 hover:bg-white/90 font-medium mt-4">
                <i class="fas fa-check-circle mr-2"></i> Submit Booking
            </button>
        </form>
    </div>
</div>
@endsection

@push('styles')
<!-- Add the same fonts as login page -->
<link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="" />
<link
    rel="stylesheet"
    as="style"
    onload="this.rel='stylesheet'"
    href="https://fonts.googleapis.com/css2?display=swap&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900&amp;family=Plus+Jakarta+Sans%3Awght%40400%3B500%3B700%3B800"
/>

<style>
  :root {
    --primary-color: #26458e;
    --hover-color: #ffffff;
    --font-family: 'Plus Jakarta Sans', 'Noto Sans', sans-serif;
  }

  body {
    font-family: var(--font-family);
  }

  /* Filter elements */
  .form-select, .form-input {
    background-color: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 6px;
    width: 100%;
    transition: all 0.2s;
    backdrop-filter: blur(4px);
  }
  
  .form-input {
    padding: 0.625rem 0.75rem;
  }

  .form-select {
    appearance: none;
    background-image: none;
  }

  .form-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
  }

  input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
    background-color: rgba(255, 255, 255, 0.4);
    border-radius: 3px;
    padding: 3px;
  }
  
  /* Add style for the parent container of the date input */
  #booking_date {
    cursor: pointer;
  }

  .form-select:focus, .form-input:focus {
    outline: none;
    border-color: rgba(255, 255, 255, 0.8);
    box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
  }
  
  /* Add elegant hover effect */
  .form-select:hover, .form-input:hover {
    border-color: rgba(255, 255, 255, 0.5);
    background-color: rgba(255, 255, 255, 0.2);
  }

  /* Styling for option elements */
  option {
    background-color: #26458e;
    color: white;
  }
  
  option.booked {
    color: #ffc0cb;
    font-weight: 600;
    background-color: rgba(255, 0, 0, 0.2);
  }
  
  option[disabled] {
    color: #aaa;
    background-color: rgba(0, 0, 0, 0.2);
  }
  
  select[readonly] {
    pointer-events: none;
    opacity: 0.7;
  }

  /* Submit button */
  .bg-white {
    background-color: #ffffff;
  }

  .text-primary {
    color: var(--primary-color);
  }
</style>
@endpush

@push('scripts')
<!-- DotLottie Player -->
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

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
        // Jika sudah melewati jam kerja (misal 20:00)
        if (currentHour >= 20) {
            this.populateTimeSelect(this.startTimeSelect, [], []);
            this.populateTimeSelect(this.endTimeSelect, [], []);
            return;
        }
        const startTime = `${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}`;
        const slots = this.generateTimeSlots(startTime, '20:00', 30);
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
            if (currentHour < 9) {
                startTime = '09:00';
            } else {
                startTime = `${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}`;
            }
        }
        const slots = this.generateTimeSlots(startTime, '20:00', 30);
        this.populateTimeSelect(this.startTimeSelect, slots, bookedIntervals);
        this.updateEndTimeOptions();
    }
    updateEndTimeOptions() {
        const startTime = this.startTimeSelect.value;
        if (!startTime) {
            this.endTimeSelect.innerHTML = '<option value="">Pilih Waktu Selesai</option>';
            return;
        }
        const endSlots = this.generateTimeSlots(startTime, '20:00', 30).slice(1);
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
        // Use our new toast notification system instead of inserting error div
        window.showErrorToast(message);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    try {
        const bookingManager = new BookingTimeManager();
        window.bookingManager = bookingManager;
        
        // Add date input focus event to automatically open date picker
        const dateInput = document.getElementById('booking_date');
        if (dateInput) {
            dateInput.addEventListener('focus', function() {
                this.showPicker();
            });
        }
        
        // Add form submit event listener
        const bookingForm = document.getElementById('bookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', function(event) {
                // Don't show if validation fails
                if (!bookingForm.checkValidity()) return;
                
                // Show loading toast when form is submitted
                Toastify({
                    text: "Submitting booking...",
                    duration: 0, // Won't disappear until page refreshes
                    gravity: "top",
                    position: "right",
                    backgroundColor: "",
                    className: "success-toast",
                    stopOnFocus: true
                }).showToast();
            });
        }
    } catch (error) {
        console.error('Error initializing BookingTimeManager:', error);
    }
});
</script>
@endpush

<!-- JavaScript untuk external description telah dihapus -->
