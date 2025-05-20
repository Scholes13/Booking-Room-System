@extends('sales_officer.layout')

@section('title', 'Reports')
@section('header', 'Reports')
@section('description', 'Generate and export activity reports')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <div class="mb-6">
        <h4 class="text-base font-semibold text-gray-800 mb-4">Filter Options</h4>
        <form id="reportForm" action="{{ route('sales_officer.reports.data') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="text" id="start_date" name="start_date" class="datepicker w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="text" id="end_date" name="end_date" class="datepicker w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="activity_type" class="block text-sm font-medium text-gray-700 mb-1">Activity Type</label>
                <select id="activity_type" name="activity_type" class="w-full rounded-md border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="">All Types</option>
                    <option value="Sales Mission">Sales Mission</option>
                    <option value="Meeting">Meeting</option>
                    <option value="Workshop">Workshop</option>
                    <option value="Training">Training</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Generate Report
                </button>
            </div>
        </form>
    </div>
    
    <div id="reportResults" class="mt-8 hidden">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-semibold text-gray-800">Report Results</h4>
            <form action="{{ route('sales_officer.reports.export') }}" method="POST">
                @csrf
                <input type="hidden" id="export_start_date" name="start_date">
                <input type="hidden" id="export_end_date" name="end_date">
                <input type="hidden" id="export_activity_type" name="activity_type">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export to Excel
                </button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody id="reportTable" class="bg-white divide-y divide-gray-200">
                    <!-- Report data will be loaded here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const reportForm = document.getElementById('reportForm');
        const reportResults = document.getElementById('reportResults');
        const reportTable = document.getElementById('reportTable');
        
        reportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(reportForm);
            
            // Update export form values
            document.getElementById('export_start_date').value = document.getElementById('start_date').value;
            document.getElementById('export_end_date').value = document.getElementById('end_date').value;
            document.getElementById('export_activity_type').value = document.getElementById('activity_type').value;
            
            // Show loading state
            reportTable.innerHTML = '<tr><td colspan="5" class="text-center py-4">Loading...</td></tr>';
            reportResults.classList.remove('hidden');
            
            fetch(reportForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                let html = '';
                
                if (data.length === 0) {
                    html = '<tr><td colspan="5" class="text-center py-4">No data found for the selected criteria</td></tr>';
                } else {
                    data.forEach(activity => {
                        html += `
                            <tr>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${activity.name}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${activity.activity_type}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${activity.department ? activity.department.name : 'N/A'}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${activity.city}, ${activity.province}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${new Date(activity.start_datetime).toLocaleDateString()}</div>
                                </td>
                            </tr>
                        `;
                    });
                }
                
                reportTable.innerHTML = html;
            })
            .catch(error => {
                console.error('Error:', error);
                reportTable.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Error loading data</td></tr>';
            });
        });
    });
</script>
@endpush
@endsection 