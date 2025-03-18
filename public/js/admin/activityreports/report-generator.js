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
            
            let endpoint = '/admin/activity/data';
            
            // Use different endpoint for detailed reports
            if (params.report_type === 'detailed_activity') {
                endpoint = '/admin/activity/detailed';
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
                                    <td class="px-6 py-4 text-sm text-gray-900">${act.start_time || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${act.end_time || '-'}</td>
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
                            ${data.departments.map(dept => `
                                <tr>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.name || '-'}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.total_activities || 0}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${Math.round(dept.hours_used) || '-'} hours</td>
                                    <td class="px-6 py-4 text-sm text-gray-900">${dept.total_days || '-'} days</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }
 
    generateLocationActivityReport(data) {
        const { location_stats, total_activities, total_locations } = data;
        
        // Generate summary cards
        let html = `<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">`;
        
        // Add summary statistics cards
        html += this.createStatCard('Total Activities', total_activities, 'bg-blue-500');
        html += this.createStatCard('Total Locations', total_locations, 'bg-green-500');
        
        html += `</div>`;

        // Generate location data table
        html += `
        <div class="bg-white p-4 rounded-lg shadow-sm mb-6">
            <h3 class="text-lg font-semibold mb-4">Activities by Location</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 [&>th]:py-2 [&>th]:px-4 text-left">
                            <th>Location</th>
                            <th>Total Activities</th>
                            <th>Hours Used</th>
                            <th>Meeting</th>
                            <th>Invitation</th>
                            <th>Survey</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        // Sort locations by total activities (descending)
        location_stats.sort((a, b) => b.total_activities - a.total_activities);

        // Generate table rows
        location_stats.forEach(loc => {
            html += `
                <tr class="border-t border-gray-200 [&>td]:py-2 [&>td]:px-4">
                    <td class="font-medium">${loc.location}</td>
                    <td>${loc.total_activities}</td>
                    <td>${loc.hours_used} hours</td>
                    <td>${loc.activities_by_type.Meeting || 0}</td>
                    <td>${loc.activities_by_type.Invitation || 0}</td>
                    <td>${loc.activities_by_type.Survey || 0}</td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
        </div>
        `;

        return html;
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
        const activities = data.activities || [];
        const dateRange = data.date_range || { start: 'Unknown', end: 'Unknown' };
        
        if (activities.length === 0) {
            return `
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                    <p>No activities found in the selected period</p>
                </div>
            `;
        }
        
        // Generate header with summary stats
        let html = `
        <div class="mb-8">
            <div class="mb-4">
                <h2 class="text-xl font-bold mb-1">Detailed Activity Report</h2>
                <p class="text-gray-500">Period: ${dateRange.start} - ${dateRange.end}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-blue-500 text-white rounded-lg p-6 shadow-md">
                    <div class="text-4xl font-bold">${activities.length}</div>
                    <div class="text-sm opacity-80">Total Activities</div>
                </div>
                
                <div class="bg-purple-500 text-white rounded-lg p-6 shadow-md">
                    <div class="text-4xl font-bold">
                        ${this.countUniqueValues(activities, 'location')}
                    </div>
                    <div class="text-sm opacity-80">Different Locations</div>
                </div>
            </div>
        </div>
        `;
        
        // Generate detailed activity list
        html += `
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <h3 class="bg-gray-800 text-white px-6 py-4 font-semibold">Activity Details</h3>
            <div class="p-2">
        `;
        
        // Group activities by date for better organization
        const groupedByDate = this.groupActivitiesByDate(activities);
        
        Object.entries(groupedByDate).forEach(([date, dateActivities]) => {
            html += `
            <div class="mb-6">
                <div class="bg-gray-100 px-4 py-2 rounded-t-lg font-medium text-gray-700 border-l-4 border-indigo-500">
                    ${this.formatDateHeader(date)}
                </div>
                <div class="space-y-4 mt-3">
            `;
            
            dateActivities.forEach(activity => {
                const activityColor = activity.activity_color || '#6B7280';
                
                html += `
                <div class="p-4 bg-white rounded-lg shadow border border-gray-200 hover:shadow-md transition mx-1">
                    <div class="flex items-start">
                        <!-- Left side with icon -->
                        <div class="mr-4">
                            <div class="w-12 h-12 flex items-center justify-center rounded-full" 
                                 style="background-color: ${activityColor}20;">
                                <i class="${this.getActivityIcon(activity.activity_type)}" 
                                   style="color: ${activityColor};"></i>
                            </div>
                        </div>
                        
                        <!-- Right side with details -->
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-lg">${activity.nama}</h4>
                                <span class="text-xs px-2 py-1 rounded-full" 
                                      style="background-color: ${activityColor}20; color: ${activityColor};">
                                    ${activity.activity_type}
                                </span>
                            </div>
                            
                            <div class="text-sm text-gray-700 mt-1">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-building mr-2 w-5 text-center text-gray-500"></i>
                                    <span>${activity.department}</span>
                                </div>
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-map-marker-alt mr-2 w-5 text-center text-gray-500"></i>
                                    <span>${activity.location}</span>
                                </div>
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-clock mr-2 w-5 text-center text-gray-500"></i>
                                    <span>${activity.time_range} (${activity.duration})</span>
                                </div>
                            </div>
                            
                            <div class="mt-3 text-gray-600 p-3 bg-gray-50 rounded-lg text-sm border-l-4" 
                                 style="border-color: ${activityColor};">
                                <p>${activity.description || 'No description provided'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                `;
            });
            
            html += `
                </div>
            </div>
            `;
        });
        
        html += `
            </div>
        </div>
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
        const icons = {
            'Meeting': 'fas fa-handshake fa-lg',
            'Invitation': 'fas fa-envelope-open-text fa-lg',
            'Survey': 'fas fa-clipboard-list fa-lg'
        };
        
        return icons[activityType] || 'fas fa-calendar-day fa-lg';
    }

    // Add a new export method for detailed reports
    async exportDetailedReport(format = 'pdf') {
        const params = this.filterManager.getFilterParams();
        
        try {
            if (params.report_type === 'detailed_activity') {
                // If exporting to Excel or CSV formats, use a data export approach
                if (format === 'xlsx' || format === 'csv') {
                    // Fetch the data again to ensure we have the raw data
                    const response = await fetch(`${window.location.origin}/admin/activity/detailed`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(params)
                    });
                    
                    if (!response.ok) {
                        throw new Error('Failed to fetch activity data for export');
                    }
                    
                    const data = await response.json();
                    
                    if (!data.activities || data.activities.length === 0) {
                        alert('No data available to export');
                        return;
                    }
                    
                    // Prepare data for CSV or XLSX format
                    const exportData = data.activities.map(activity => {
                        return {
                            'Name': activity.nama,
                            'Department': activity.department, 
                            'Location': activity.location,
                            'Activity Type': activity.activity_type,
                            'Time': activity.time_range,
                            'Duration': activity.duration,
                            'Description': activity.description || ''
                        };
                    });
                    
                    // For CSV export
                    if (format === 'csv') {
                        this.exportToCSV(exportData, `detailed_activity_report_${Date.now()}.csv`);
                    } 
                    // For XLSX export
                    else {
                        this.exportToExcel(exportData, `detailed_activity_report_${Date.now()}.xlsx`);
                    }
                    
                    return;
                }
                
                // For PDF format, use print functionality
                const printWindow = window.open('', '_blank');
                if (!printWindow) {
                    alert('Please allow popups to export the report');
                    return;
                }

                // Get the current report content
                const reportContent = document.getElementById('report_content').innerHTML;
                
                printWindow.document.write(`
                    <!DOCTYPE html>
                    <html lang="en">
                    <head>
                        <meta charset="UTF-8">
                        <meta name="viewport" content="width=device-width, initial-scale=1.0">
                        <title>Detailed Activity Report</title>
                        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
                        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
                        <style>
                            body {
                                font-family: 'Inter', sans-serif;
                                color: #333;
                                margin: 0;
                                padding: 20px;
                                line-height: 1.6;
                            }
                            .page-header {
                                border-bottom: 1px solid #ddd;
                                padding-bottom: 20px;
                                margin-bottom: 30px;
                            }
                            .company-logo {
                                text-align: center;
                                margin-bottom: 20px;
                            }
                            .company-info {
                                text-align: center;
                                margin-bottom: 30px;
                            }
                            @media print {
                                .no-print {
                                    display: none;
                                }
                                body {
                                    padding: 0;
                                }
                                button {
                                    display: none;
                                }
                            }
                        </style>
                    </head>
                    <body>
                        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
                            <button onclick="window.print()" style="background: #4F46E5; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                                <i class="fas fa-print"></i> Print
                            </button>
                        </div>
                        
                        <div class="page-header">
                            <div class="company-logo">
                                <img src="${window.location.origin}/img/logo.png" alt="Company Logo" height="60">
                            </div>
                            <div class="company-info">
                                <h2 style="margin-bottom: 5px; color: #4F46E5;">Detailed Activity Report</h2>
                                <p style="margin: 0;">Generated on ${new Date().toLocaleDateString('id-ID', { 
                                    weekday: 'long', 
                                    day: 'numeric', 
                                    month: 'long', 
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}</p>
                            </div>
                        </div>
                        
                        ${reportContent}
                        
                        <script>
                            // Auto print if PDF format selected
                            ${format === 'pdf' ? 'window.onload = () => setTimeout(() => window.print(), 1000);' : ''}
                        </script>
                    </body>
                    </html>
                `);
                
                printWindow.document.close();
            } else {
                // For other report types, use the existing export functionality
                console.log("Non-detailed reports should use the regular export feature");
            }
        } catch (error) {
            console.error('Error exporting detailed report:', error);
            alert('Failed to export report. Please try again.');
        }
    }
    
    // Helper method to export data to CSV format
    exportToCSV(data, filename) {
        if (!data || !data.length) {
            return;
        }
        
        // Get headers from the first data object
        const headers = Object.keys(data[0]);
        
        // Create CSV content
        let csvContent = headers.join(',') + '\n';
        
        // Add rows
        data.forEach(item => {
            const row = headers.map(header => {
                // Handle commas and quotes in the data
                const cell = String(item[header] || '');
                return `"${cell.replace(/"/g, '""')}"`;
            });
            csvContent += row.join(',') + '\n';
        });
        
        // Create blob and download
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // Helper method to export data to Excel format using SheetJS (xlsx library)
    // Note: This is a simplified version, ideally you'd include the xlsx library for better Excel exports
    exportToExcel(data, filename) {
        if (!window.XLSX) {
            // If SheetJS is not available, fall back to CSV
            console.warn('XLSX library not available, falling back to CSV export');
            return this.exportToCSV(data, filename.replace('.xlsx', '.csv'));
        }
        
        try {
            const worksheet = XLSX.utils.json_to_sheet(data);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Detailed Activities');
            XLSX.writeFile(workbook, filename);
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Failed to export as Excel. Attempting CSV export instead.');
            this.exportToCSV(data, filename.replace('.xlsx', '.csv'));
        }
    }
}