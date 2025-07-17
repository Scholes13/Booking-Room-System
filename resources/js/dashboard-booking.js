/**
 * Dashboard Booking Management JavaScript
 * Handles CRUD operations, auto-population, and modal management
 */

class DashboardBookingManager {
    constructor() {
        this.currentBookingId = null;
        this.flatpickrInstance = null;
        
        // API endpoints
        this.apiEndpoints = {
            bookingUpdate: '/api/bookings',
            bookingDelete: '/api/bookings',
            validateTimeSlot: '/api/bookings/validate-time-slot'
        };
        
        this.init();
    }

    /**
     * Initialize the dashboard
     */
    init() {
        this.initializeFlatpickr();
        this.setupEventListeners();
        this.setupModalClosers();
        this.restoreStateAndLoad();
    }

    /**
     * Initialize Flatpickr date picker
     */
    initializeFlatpickr() {
        const datePickerInput = document.getElementById('date-picker');
        if (datePickerInput) {
            this.flatpickrInstance = flatpickr(datePickerInput, {
                mode: "range",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                onChange: (selectedDates, dateStr) => {
                    if (selectedDates.length === 2) {
                        const filter = 'custom';
                        localStorage.setItem('activeFilter', filter);
                        localStorage.setItem('activeDate', dateStr);
                        this.setActiveFilter(document.getElementById('filter-custom'));
                        this.fetchBookings(filter, dateStr);
                    }
                },
            });
        }
    }

