class ActivityReportGenerator {
    constructor(filterManager) {
        this.filterManager = filterManager;
        this.itemsPerPage = 10; // Jumlah item per halaman
        this.currentPage = 1;   // Halaman saat ini
    }

    async generateReport() {
        try {
            const data = await this.filterManager.fetchReportData();
            this.reportData = data; // Simpan data untuk pagination
            this.currentPage = 1;   // Reset ke halaman pertama
            return this.renderReport(data);
        } catch (error) {
            console.error('Error generating report:', error);
            throw error;
        }
    }

    renderReport(data) {
        if (data.no_data) {
            return `
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-chart-line text-4xl mb-3"></i>
                    <p>${data.message || 'No data available for the selected criteria'}</p>
                </div>
            `;
        }

        switch (this.filterManager.filters.report_type) {
            case 'employee_activity':
                return this.renderEmployeeActivityReport(data);
            case 'department_activity':
                return this.renderDepartmentActivityReport(data);
            case 'location_activity':
                return this.renderLocationActivityReport(data);
            case 'detailed_activity':
                return this.renderDetailedActivityReport(data);
            default:
                return '<p>Unsupported report type</p>';
        }
    }

    renderEmployeeActivityReport(data) {
        // Sort data by start_datetime (ascending)
        if (data.activities && Array.isArray(data.activities)) {
            data.activities.sort((a, b) => {
                const aDate = new Date(a.start_datetime);
                const bDate = new Date(b.start_datetime);
                return aDate - bDate;
            });
        }
        
        // Pagination logic
        const activities = this.getPaginatedItems(data.activities);
        const totalPages = this.getTotalPages(data.activities.length);

        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Activities</h3>
                        <p class="text-3xl font-bold">${data.total_activities}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${activities.map(activity => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900" data-label="Name">${activity.name || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Department">${activity.department || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Location">${activity.city ? `${activity.city}, ${activity.province}` : 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Activity Type">
                                            <span class="px-2 py-1 text-xs rounded-full inline-block" style="background-color: rgba(36, 68, 140, 0.1); border-left: 3px solid #24448c;">
                                                ${activity.category || 'N/A'}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Duration">${activity.total_days || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" data-label="Description">${activity.description || 'N/A'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    
                    ${this.renderPagination(data.activities.length)}
                </div>
            </div>
        `;
    }

    renderDepartmentActivityReport(data) {
        // Pagination logic
        const departmentStats = this.getPaginatedItems(data.department_stats);
        const totalPages = this.getTotalPages(data.department_stats.length);

        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Activities</h3>
                        <p class="text-3xl font-bold">${data.total_activities}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Hours</h3>
                        <p class="text-3xl font-bold">${data.total_hours}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Activities</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours Used</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Days</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${departmentStats.map(dept => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900" data-label="Department">${dept.name || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Total Activities">${dept.total_activities || '0'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Hours Used">${dept.hours_used || '0'} hours</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Total Days">${dept.total_days || '0'} days</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    
                    ${this.renderPagination(data.department_stats.length)}
                </div>
            </div>
        `;
    }

    renderLocationActivityReport(data) {
        // Pagination logic
        const locationStats = this.getPaginatedItems(data.location_stats);
        const totalPages = this.getTotalPages(data.location_stats.length);

        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Activities</h3>
                        <p class="text-3xl font-bold">${data.total_activities}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Locations</h3>
                        <p class="text-3xl font-bold">${data.total_locations}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Activities</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours Used</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Meetings</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invitations</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Surveys</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${locationStats.map(loc => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900" data-label="Location">${loc.location || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Total Activities">${loc.total_activities || '0'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Hours Used">${loc.hours_used || '0'} hours</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Meetings">${loc.activities_by_type.Meeting || '0'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Invitations">${loc.activities_by_type.Invitation || '0'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Surveys">${loc.activities_by_type.Survey || '0'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    
                    ${this.renderPagination(data.location_stats.length)}
                </div>
            </div>
        `;
    }

    renderDetailedActivityReport(data) {
        // Sort data by start_datetime (ascending)
        if (data.activities && Array.isArray(data.activities)) {
            data.activities.sort((a, b) => {
                const aDate = new Date(a.start_datetime);
                const bDate = new Date(b.start_datetime);
                return aDate - bDate;
            });
        }
        
        // Pagination logic
        const activities = this.getPaginatedItems(data.activities);
        const totalPages = this.getTotalPages(data.activities.length);

        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Total Activities</h3>
                        <p class="text-3xl font-bold">${data.total_activities}</p>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <h3 class="text-lg font-semibold">Date Range</h3>
                        <p class="text-xl font-medium">${data.date_range?.start || 'N/A'} - ${data.date_range?.end || 'N/A'}</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Range</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${activities.map(activity => `
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900" data-label="Name">${activity.nama || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Department">${activity.department || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Location">${activity.location || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Activity Type">
                                            <span class="px-2 py-1 text-xs rounded-full inline-block" style="background-color: ${this.getActivityColor(activity.activity_type)}">
                                                ${activity.activity_type || 'N/A'}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Time Range">${activity.time_range || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500" data-label="Duration">${activity.duration || 'N/A'}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" data-label="Description">${activity.description || 'N/A'}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                    
                    ${this.renderPagination(data.activities.length)}
                </div>
            </div>
        `;
    }

    // Fungsi untuk mendapatkan warna berdasarkan tipe aktivitas
    getActivityColor(activityType) {
        const colors = {
            'Meeting': 'rgba(36, 68, 140, 0.1)',
            'Invitation': 'rgba(94, 53, 177, 0.1)',
            'Survey': 'rgba(230, 74, 25, 0.1)',
            'Training': 'rgba(46, 125, 50, 0.1)',
            'Conference': 'rgba(21, 101, 192, 0.1)',
            'Business Trip': 'rgba(255, 111, 0, 0.1)'
        };
        
        return colors[activityType] || 'rgba(189, 189, 189, 0.1)';
    }

    // Fungsi pagination
    getPaginatedItems(items) {
        if (!items) return [];
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        return items.slice(startIndex, startIndex + this.itemsPerPage);
    }

    getTotalPages(totalItems) {
        return Math.ceil(totalItems / this.itemsPerPage);
    }

    renderPagination(totalItems) {
        const totalPages = this.getTotalPages(totalItems);
        
        if (totalPages <= 1) return '';
        
        let paginationHtml = `
            <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">${(this.currentPage - 1) * this.itemsPerPage + 1}</span>
                            to
                            <span class="font-medium">${Math.min(this.currentPage * this.itemsPerPage, totalItems)}</span>
                            of
                            <span class="font-medium">${totalItems}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        `;
        
        // Previous page button
        paginationHtml += `
            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${this.currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}" 
                data-page="${this.currentPage - 1}" 
                ${this.currentPage === 1 ? 'aria-disabled="true"' : ''}>
                <span class="sr-only">Previous</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        `;
        
        // Page numbers
        const maxPagesToShow = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
        
        if (endPage - startPage + 1 < maxPagesToShow) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `
                <a href="#" 
                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 ${i === this.currentPage ? 'bg-primary text-white' : 'bg-white text-gray-700'} 
                        text-sm font-medium hover:bg-gray-50" 
                    data-page="${i}">
                    ${i}
                </a>
            `;
        }
        
        // Next page button
        paginationHtml += `
            <a href="#" 
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 ${this.currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}" 
                data-page="${this.currentPage + 1}" 
                ${this.currentPage === totalPages ? 'aria-disabled="true"' : ''}>
                <span class="sr-only">Next</span>
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        `;
        
        paginationHtml += `
                        </nav>
                    </div>
                </div>
            </div>
        `;
        
        // Attach event listener after rendering
        setTimeout(() => {
            document.querySelectorAll('[data-page]').forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const page = parseInt(link.getAttribute('data-page'));
                    
                    // Validate page
                    if (page >= 1 && page <= totalPages && page !== this.currentPage) {
                        this.currentPage = page;
                        document.getElementById('report_content').innerHTML = this.renderReport(this.reportData);
                    }
                });
            });
        }, 0);
        
        return paginationHtml;
    }
}