<!-- Filter Card -->
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Report Type -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Report Type</label>
            <select id="report_type" class="w-full rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
                <option value="sales_missions">Sales Mission Reports</option>
                <option value="companies">Company Visit Reports</option>
                <option value="locations">Location Reports</option>
            </select>
        </div>

        <!-- Time Period -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Time Period</label>
            <select id="time_period" class="w-full rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
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
        <button id="viewReport" class="px-4 py-2 bg-sales text-white rounded-lg hover:bg-amber-600 transition-colors">
            <i class="fas fa-search mr-2"></i>View Report
        </button>
        <button id="exportReport" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors" data-export-url="{{ route('sales_mission.reports.export') }}">
            <i class="fas fa-file-export mr-2"></i>Export
        </button>
    </div>
</div> 