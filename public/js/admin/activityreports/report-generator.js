/**
* ActivityReportGenerator
* -----------------------
* Mirip "ReportGenerator" booking, tetapi endpoint "/admin/activity/data"
* dan reportType "employee_activity" / "department_activity".
*/

class ActivityReportGenerator {
    constructor(filterManager) {
        this.filterManager = filterManager;
        this.initializeElements();
        this.initializeEventListeners();
    }
 
    initializeElements() {
        this.viewReportBtn = document.getElementById('viewReport');
        this.loadingElement = document.getElementById('loading');
        this.reportContent = document.getElementById('report_content');
    }
 
    initializeEventListeners() {
        if (this.viewReportBtn) {
            this.viewReportBtn.addEventListener('click', () => this.loadReport());
        }
    }
 
    async loadReport() {
        const params = this.filterManager.getFilterParams();
        console.log('Activity Filter Params:', params);
 
        try {
            this.showLoading();
            
            // Determine the correct prefix based on the URL path
            let prefix = '/admin';
            if (window.location.pathname.includes('/bas/')) {
                prefix = '/bas';
            }
            
            let endpoint = `${prefix}/activity/data`;
            
            // Use different endpoint for detailed reports
            if (params.report_type === 'detailed_activity') {
                endpoint = `${prefix}/activity/detailed`;
            }
            
            console.log(`[ActivityReportGenerator] Fetch => ${endpoint}`);
 
            const response = await fetch(`${window.location.origin}${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(params)
            });
 
            console.log('[ActivityReportGenerator] Response status:', response.status);
 
            if (!response.ok) {
                throw new Error('Failed to fetch activity report data');
            }
 
            const data = await response.json();
            console.log('Activity Report Data:', data);
            this.displayReport(data, params.report_type);
 
        } catch (error) {
            console.error('Activity report loading error:', error);
            this.showError('Failed to load activity report data. Please try again.');
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
            case 'employee_activity':
                html = this.generateEmployeeActivityReport(data);
                break;
            case 'department_activity':
                html = this.generateDepartmentActivityReport(data);
                break;
            case 'location_activity':
                html = this.generateLocationActivityReport(data);
                break;
            case 'detailed_activity':
                html = this.generateDetailedActivityReport(data);
                break;
            default:
                html = `
                    <div class="text-center py-12 text-gray-500">
                        <p>Unsupported report type: ${reportType}</p>
                    </div>
                `;
                break;
        }
 
        this.reportContent.innerHTML = html;
    }
 
    generateEmployeeActivityReport(data) {
        console.log('Category Stats:', data.category_stats);
        
        const catStats = data.category_stats || {};
 
        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    ${this.createStatCard('Total Activities', data.total_activities || 0, 'bg-blue-50 text-blue-900')}
 
                    ${this.createStatCard(
                        'Meeting',
                        this.formatCount(catStats?.Meeting?.count, catStats?.Meeting?.percentage),
                        'bg-blue-100 text-blue-900'
                    )}
                    ${this.createStatCard(
                        'Invitation',
                        this.formatCount(catStats?.Invitation?.count, catStats?.Invitation?.percentage),
                        'bg-green-100 text-green-900'
                    )}
                    ${this.createStatCard(
                        'Survey',
                        this.formatCount(catStats?.Survey?.count, catStats?.Survey?.percentage),
                        'bg-orange-100 text-orange-900'
                    )}
                </div>
 
                <div class="table-container mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Activities Details</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Days</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${data.activities.map(act => `
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">${act.name || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${act.department || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${this.formatDateTime(act.start_datetime) || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${this.formatDateTime(act.end_datetime) || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${act.total_days || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${act.category || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${act.description || '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
 
    generateDepartmentActivityReport(data) {
        const catStats = data.category_stats || {};
        const departmentStats = data.department_stats || [];

        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    ${this.createStatCard('Total Activities', data.total_activities || 0, 'bg-blue-50 text-blue-900')}

                    ${this.createStatCard(
                        'Meeting',
                        this.formatCount(catStats?.Meeting?.count, catStats?.Meeting?.percentage),
                        'bg-blue-100 text-blue-900'
                    )}
                    ${this.createStatCard(
                        'Invitation',
                        this.formatCount(catStats?.Invitation?.count, catStats?.Invitation?.percentage),
                        'bg-green-100 text-green-900'
                    )}
                    ${this.createStatCard(
                        'Survey',
                        this.formatCount(catStats?.Survey?.count, catStats?.Survey?.percentage),
                        'bg-orange-100 text-orange-900'
                    )}
                </div>

                <div class="table-container mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Department Activities</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Activities</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours Used</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Days</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${departmentStats.map(dept => `
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.department || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.total_activities || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.hours_used || 0} hours</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.total_days || 0} days</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
 
    generateLocationActivityReport(data) {
        const locationStats = data.location_stats || [];
        const totalActivities = data.total_activities || 0;

        return `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    ${this.createStatCard('Total Activities', totalActivities, 'bg-blue-50 text-blue-900')}
                    ${this.createStatCard('Unique Locations', this.countUniqueValues(locationStats, 'location'), 'bg-purple-100 text-purple-900')}
                    ${this.createStatCard('Total Hours', data.total_hours || 0, 'bg-indigo-100 text-indigo-900')}
                </div>

                <div class="table-container mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Activity Locations</h3>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activities</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meeting</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invitation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Survey</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            ${locationStats.map(loc => `
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">${loc.location || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${loc.total_activities || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${loc.hours_used || 0} hours</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${loc.activities_by_type?.Meeting || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${loc.activities_by_type?.Invitation || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${loc.activities_by_type?.Survey || 0}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
 
    createStatCard(title, value, bgColorClass) {
        return `
            <div class="p-4 rounded-lg font-semibold text-center transition transform hover:-translate-y-1 ${bgColorClass}">
                <h3 class="text-sm">${title}</h3>
                <p class="mt-2 text-2xl">${value}</p>
            </div>
        `;
    }
 
    formatCount(count, percentage) {
        const c = count || 0;
        const p = (percentage !== undefined) ? `${percentage}%` : '0%';
        return `${c} (${p})`;
    }
 
    showLoading() {
        this.loadingElement?.classList.remove('hidden');
        this.reportContent?.classList.add('opacity-50');
    }
 
    hideLoading() {
        this.loadingElement?.classList.add('hidden');
        this.reportContent?.classList.remove('opacity-50');
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

    generateDetailedActivityReport(data) {
        console.log('Detailed Activity Data:', data);
        const activities = data.activities || [];
        
        // Group activities by date for better display
        const activitiesByDate = this.groupActivitiesByDate(activities);
        
        let html = `
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    ${this.createStatCard('Total Activities', activities.length, 'bg-blue-50 text-blue-900')}
                    ${this.createStatCard('Date Range', `${data.date_range?.start_formatted || '-'} to ${data.date_range?.end_formatted || '-'}`, 'bg-gray-100 text-gray-800')}
                </div>
                
                <!-- Filter options for detailed view -->
                <div class="flex gap-4 my-6">
                    <div class="relative w-full md:w-1/3">
                        <input type="text" id="detailSearch" 
                               placeholder="Search activity..."
                               class="px-4 py-2 border border-gray-300 rounded w-full" />
                    </div>
                </div>

                <!-- Activities Timeline -->
                <div id="activities-timeline" class="space-y-8">`;
        
        // Generate timeline by date
        Object.keys(activitiesByDate).sort().reverse().forEach(date => {
            const dateActivities = activitiesByDate[date];
            
            html += `
                <div class="date-group">
                    <h3 class="text-lg font-bold text-gray-900 my-4">
                        ${this.formatDateHeader(date)}
                    </h3>
                    
                    <div class="space-y-4">`;
            
            // Generate activity cards for this date
            dateActivities.forEach(activity => {
                const icon = this.getActivityIcon(activity.category);
                const categoryClass = 
                    activity.category === 'Meeting' ? 'bg-blue-100 text-blue-800' : 
                    activity.category === 'Invitation' ? 'bg-green-100 text-green-800' : 
                    'bg-orange-100 text-orange-800';
                
                html += `
                    <div class="activity-card bg-white rounded-lg shadow p-4 hover:shadow-md transition-shadow duration-200">
                        <div class="flex items-start">
                            <div class="activity-icon mr-4 flex-shrink-0 text-2xl">
                                ${icon}
                            </div>
                            
                            <div class="flex-grow">
                                <div class="flex flex-wrap items-center justify-between mb-2">
                                    <h4 class="text-lg font-semibold">${activity.name || 'Unnamed Activity'}</h4>
                                    <span class="px-2 py-1 text-xs rounded-full ${categoryClass}">${activity.category}</span>
                                </div>
                                
                                <div class="text-sm text-gray-600 mb-2">${activity.department || 'No Department'}</div>
                                
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500 mb-3">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>${this.formatDateTime(activity.start_datetime)} - ${this.formatDateTime(activity.end_datetime)}</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>${activity.city || '-'}, ${activity.province || '-'}</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span>Duration: ${activity.total_days}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <p class="text-sm text-gray-700">${activity.description || 'No description provided.'}</p>
                                </div>
                            </div>
                        </div>
                    </div>`;
            });
            
            html += `
                    </div>
                </div>`;
        });
        
        html += `
                </div>
            </div>
            
            <script>
                // Simple filtering for detailed view
                document.getElementById('detailSearch').addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const allCards = document.querySelectorAll('.activity-card');
                    
                    allCards.forEach(card => {
                        const text = card.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                    
                    // Show/hide date headers based on visible cards
                    document.querySelectorAll('.date-group').forEach(group => {
                        const visibleCards = Array.from(group.querySelectorAll('.activity-card'))
                            .filter(card => card.style.display !== 'none');
                            
                        if (visibleCards.length === 0) {
                            group.style.display = 'none';
                        } else {
                            group.style.display = 'block';
                        }
                    });
                });
            </script>
        `;
        
        return html;
    }
    
    // Helper methods for detailed activity report
    countUniqueValues(array, property) {
        return new Set(array.map(item => item[property])).size;
    }
    
    groupActivitiesByDate(activities) {
        const grouped = {};
        
        activities.forEach(activity => {
            const date = activity.start_date;
            if (!grouped[date]) {
                grouped[date] = [];
            }
            grouped[date].push(activity);
        });
        
        // Sort dates in descending order (newest first)
        return Object.fromEntries(
            Object.entries(grouped).sort((a, b) => b[0].localeCompare(a[0]))
        );
    }
    
    formatDateHeader(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString('id-ID', { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric'
        });
    }
    
    getActivityIcon(activityType) {
        switch (activityType) {
            case 'Meeting':
                return `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>`;
            case 'Invitation':
                return `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>`;
            case 'Survey':
                return `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>`;
            default:
                return `<svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>`;
        }
    }

    // Add a new export method for detailed reports
    async exportDetailedReport(format = 'xlsx') {
        const params = this.filterManager.getFilterParams();
        params.format = format;
        
        try {
            // Pastikan semua parameter yang dibutuhkan tersedia
            if (!params.year) {
                // Jika tidak ada tahun, default ke tahun sekarang
                params.year = new Date().getFullYear();
            }

            // Pastikan periode sesuai dan parameter lain tersedia
            if (params.time_period === 'monthly' && !params.month) {
                params.month = new Date().getMonth() + 1; // bulan dimulai dari 0
            } 
            else if (params.time_period === 'quarterly' && !params.quarter) {
                // Hitung quarter berdasarkan bulan sekarang
                const currentMonth = new Date().getMonth() + 1;
                params.quarter = Math.ceil(currentMonth / 3);
            }

            console.log('Detailed export params:', params);

            // Show loading indicator
            Swal.fire({
                title: 'Exporting...',
                html: 'Please wait while we prepare your export',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Use the same endpoint as regular export
            const response = await fetch(`${window.location.origin}/admin/activity/export`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(params)
            });

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || 'Export failed');
            }

            // Jika format PDF dan respons berisi modal dialog, tampilkan
            const contentType = response.headers.get('content-type');
            if (format === 'pdf' && contentType && contentType.includes('application/json')) {
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                Swal.fire({
                    icon: 'info',
                    title: 'PDF Format',
                    html: 'PDF format is not directly downloadable. Please use Excel or CSV format.',
                    showConfirmButton: true
                });
                return;
            }

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;

            // Generate nama file
            const timestamp = Date.now();
            a.download = `detailed_activity_report_${timestamp}.${format}`;
            
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            Swal.fire({
                icon: 'success',
                title: 'Export Successful',
                text: 'Your detailed activity report has been exported.',
                timer: 2000,
                showConfirmButton: false
            });
        } catch (error) {
            console.error('Error exporting detailed report:', error);
            Swal.fire({
                icon: 'error',
                title: 'Export Failed',
                text: error.message || 'Failed to export report. Please try again.',
                timer: 3000,
                showConfirmButton: false
            });
        }
    }

    formatDateTime(datetimeStr) {
        if (!datetimeStr) return '-';
        
        try {
            const dt = new Date(datetimeStr);
            if (isNaN(dt.getTime())) return '-';
            
            // Format: DD/MM/YYYY HH:MM
            return `${dt.getDate().toString().padStart(2, '0')}/${(dt.getMonth() + 1).toString().padStart(2, '0')}/${dt.getFullYear()} ${dt.getHours().toString().padStart(2, '0')}:${dt.getMinutes().toString().padStart(2, '0')}`;
        } catch (e) {
            console.error('Error formatting datetime:', e);
            return '-';
        }
    }
}