<!-- Filter Card -->
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Report Type -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
            <select id="report_type" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                <option value="employee_activity">Employee Activity Reports</option>
                <option value="department_activity">Department Activity Reports</option>
            </select>
        </div>

        <!-- Time Period -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Time Period</label>
            <select id="time_period" class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary/20">
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="yearly">Yearly</option>
            </select>
        </div>

        <!-- Dynamic Period Selection -->
        <div id="period_container">
            <!-- Will be populated dynamically based on time period selection -->
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4 flex justify-end space-x-3">
        <button id="viewReport" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-hover transition-colors">
            <i class="fas fa-search mr-2"></i>View Report
        </button>
        <button id="exportReport" class="px-4 py-2 bg-secondary text-white rounded-lg hover:bg-secondary/80 transition-colors">
            <i class="fas fa-file-export mr-2"></i>Export
        </button>
    </div>
</div>
