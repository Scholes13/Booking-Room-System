class ReportGenerator {
    constructor(filterManager) {
        this.filterManager = filterManager;
        this.charts = {};
        this.initializeElements();
        this.initializeEventListeners();
    }

    initializeElements() {
        this.viewReportBtn = document.getElementById('viewReport');
        this.loadingElement = document.getElementById('loading');
        this.reportContent = document.getElementById('report_content');
    }

    initializeEventListeners() {
        this.viewReportBtn.addEventListener('click', () => this.loadReport());
    }

    async loadReport() {
        const params = this.filterManager.getFilterParams();
        console.log('Filter Parameters:', params);

        try {
            this.showLoading();

            const response = await fetch(`${window.location.origin}/admin/reports/data`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(params)
            });

            if (!response.ok) {
                throw new Error('Failed to fetch report data');
            }

            const data = await response.json();
            console.log('Report Data:', data);
            this.displayReport(data, params.report_type);

        } catch (error) {
            console.error('Report loading error:', error);
            this.showError('Failed to load report data. Please try again.');
        } finally {
            this.hideLoading();
        }
    }

    displayReport(data, reportType) {
        if (!data || data.no_data) {
            this.reportContent.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-info-circle text-4xl mb-3"></i>
                    <p>${data?.message || 'No data available'}</p>
                </div>
            `;
            return;
        }

        let html = '';
        switch (reportType) {
            case 'rooms':
                html = this.generateRoomsReport(data);
                break;
            case 'departments':
                html = this.generateDepartmentsReport(data);
                break;
            case 'bookings':
                html = this.generateBookingsReport(data);
                break;
        }

        this.reportContent.innerHTML = html;
    }

    /* ===================== REPORT: ROOMS ===================== */
    generateRoomsReport(data) {
        return `
            <div class="space-y-6">
                <!-- Stats Cards for Rooms -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    ${this.createStatCard('Total Rooms', data.total_rooms || 0, 'bg-blue-50 text-blue-900')}
                    ${this.createStatCard('Average Usage', `${data.average_usage || 0}%`, 'bg-green-50 text-green-900')}
                    ${this.createStatCard('Most Used Room', data.most_used_room || '-', 'bg-purple-50 text-purple-900')}
                </div>

                <!-- Table for Room Details -->
                <div class="table-container mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Room Details</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Bookings</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usage %</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.rooms.map(room => `
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">${room.name || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${room.capacity || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${room.total_bookings || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${room.hours_used || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${room.usage_percentage || 0}%</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm
                                            ${room.status === 'available' 
                                                ? 'bg-green-100 text-green-800' 
                                                : 'bg-red-100 text-red-800'}">
                                            ${room.status || '-'}
                                        </span>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    /* ===================== REPORT: DEPARTMENTS ===================== */
    generateDepartmentsReport(data) {
        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    ${this.createStatCard('Total Departments', data.total_departments || 0, 'bg-blue-50 text-blue-900')}
                </div>

                <div class="table-container mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Departments Details</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Bookings</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average Duration</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.departments.map(dept => `
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.department || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.total_bookings || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.hours_used || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.average_duration || '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    /* ===================== REPORT: BOOKINGS ===================== */
    generateBookingsReport(data) {
        // Asumsikan data.category_stats = { meeting: {count, percentage}, interview: {...}, ... }
        const catStats = data.category_stats || {};

        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    ${this.createStatCard('Total Bookings', data.total_bookings || 0, 'bg-blue-50 text-blue-900')}

                    <!-- Meeting -->
                    ${this.createStatCard(
                        'Meeting',
                        this.formatCount(catStats?.meeting?.count, catStats?.meeting?.percentage),
                        'bg-blue-100 text-blue-900'
                    )}

                    <!-- Interview -->
                    ${this.createStatCard(
                        'Interview',
                        this.formatCount(catStats?.interview?.count, catStats?.interview?.percentage),
                        'bg-green-100 text-green-900'
                    )}

                    <!-- Training -->
                    ${this.createStatCard(
                        'Training',
                        this.formatCount(catStats?.training?.count, catStats?.training?.percentage),
                        'bg-orange-100 text-orange-900'
                    )}

                    <!-- Hosting -->
                    ${this.createStatCard(
                        'Hosting',
                        this.formatCount(catStats?.hosting?.count, catStats?.hosting?.percentage),
                        'bg-red-100 text-red-900'
                    )}
                </div>

                <div class="table-container mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bookings Details</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.bookings.map(booking => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.nama || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.department || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.date || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.start_time || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.end_time || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.meeting_room || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${booking.description || '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    /* Membuat card stat umum */
    createStatCard(title, value, bgColorClass) {
        return `
            <div class="p-4 rounded-lg font-semibold text-center transition transform hover:-translate-y-1 ${bgColorClass}">
                <h3 class="text-sm">${title}</h3>
                <p class="mt-2 text-2xl">${value}</p>
            </div>
        `;
    }

    /* Format tampilan count + percentage */
    formatCount(count, percentage) {
        const c = count || 0;
        const p = (percentage !== undefined) ? `${percentage}%` : '0%';
        return `${c} (${p})`;
    }

    showLoading() {
        this.loadingElement.classList.remove('hidden');
        this.reportContent.classList.add('opacity-50');
    }

    hideLoading() {
        this.loadingElement.classList.add('hidden');
        this.reportContent.classList.remove('opacity-50');
    }

    showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }
}
