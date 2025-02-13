class BookingTimeManager {
    constructor() {
        this.initializeElements();
        if (this.elementsExist()) {
            this.initializeEventListeners();
        } else {
            console.warn('Missing elements:', this.getMissingElements());
        }
    }

    initializeElements() {
        this.startTimeSelect = document.getElementById('start_time_select');
        this.endTimeSelect = document.getElementById('end_time_select');
        this.dateInput = document.getElementById('booking_date');
        this.roomSelect = document.getElementById('meeting_room');
        this.loadingContainer = document.getElementById('loading-container');
    }

    getMissingElements() {
        const elements = {
            startTimeSelect: 'start_time_select',
            endTimeSelect: 'end_time_select',
            dateInput: 'booking_date',
            roomSelect: 'meeting_room',
            loadingContainer: 'loading-container'
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
            this.loadingContainer
        );
    }

    initializeEventListeners() {
        this.dateInput.addEventListener('change', () => this.fetchBookedIntervals());
        this.roomSelect.addEventListener('change', () => this.fetchBookedIntervals());
        this.startTimeSelect.addEventListener('change', () => this.updateEndTimeOptions());
    }

    generateTimeSlots(startTime, endTime, interval = 30) {
        const slots = [];
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);
        
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
        return bookedIntervals.some(([start, end]) => slot >= start && slot <= end);
    }

    async fetchBookedIntervals() {
        const date = this.dateInput.value;
        const room = this.roomSelect.value;
        
        if (!date || !room) return;

        this.loadingContainer.classList.remove('hidden');
        
        try {
            // Menggunakan URL absolut untuk route
            const response = await fetch(`/available-times?date=${date}&meeting_room_id=${room}`);
            const data = await response.json();
            
            const bookedIntervals = data.map(interval => [
                interval.start.substring(0, 5),
                interval.end.substring(0, 5)
            ]);

            this.updateTimeSelects(bookedIntervals);
        } catch (error) {
            console.error('Error fetching booked intervals:', error);
        } finally {
            this.loadingContainer.classList.add('hidden');
        }
    }

    updateTimeSelects(bookedIntervals) {
        const slots = this.generateTimeSlots('08:00', '17:00', 30);
        
        this.populateTimeSelect(this.startTimeSelect, slots, bookedIntervals);
        this.updateEndTimeOptions();
    }

    populateTimeSelect(select, slots, bookedIntervals) {
        select.innerHTML = '<option value="">Pilih Waktu</option>';
        
        slots.forEach(slot => {
            const option = document.createElement('option');
            option.value = slot;
            option.textContent = slot;
            
            if (this.isSlotBooked(slot, bookedIntervals)) {
                option.classList.add('booked');
                option.textContent = `${slot} (Booked)`;
                option.disabled = true;
            }
            
            select.appendChild(option);
        });
    }

    updateEndTimeOptions() {
        const startTime = this.startTimeSelect.value;
        if (!startTime) return;

        const endSlots = this.generateTimeSlots(startTime, '17:00', 30);
        const currentEndTime = this.endTimeSelect.value;
        
        this.populateTimeSelect(this.endTimeSelect, endSlots, []);
        
        if (currentEndTime && endSlots.includes(currentEndTime)) {
            this.endTimeSelect.value = currentEndTime;
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    try {
        const bookingManager = new BookingTimeManager();
        window.bookingManager = bookingManager;
    } catch (error) {
        console.error('Error initializing BookingTimeManager:', error);
    }
});