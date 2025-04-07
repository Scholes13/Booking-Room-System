class ReportGenerator {
    constructor(filterManager) {
        this.filterManager = filterManager;
        this.reportContent = document.getElementById('report_content');
        this.loadingIndicator = document.getElementById('loading');
        this.viewReportBtn = document.getElementById('viewReport');
        
        // Validasi elemen yang dibutuhkan
        if (!this.reportContent || !this.loadingIndicator || !this.viewReportBtn) {
            console.error('Required elements for report generation are missing');
            return;
        }
        
        // Inisialisasi event listeners
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        if (!this.viewReportBtn) return;
        
        this.viewReportBtn.addEventListener('click', () => this.loadReport());
    }

    async loadReport() {
        if (!this.filterManager || !this.reportContent || !this.loadingIndicator) {
            console.error('Required components for report generation are not initialized');
            return;
        }

        try {
            // Tampilkan loading state
            this.showLoading();

            // Ambil filter parameters
            const params = this.filterManager.getFilterParams();
            if (!params || Object.keys(params).length === 0) {
                throw new Error('No filter parameters available');
            }

            // Determine the correct endpoint based on the current URL path
            let endpoint = '/admin/reports/data';
            if (window.location.pathname.includes('/bas/')) {
                endpoint = '/bas/reports/data';
            } else if (window.location.pathname.includes('/superadmin/')) {
                endpoint = '/superadmin/reports/data';
            }

            // Use the full URL with origin
            const url = `${window.location.origin}${endpoint}`;
            
            console.log('Fetching report from URL:', url);
            
            // Kirim request ke API
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(params)
            });

            if (!response.ok) {
                throw new Error(`Report fetch failed with status: ${response.status}`);
            }

            const data = await response.json();
            
            // Render report content
            this.renderReport(data, params);
            
        } catch (error) {
            console.error('Error loading report:', error);
            this.showError(error.message);
        } finally {
            // Sembunyikan loading state
            this.hideLoading();
        }
    }

    renderReport(data, params) {
        if (!this.reportContent) return;

        // If data contains HTML from server (server version), use it directly
        if (data.html) {
            this.reportContent.innerHTML = data.html;
            
            // Render charts if available
            if (data.chartData && Array.isArray(data.chartData)) {
                this.renderCharts(data.chartData);
            }
            return;
        }
        
        // If data is in raw format (no html property), we'll handle client-side rendering
        // Check if we have data object directly or in data.raw
        const reportData = data.raw || data;
        
        // Check if data is empty or has error flag
        if (reportData.error || reportData.no_data) {
            this.showError(reportData.message || 'No data available for the selected period');
            return;
        }

        let html = '';
        const reportType = params.report_type;
        
        switch (reportType) {
            case 'rooms':
                html = this.generateRoomsReport(reportData);
                break;
            case 'departments':
                html = this.generateDepartmentsReport(reportData);
                break;
            case 'bookings':
                html = this.generateBookingsReport(reportData);
                break;
        }

        this.reportContent.innerHTML = html;
    }

    renderCharts(chartDataArray) {
        // Loop melalui setiap data chart
        chartDataArray.forEach((chartData, index) => {
            const canvasId = `chart-${index}`;
            const canvas = document.getElementById(canvasId);
            
            if (!canvas) {
                console.error(`Canvas element with ID ${canvasId} not found`);
                return;
            }

            try {
                // Create chart instance
                new Chart(canvas, {
                    type: chartData.type,
                    data: chartData.data,
                    options: chartData.options
                });
            } catch (error) {
                console.error(`Error rendering chart ${canvasId}:`, error);
            }
        });
    }

    showLoading() {
        if (!this.loadingIndicator || !this.reportContent) return;
        
        this.loadingIndicator.classList.remove('hidden');
        this.reportContent.classList.add('hidden');
    }

    hideLoading() {
        if (!this.loadingIndicator || !this.reportContent) return;
        
        this.loadingIndicator.classList.add('hidden');
        this.reportContent.classList.remove('hidden');
    }

    showError(message) {
        if (!this.reportContent) return;
        
        this.reportContent.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-exclamation-triangle text-4xl mb-3 text-yellow-500"></i>
                <p>${message || 'An error occurred while generating the report'}</p>
            </div>
        `;
    }

    // Client-side HTML generation methods
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
                            ${data.rooms ? data.rooms.map(room => `
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
                            `).join('') : ''}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

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
                            ${data.departments ? data.departments.map(dept => `
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.department || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.total_bookings || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.hours_used || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.average_duration || '-'}</td>
                                </tr>
                            `).join('') : ''}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    generateBookingsReport(data) {
        // Prepare category stats if available
        const catStats = data.category_stats || {};

        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    ${this.createStatCard('Total Bookings', data.total_bookings || 0, 'bg-blue-50 text-blue-900')}

                    <!-- Meeting -->
                    ${this.createStatCard(
                        'Meeting',
                        this.formatCount(catStats.meeting?.count, catStats.meeting?.percentage),
                        'bg-blue-100 text-blue-900'
                    )}

                    <!-- Interview -->
                    ${this.createStatCard(
                        'Interview',
                        this.formatCount(catStats.interview?.count, catStats.interview?.percentage),
                        'bg-green-100 text-green-900'
                    )}

                    <!-- Training -->
                    ${this.createStatCard(
                        'Training',
                        this.formatCount(catStats.training?.count, catStats.training?.percentage),
                        'bg-orange-100 text-orange-900'
                    )}

                    <!-- Hosting -->
                    ${this.createStatCard(
                        'Hosting',
                        this.formatCount(catStats.hosting?.count, catStats.hosting?.percentage),
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
                            ${data.bookings ? data.bookings.map(booking => `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.nama || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.department || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.date || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.start_time || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.end_time || '-'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${booking.meeting_room || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${booking.description || '-'}</td>
                                </tr>
                            `).join('') : ''}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

    /* Create stat card */
    createStatCard(title, value, bgColorClass) {
        return `
            <div class="p-4 rounded-lg font-semibold text-center transition transform hover:-translate-y-1 ${bgColorClass}">
                <h3 class="text-sm">${title}</h3>
                <p class="mt-2 text-2xl">${value}</p>
            </div>
        `;
    }

    /* Format count + percentage */
    formatCount(count, percentage) {
        const c = count || 0;
        const p = (percentage !== undefined) ? `${percentage}%` : '0%';
        return `${c} (${p})`;
    }
}
