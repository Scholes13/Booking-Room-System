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
            console.log('[ActivityReportGenerator] Fetch => /admin/activity/data');
 
            const response = await fetch(`${window.location.origin}/admin/activity/data`, {
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
}