    /**
     * Setup event listeners
     */
    setupEventListeners() {
        // Filter buttons
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            if (button.id === 'filter-custom') return;
            button.addEventListener('click', (e) => {
                const filter = e.target.dataset.filter;
                localStorage.setItem('activeFilter', filter);
                localStorage.removeItem('activeDate');
                this.setActiveFilter(e.target);
                if (this.flatpickrInstance) this.flatpickrInstance.clear();
                this.fetchBookings(filter);
            });
        });

        // Employee change handler for department auto-population
        const employeeSelect = document.getElementById('edit_nama_select');
        if (employeeSelect) {
            employeeSelect.addEventListener('change', (e) => {
                this.handleEmployeeChange(e.target.value);
            });
        }
    }

    /**
     * Setup modal closers and event handlers
     */
    setupModalClosers() {
        // Close modal function
        const closeAllModals = () => {
            document.querySelectorAll('.modal-container').forEach(modal => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });
        };

        // Attach to all close buttons and cancel buttons
        document.querySelectorAll('.modal-close').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                closeAllModals();
            });
        });

        // Attach to modal overlays
        document.querySelectorAll('.modal-container').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeAllModals();
                }
            });
        });

        // External Description toggle in Edit Modal
        const bookingTypeSelect = document.getElementById('edit_booking_type');
        if (bookingTypeSelect) {
            bookingTypeSelect.addEventListener('change', () => {
                const externalDescContainer = document.getElementById('edit_external_description_container');
                if (bookingTypeSelect.value === 'external') {
                    externalDescContainer.classList.remove('hidden');
                } else {
                    externalDescContainer.classList.add('hidden');
                }
            });
        }
    }

    /**
     * Handle employee change for department auto-population
     */
    handleEmployeeChange(employeeName) {
        const departmentSelect = document.getElementById('edit_department_select');
        const employeeSelect = document.getElementById('edit_nama_select');
        
        if (!employeeName || !departmentSelect || !employeeSelect) {
            if (departmentSelect) departmentSelect.value = '';
            return;
        }

        try {
            // Find the selected employee option and get its department data attribute
            const selectedOption = employeeSelect.querySelector(`option[value="${employeeName}"]`);
            
            if (selectedOption && selectedOption.dataset.department) {
                departmentSelect.value = selectedOption.dataset.department;
            } else {
                departmentSelect.value = '';
            }
        } catch (error) {
            departmentSelect.value = '';
        }
    }

    /**
     * Attach action listeners to table buttons
     */
    attachActionListeners() {
        // Remove existing event listeners by removing the old event handler attribute
        document.querySelectorAll('.edit-booking-btn, .delete-booking-btn').forEach(button => {
            button.removeAttribute('data-listener-attached');
        });
        
        // Edit Button Listeners
        const editButtons = document.querySelectorAll('.edit-booking-btn');
        
        editButtons.forEach((button, index) => {
            // Skip if listener already attached
            if (button.getAttribute('data-listener-attached') === 'true') {
                return;
            }
            
            // Mark as having listener attached
            button.setAttribute('data-listener-attached', 'true');
            
            button.addEventListener('click', async (e) => {
                e.preventDefault();
                
                // Find the actual button element (in case user clicked on icon)
                let buttonElement = e.target;
                if (!buttonElement.classList.contains('edit-booking-btn')) {
                    buttonElement = buttonElement.closest('.edit-booking-btn');
                }
                
                if (buttonElement) {
                    await this.handleEditBooking(buttonElement);
                }
            });
        });

        // Delete Button Listeners
        const deleteButtons = document.querySelectorAll('.delete-booking-btn');
        deleteButtons.forEach((button, index) => {
            // Skip if listener already attached
            if (button.getAttribute('data-listener-attached') === 'true') {
                return;
            }
            
            // Mark as having listener attached
            button.setAttribute('data-listener-attached', 'true');
            
            button.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Find the actual button element (in case user clicked on icon)
                let buttonElement = e.target;
                if (!buttonElement.classList.contains('delete-booking-btn')) {
                    buttonElement = buttonElement.closest('.delete-booking-btn');
                }
                
                if (buttonElement) {
                    this.handleDeleteBooking(buttonElement);
                }
            });
        });
    }

    /**
     * Handle edit booking button click
     */
    async handleEditBooking(button) {
        try {
            this.currentBookingId = button.dataset.id;
            
            // Show modal
            const modal = document.getElementById('editBookingModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            // Convert dataset to proper format for populateEditForm
            // Note: HTML data-start-time becomes dataset.startTime in JavaScript
            const formData = {
                date: button.dataset.date,
                startTime: button.dataset.startTime,
                endTime: button.dataset.endTime,
                description: button.dataset.description,
                bookingType: button.dataset.bookingType,
                externalDescription: button.dataset.externalDescription,
                roomId: button.dataset.roomId,
                name: button.dataset.name,
                department: button.dataset.department
            };
            

            // Add delay to ensure modal is fully rendered before populating form
            setTimeout(() => {
                this.populateEditForm(formData);
            }, 100);

            // Setup form submission
            this.setupEditFormSubmission();

        } catch (error) {
            this.showErrorMessage('Failed to open edit form');
        }
    }

    /**
     * Populate edit form with booking data
     */
    populateEditForm(data) {
        // Set basic form fields
        const dateField = document.getElementById('edit_date');
        const startTimeField = document.getElementById('edit_start_time');
        const endTimeField = document.getElementById('edit_end_time');
        const descField = document.getElementById('edit_description');
        const bookingTypeField = document.getElementById('edit_booking_type');

        if (dateField) {
            dateField.value = data.date || '';
        }
        if (startTimeField) {
            startTimeField.value = data.startTime || data['start-time'] || '';
        }
        if (endTimeField) {
            endTimeField.value = data.endTime || data['end-time'] || '';
        }
        if (descField) {
            descField.value = data.description || '';
        }
        if (bookingTypeField) {
            bookingTypeField.value = data.bookingType || data['booking-type'] || 'internal';
        }

        // Handle external description visibility
        const externalDescContainer = document.getElementById('edit_external_description_container');
        const externalDescField = document.getElementById('edit_external_description');
        const bookingType = data.bookingType || data['booking-type'] || 'internal';
        
        if (bookingType === 'external') {
            if (externalDescContainer) externalDescContainer.classList.remove('hidden');
            if (externalDescField) {
                externalDescField.value = data.externalDescription || data['external-description'] || '';
            }
        } else {
            if (externalDescContainer) externalDescContainer.classList.add('hidden');
        }

        // Set dropdown values directly (no TomSelect)
        const roomSelect = document.getElementById('edit_meeting_room_id');
        const nameSelect = document.getElementById('edit_nama_select');
        const departmentSelect = document.getElementById('edit_department_select');
        
        // Set dropdown values with validation and event triggering
        if (roomSelect && data.roomId) {
            const roomOptions = Array.from(roomSelect.options);
            const matchingRoomOption = roomOptions.find(opt => opt.value === data.roomId);
            
            if (matchingRoomOption) {
                roomSelect.value = data.roomId;
                // Trigger change event to update visual display
                roomSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
        
        if (nameSelect && data.name) {
            const nameOptions = Array.from(nameSelect.options);
            const matchingNameOption = nameOptions.find(opt => opt.value === data.name);
            
            if (matchingNameOption) {
                nameSelect.value = data.name;
                // Trigger change event to update visual display
                nameSelect.dispatchEvent(new Event('change', { bubbles: true }));
                
                // Auto-populate department when employee is selected
                this.handleEmployeeChange(data.name);
            }
        }
        
        if (departmentSelect && data.department) {
            const departmentOptions = Array.from(departmentSelect.options);
            const matchingDeptOption = departmentOptions.find(opt => opt.value === data.department);
            
            if (matchingDeptOption) {
                departmentSelect.value = data.department;
                // Trigger change event to update visual display
                departmentSelect.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    }

    /**
     * Setup edit form submission
     */
    setupEditFormSubmission() {
        const form = document.getElementById('editBookingForm');
        if (!form) return;

        // Remove existing event listeners by cloning the form
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        
        // Handle form submission (Update button)
        newForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.submitEditForm(newForm);
        });

        // Handle Cancel button specifically
        const cancelButton = newForm.querySelector('.modal-close, [data-action="cancel"]');
        if (cancelButton) {
            cancelButton.addEventListener('click', (e) => {
                e.preventDefault();
                this.closeModal();
            });
        }

        // Handle Update button specifically (in case it's not a submit button)
        const updateButton = newForm.querySelector('[data-action="update"], [type="submit"]');
        if (updateButton && updateButton.type !== 'submit') {
            updateButton.addEventListener('click', async (e) => {
                e.preventDefault();
                await this.submitEditForm(newForm);
            });
        }
    }

    /**
     * Submit edit form via AJAX
     */
    async submitEditForm(form) {
        try {
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Validate time slot before submission
            const isValid = await this.validateTimeSlot(data);
            if (!isValid) {
                return; // Validation error already shown
            }

            const response = await fetch(`${this.apiEndpoints.bookingUpdate}/${this.currentBookingId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccessMessage(result.message);
                this.closeModal();
                this.refreshBookingTable();
            } else {
                this.showErrorMessage(result.message);
                if (result.errors) {
                    this.displayValidationErrors(result.errors);
                }
            }

        } catch (error) {
            this.showErrorMessage('Failed to update booking');
        }
    }

    /**
     * Validate time slot for conflicts
     */
    async validateTimeSlot(data) {
        try {
            const response = await fetch(this.apiEndpoints.validateTimeSlot, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    meeting_room_id: data.meeting_room_id,
                    date: data.date,
                    start_time: data.start_time,
                    end_time: data.end_time,
                    exclude_booking_id: this.currentBookingId
                })
            });

            const result = await response.json();

            if (!result.success && result.conflicts && result.conflicts.length > 0) {
                const conflictMessages = result.conflicts.map(conflict => 
                    `${conflict.nama} (${conflict.start_time} - ${conflict.end_time})`
                ).join(', ');
                
                this.showErrorMessage(`Time slot conflicts with: ${conflictMessages}`);
                return false;
            }

            return true;
        } catch (error) {
            return true; // Allow submission if validation fails
        }
    }

    /**
     * Handle delete booking button click
     */
    handleDeleteBooking(button) {
        this.currentBookingId = button.dataset.id;
        const modal = document.getElementById('deleteBookingModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // Setup delete form submission
        const deleteForm = document.getElementById('deleteBookingForm');
        if (deleteForm) {
            const newDeleteForm = deleteForm.cloneNode(true);
            deleteForm.parentNode.replaceChild(newDeleteForm, deleteForm);
            
            newDeleteForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.submitDeleteForm();
            });
        }
    }

    /**
     * Submit delete form via AJAX
     */
    async submitDeleteForm() {
        try {
            const response = await fetch(`${this.apiEndpoints.bookingDelete}/${this.currentBookingId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showSuccessMessage(result.message);
                this.closeModal();
                this.refreshBookingTable();
            } else {
                this.showErrorMessage(result.message);
            }

        } catch (error) {
            this.showErrorMessage('Failed to delete booking');
        }
    }

    /**
     * Close all modals
     */
    closeModal() {
        document.querySelectorAll('.modal-container').forEach(modal => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        });
        this.currentBookingId = null;
    }

    /**
     * Refresh booking table
     */
    refreshBookingTable() {
        const activeFilter = localStorage.getItem('activeFilter') || 'today';
        const activeDate = localStorage.getItem('activeDate');
        this.fetchBookings(activeFilter, activeDate);
    }

    /**
     * Fetch bookings data
     */
    async fetchBookings(filter, date = null, page = 1) {
        const loadingOverlay = document.getElementById('loadingOverlay');
        if (loadingOverlay) loadingOverlay.classList.remove('hidden');

        let url = new URL(window.location.origin + '/admin/dashboard/bookings');
        url.searchParams.append('filter', filter);
        url.searchParams.append('page', page);
        if (date) {
            url.searchParams.append('date', date);
        }

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            this.updateUI(data, filter);
            this.updateExportLink(filter, date);
        } catch (error) {
            const tableBody = document.getElementById('bookingTableBody');
            if (tableBody) {
                tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-red-500">Failed to load data. Please try again. Error: ${error.message}</td></tr>`;
            }
        } finally {
            if (loadingOverlay) loadingOverlay.classList.add('hidden');
        }
    }

    /**
     * Update UI with fetched data
     */
    updateUI(data, filter) {
        this.updateStatsWithData(data, filter);
        this.updateBookingsTable(data.bookings.data);
        this.updatePagination(data.bookings);
        
        // Update pagination info
        const showingCount = document.getElementById('showingCount');
        const totalCount = document.getElementById('totalCount');
        const totalBookingsCount = document.getElementById('totalBookingsCount');
        
        if (showingCount) showingCount.textContent = data.bookings.from || 0;
        if (totalCount) totalCount.textContent = data.bookings.to || 0;
        if (totalBookingsCount) totalBookingsCount.textContent = data.bookings.total || 0;
    }

    /**
     * Update stats cards with data
     */
    updateStatsWithData(data, filter) {
        const totalBookingsEl = document.getElementById('totalBookings');
        if (totalBookingsEl) totalBookingsEl.textContent = data.stats.totalBookings.count || '0';
        
        let comparisonText = `${data.stats.totalBookings.count} bookings `;
        switch (filter) {
            case 'week':
                comparisonText += 'this week';
                break;
            case 'month':
                comparisonText += 'this month';
                break;
            case 'custom':
                comparisonText += 'in period';
                break;
            case 'today':
            default:
                comparisonText += 'today';
                break;
        }
        const bookingComparisonEl = document.getElementById('bookingComparison');
        if (bookingComparisonEl) bookingComparisonEl.textContent = comparisonText;

        this.updateTrendIndicator('bookingTrend', data.stats.bookingComparison);
        this.updateTrendIndicator('usageTrend', data.stats.usageRate.trend);

        const roomUsageEl = document.getElementById('roomUsage');
        if (roomUsageEl) roomUsageEl.textContent = `${data.stats.usageRate.percentage || 0}%`;
        
        const usageBar = document.getElementById('usageBar');
        if (usageBar) {
            usageBar.style.width = `${data.stats.usageRate.percentage || 0}%`;
        }
        
        const mostUsedRoomEl = document.getElementById('mostUsedRoom');
        if (mostUsedRoomEl) mostUsedRoomEl.textContent = data.stats.mostUsedRoom.name || 'N/A';
        
        const roomUsageHoursEl = document.getElementById('roomUsageHours');
        if (roomUsageHoursEl) roomUsageHoursEl.textContent = `${data.stats.mostUsedRoom.hours || 0} hours`;
        
        const topDeptsContainer = document.getElementById('topDepartments');
        if (topDeptsContainer) {
            topDeptsContainer.innerHTML = '';
            if (data.stats.topDepartments && data.stats.topDepartments.length > 0) {
                data.stats.topDepartments.forEach(dept => {
                    const deptEl = document.createElement('span');
                    deptEl.className = 'px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded';
                    deptEl.textContent = dept.name;
                    topDeptsContainer.appendChild(deptEl);
                });
            } else {
                topDeptsContainer.innerHTML = '<span class="text-xs text-gray-500">No department data.</span>';
            }
        }
    }

    /**
     * Update trend indicator
     */
    updateTrendIndicator(elementId, trendData) {
        const trendEl = document.getElementById(elementId);
        if (!trendEl || typeof trendData === 'undefined') {
            if (trendEl) trendEl.classList.add('hidden');
            return;
        }
        
        trendEl.classList.remove('hidden');

        const icon = trendEl.querySelector('.trend-icon');
        const textEl = trendEl.querySelector('.trend-text');
        
        // Reset classes
        trendEl.classList.remove('bg-green-100', 'text-green-800', 'bg-red-100', 'text-red-800', 'bg-gray-100', 'text-gray-800');
        if (icon) icon.classList.remove('fa-arrow-up', 'fa-arrow-down', 'fa-equals');

        const change = trendData.percentage_change;
        
        if (change === 0) {
            if (textEl) textEl.textContent = 'No Change';
            trendEl.classList.add('bg-gray-100', 'text-gray-800');
            if (icon) icon.classList.add('fa-equals');
        } else {
            if (textEl) textEl.textContent = `${Math.abs(change)}%`;
            if (trendData.is_increase) {
                trendEl.classList.add('bg-green-100', 'text-green-800');
                if (icon) icon.classList.add('fa-arrow-up');
            } else {
                trendEl.classList.add('bg-red-100', 'text-red-800');
                if (icon) icon.classList.add('fa-arrow-down');
            }
        }
    }

    /**
     * Update bookings table
     */
    updateBookingsTable(bookings) {
        const tableBody = document.getElementById('bookingTableBody');
        if (!tableBody) {
            return;
        }

        tableBody.innerHTML = '';
        
        if (!bookings || bookings.length === 0) {
            tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">No booking data for this period.</td></tr>`;
            return;
        }

        bookings.forEach((booking, index) => {
            let statusClass = '';
            const status = booking.dynamic_status || 'Scheduled';
            switch (status) {
                case 'Ongoing':
                    statusClass = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'Completed':
                    statusClass = 'bg-green-100 text-green-800';
                    break;
                case 'Scheduled':
                default:
                    statusClass = 'bg-blue-100 text-blue-800';
                    break;
            }

            const roomName = booking.meeting_room ? booking.meeting_room.name : 'N/A';
            const departmentName = (booking.user && booking.user.department) ? booking.user.department.name : (booking.department || 'N/A');
            const userName = booking.user ? booking.user.name : (booking.nama || 'Deleted User');
            const startTime = booking.start_time ? booking.start_time.substring(0,5) : '00:00';
            const endTime = booking.end_time ? booking.end_time.substring(0,5) : '00:00';

            const row = `
                <tr class="booking-row" data-id="${booking.id}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${roomName}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${departmentName}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${userName}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${booking.date || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">${startTime} - ${endTime}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                            ${status}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="/admin/bookings/${booking.id}" title="Details" class="p-2 text-gray-400 hover:text-blue-500 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" title="Edit" class="edit-booking-btn p-2 text-gray-400 hover:text-primary transition-colors"
                                data-id="${booking.id}"
                                data-name="${userName}"
                                data-department="${departmentName}"
                                data-room-id="${booking.meeting_room_id || ''}"
                                data-date="${booking.date || ''}"
                                data-start-time="${startTime}"
                                data-end-time="${endTime}"
                                data-description="${booking.description || ''}"
                                data-booking-type="${booking.booking_type || 'internal'}"
                                data-external-description="${booking.external_description || ''}">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                            <button type="button" title="Delete" class="delete-booking-btn p-2 text-gray-400 hover:text-red-500 transition-colors" data-id="${booking.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
        
        // Re-attach event listeners after table update
        this.attachActionListeners();
    }

    /**
     * Update pagination
     */
    updatePagination(paginationData) {
        const prevPageButton = document.getElementById('prevPage');
        const nextPageButton = document.getElementById('nextPage');
        
        if (!prevPageButton || !nextPageButton) return;

        let currentPage = paginationData.current_page;

        // Previous Button
        if (paginationData.prev_page_url) {
            prevPageButton.disabled = false;
            prevPageButton.onclick = () => {
                const filter = localStorage.getItem('activeFilter') || 'today';
                const date = localStorage.getItem('activeDate');
                this.fetchBookings(filter, date, currentPage - 1);
            };
        } else {
            prevPageButton.disabled = true;
            prevPageButton.onclick = null;
        }

        // Next Button
        if (paginationData.next_page_url) {
            nextPageButton.disabled = false;
            nextPageButton.onclick = () => {
                const filter = localStorage.getItem('activeFilter') || 'today';
                const date = localStorage.getItem('activeDate');
                this.fetchBookings(filter, date, currentPage + 1);
            };
        } else {
            nextPageButton.disabled = true;
            nextPageButton.onclick = null;
        }
    }

    /**
     * Update export link
     */
    updateExportLink(filter, date = null) {
        const exportLink = document.getElementById('export-link');
        if (!exportLink) return;

        const originalHref = exportLink.getAttribute('data-original-href') || exportLink.href;
        if (!exportLink.getAttribute('data-original-href')) {
            exportLink.setAttribute('data-original-href', originalHref);
        }

        const params = new URLSearchParams();
        params.append('filter', filter);
        if (date) {
            params.append('date', date);
        }
        exportLink.href = `${originalHref}?${params.toString()}`;
    }

    /**
     * Set active filter button
     */
    setActiveFilter(activeButton) {
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(btn => {
            btn.classList.remove('bg-primary', 'text-white');
            btn.classList.add('bg-gray-100', 'text-gray-700');
        });
        activeButton.classList.remove('bg-gray-100', 'text-gray-700');
        activeButton.classList.add('bg-primary', 'text-white');
    }

    /**
     * Restore state and load data
     */
    restoreStateAndLoad() {
        const savedFilter = localStorage.getItem('activeFilter') || 'today';
        const savedDate = localStorage.getItem('activeDate');
        let filterToLoad = savedFilter;
        let dateToLoad = savedDate;

        let activeBtn;

        if (savedFilter === 'custom' && savedDate) {
            activeBtn = document.getElementById('filter-custom');
            if (this.flatpickrInstance) this.flatpickrInstance.setDate(savedDate, false);
        } else {
            activeBtn = document.getElementById(`filter-${savedFilter}`) || document.getElementById('filter-today');
            dateToLoad = null; 
        }
        
        this.setActiveFilter(activeBtn);
        this.fetchBookings(filterToLoad, dateToLoad);
    }

    /**
     * Display validation errors
     */
    displayValidationErrors(errors) {
        // Clear previous errors
        document.querySelectorAll('.error-message').forEach(el => el.remove());

        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                errorDiv.textContent = errors[field][0];
                input.parentNode.appendChild(errorDiv);
            }
        });
    }

    /**
     * Show success message
     */
    showSuccessMessage(message) {
        this.showToast(message, 'success');
    }

    /**
     * Show error message
     */
    showErrorMessage(message) {
        this.showToast(message, 'error');
    }

    /**
     * Show toast notification
     */
    showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full`;
        
        if (type === 'success') {
            toast.classList.add('bg-green-500');
        } else if (type === 'error') {
            toast.classList.add('bg-red-500');
        } else {
            toast.classList.add('bg-blue-500');
        }
        
        toast.textContent = message;
        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Animate out and remove
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 3000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new DashboardBookingManager();
});