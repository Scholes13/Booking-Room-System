@extends('sales_mission.layout')

@section('title', 'Sales Mission Reports')
@section('header', 'Sales Mission Reports')
@section('description', 'Generate and export sales mission reports')

@section('content')
<div class="space-y-6">
    <!-- Include Filter Card -->
    @include('sales_mission.reports.partials.filter-card')

    <!-- Loading State -->
    <div id="loading" class="hidden">
        <div class="flex justify-center items-center p-8">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-sales"></div>
        </div>
    </div>

    <!-- Report Content -->
    <div id="report_content" class="bg-white rounded-lg shadow-sm p-6">
        <!-- Will be populated with report data -->
        <div class="text-center py-12 text-gray-500">
            <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
            </svg>
            <p>Select report type and time period to view data</p>
        </div>
    </div>
</div>

<!-- Include Export Modal -->
@include('sales_mission.reports.partials.export-modal')

@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Override API endpoints for Sales Mission role -->
    <script>
        window.reportApiEndpoints = {
            getData: '{{ route('sales_mission.reports.data') }}',
            export: '{{ route('sales_mission.reports.export') }}'
        };
    </script>
    
    <!-- Our application scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                // Set Chart.js defaults with sales mission colors
                Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
                Chart.defaults.color = '#555';
                Chart.defaults.plugins.tooltip.padding = 10;
                Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
                Chart.defaults.plugins.tooltip.titleColor = '#fff';
                Chart.defaults.plugins.tooltip.bodyColor = '#fff';
                Chart.defaults.plugins.tooltip.borderWidth = 0;
                Chart.defaults.plugins.tooltip.borderRadius = 4;
                
                // Get DOM elements
                const reportTypeSelect = document.getElementById('report_type');
                const timePeriodSelect = document.getElementById('time_period');
                const periodContainer = document.getElementById('period_container');
                const viewReportBtn = document.getElementById('viewReport');
                const exportReportBtn = document.getElementById('exportReport');
                const reportContent = document.getElementById('report_content');
                const loading = document.getElementById('loading');
                const exportModal = document.getElementById('exportModal');
                const closeExportModal = document.getElementById('closeExportModal');
                const cancelExport = document.getElementById('cancelExport');
                const confirmExport = document.getElementById('confirmExport');
                
                // Current date for defaults
                const currentDate = new Date();
                const currentYear = currentDate.getFullYear();
                const currentMonth = currentDate.getMonth() + 1;
                const currentQuarter = Math.ceil(currentMonth / 3);
                
                // Function to update period selection based on time period
                const updatePeriodSelection = () => {
                    const timePeriod = timePeriodSelect.value;
                    let html = '';
                    
                    if (timePeriod === 'monthly') {
                        html = `
                            <label class="block text-sm font-medium text-gray-700 mb-1">Month & Year</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select id="month" class="rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
                                    ${Array.from({length: 12}, (_, i) => `
                                        <option value="${i + 1}" ${i + 1 === currentMonth ? 'selected' : ''}>
                                            ${new Date(0, i).toLocaleString('default', { month: 'long' })}
                                        </option>
                                    `).join('')}
                                </select>
                                <select id="year" class="rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
                                    ${Array.from({length: 5}, (_, i) => `
                                        <option value="${currentYear - i}" ${i === 0 ? 'selected' : ''}>
                                            ${currentYear - i}
                                        </option>
                                    `).join('')}
                                </select>
                            </div>
                        `;
                    } else if (timePeriod === 'quarterly') {
                        html = `
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quarter & Year</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select id="quarter" class="rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
                                    <option value="1" ${currentQuarter === 1 ? 'selected' : ''}>Q1 (Jan-Mar)</option>
                                    <option value="2" ${currentQuarter === 2 ? 'selected' : ''}>Q2 (Apr-Jun)</option>
                                    <option value="3" ${currentQuarter === 3 ? 'selected' : ''}>Q3 (Jul-Sep)</option>
                                    <option value="4" ${currentQuarter === 4 ? 'selected' : ''}>Q4 (Oct-Dec)</option>
                                </select>
                                <select id="year" class="rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
                                    ${Array.from({length: 5}, (_, i) => `
                                        <option value="${currentYear - i}" ${i === 0 ? 'selected' : ''}>
                                            ${currentYear - i}
                                        </option>
                                    `).join('')}
                                </select>
                            </div>
                        `;
                    } else if (timePeriod === 'yearly') {
                        html = `
                            <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                            <select id="year" class="w-full rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
                                ${Array.from({length: 5}, (_, i) => `
                                    <option value="${currentYear - i}" ${i === 0 ? 'selected' : ''}>
                                        ${currentYear - i}
                                    </option>
                                `).join('')}
                            </select>
                        `;
                    }
                    
                    periodContainer.innerHTML = html;
                };
                
                // Initialize period selection
                updatePeriodSelection();
                
                // Event listener for time period changes
                timePeriodSelect.addEventListener('change', updatePeriodSelection);
                
                // Event listener for view report button
                viewReportBtn.addEventListener('click', async () => {
                    loading.classList.remove('hidden');
                    reportContent.innerHTML = '';
                    
                    // Get filter values
                    const reportType = reportTypeSelect.value;
                    const timePeriod = timePeriodSelect.value;
                    const year = document.getElementById('year').value;
                    
                    // Get month or quarter based on time period
                    let month, quarter;
                    if (timePeriod === 'monthly') {
                        month = document.getElementById('month').value;
                    } else if (timePeriod === 'quarterly') {
                        quarter = document.getElementById('quarter').value;
                    }
                    
                    try {
                        // Fetch report data
                        const response = await fetch(window.reportApiEndpoints.getData, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                report_type: reportType,
                                time_period: timePeriod,
                                year,
                                month,
                                quarter
                            })
                        });
                        
                        if (!response.ok) {
                            throw new Error('Failed to fetch report data');
                        }
                        
                        const data = await response.json();
                        
                        // Display report
                        displayReport(data, reportType, timePeriod);
                    } catch (error) {
                        console.error('Error fetching report data:', error);
                        reportContent.innerHTML = `
                            <div class="text-center py-12 text-red-500">
                                <svg class="mx-auto h-12 w-12 text-red-400 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                </svg>
                                <p>Failed to load report data. Please try again.</p>
                            </div>
                        `;
                    } finally {
                        loading.classList.add('hidden');
                    }
                });
                
                // Function to display report
                const displayReport = (data, reportType, timePeriod) => {
                    // Implementation will depend on the actual data structure
                    // This is a placeholder for demonstration
                    
                    reportContent.innerHTML = `
                        <div class="space-y-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-lg font-medium text-gray-900">${getReportTitle(reportType, timePeriod)}</h2>
                                <span class="text-sm text-gray-500">Total: ${data.total || 0} records</span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Charts will be inserted here -->
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-700 mb-2">Monthly Distribution</h3>
                                    <canvas id="chartMonthly" height="200"></canvas>
                                </div>
                                
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <h3 class="text-sm font-medium text-gray-700 mb-2">Location Distribution</h3>
                                    <canvas id="chartLocation" height="200"></canvas>
                                </div>
                            </div>
                            
                            <!-- Table for detailed data -->
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                        <tr>
                                            ${getTableHeaders(reportType)}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${getTableRows(data, reportType)}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                    
                    // Initialize charts if data is available
                    if (data.monthly && data.monthly.length > 0) {
                        initializeMonthlyChart(data.monthly);
                    }
                    
                    if (data.provinces && data.provinces.length > 0) {
                        initializeLocationChart(data.provinces);
                    }
                };
                
                // Helper functions for report display
                const getReportTitle = (reportType, timePeriod) => {
                    const typeTitle = {
                        'sales_missions': 'Sales Mission Report',
                        'companies': 'Company Visit Report',
                        'locations': 'Location Report'
                    }[reportType] || 'Report';
                    
                    const periodTitle = {
                        'monthly': 'Monthly',
                        'quarterly': 'Quarterly',
                        'yearly': 'Yearly'
                    }[timePeriod] || '';
                    
                    return `${typeTitle} - ${periodTitle}`;
                };
                
                const getTableHeaders = (reportType) => {
                    if (reportType === 'sales_missions') {
                        return `
                            <th scope="col" class="px-5 py-3.5">Company</th>
                            <th scope="col" class="px-5 py-3.5">PIC</th>
                            <th scope="col" class="px-5 py-3.5">Location</th>
                            <th scope="col" class="px-5 py-3.5">Date</th>
                        `;
                    } else if (reportType === 'companies') {
                        return `
                            <th scope="col" class="px-5 py-3.5">Company</th>
                            <th scope="col" class="px-5 py-3.5">PIC</th>
                            <th scope="col" class="px-5 py-3.5">Contact</th>
                            <th scope="col" class="px-5 py-3.5">Visits</th>
                        `;
                    } else if (reportType === 'locations') {
                        return `
                            <th scope="col" class="px-5 py-3.5">Province</th>
                            <th scope="col" class="px-5 py-3.5">City</th>
                            <th scope="col" class="px-5 py-3.5">Visits</th>
                            <th scope="col" class="px-5 py-3.5">Companies</th>
                        `;
                    }
                    
                    return '';
                };
                
                const getTableRows = (data, reportType) => {
                    if (!data.table || data.table.length === 0) {
                        return `
                            <tr>
                                <td colspan="4" class="px-5 py-4 text-center text-gray-500">
                                    No data available for the selected criteria
                                </td>
                            </tr>
                        `;
                    }
                    
                    // Display table rows based on data
                    return data.table.map(item => {
                        if (reportType === 'sales_missions') {
                            return `
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-5 py-4">${item.company_name}</td>
                                    <td class="px-5 py-4">${item.company_pic}</td>
                                    <td class="px-5 py-4">${item.location}</td>
                                    <td class="px-5 py-4">${item.date}</td>
                                </tr>
                            `;
                        } else if (reportType === 'companies') {
                            return `
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-5 py-4">${item.company_name}</td>
                                    <td class="px-5 py-4">${item.company_pic}</td>
                                    <td class="px-5 py-4">${item.company_contact}</td>
                                    <td class="px-5 py-4">${item.visits}</td>
                                </tr>
                            `;
                        } else if (reportType === 'locations') {
                            return `
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="px-5 py-4">${item.province}</td>
                                    <td class="px-5 py-4">${item.city}</td>
                                    <td class="px-5 py-4">${item.visits}</td>
                                    <td class="px-5 py-4">${item.companies}</td>
                                </tr>
                            `;
                        }
                        
                        return '';
                    }).join('');
                };
                
                // Chart initialization functions
                const initializeMonthlyChart = (data) => {
                    const ctx = document.getElementById('chartMonthly').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.map(item => item.month),
                            datasets: [{
                                label: 'Sales Missions',
                                data: data.map(item => item.count),
                                backgroundColor: 'rgba(245, 158, 11, 0.6)',
                                borderColor: 'rgba(245, 158, 11, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                };
                
                const initializeLocationChart = (data) => {
                    const ctx = document.getElementById('chartLocation').getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.map(item => item.province),
                            datasets: [{
                                data: data.map(item => item.count),
                                backgroundColor: [
                                    'rgba(245, 158, 11, 0.8)',
                                    'rgba(217, 119, 6, 0.8)',
                                    'rgba(180, 83, 9, 0.8)',
                                    'rgba(146, 64, 14, 0.8)',
                                    'rgba(120, 53, 15, 0.8)'
                                ],
                                borderColor: 'rgba(255, 255, 255, 0.5)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 15
                                    }
                                }
                            }
                        }
                    });
                };
                
                // Export functionality
                exportReportBtn.addEventListener('click', () => {
                    exportModal.classList.remove('hidden');
                });
                
                closeExportModal.addEventListener('click', () => {
                    exportModal.classList.add('hidden');
                });
                
                cancelExport.addEventListener('click', () => {
                    exportModal.classList.add('hidden');
                });
                
                confirmExport.addEventListener('click', async () => {
                    exportModal.classList.add('hidden');
                    
                    // Get filter values
                    const reportType = reportTypeSelect.value;
                    const timePeriod = timePeriodSelect.value;
                    const year = document.getElementById('year').value;
                    const format = document.getElementById('export_format').value;
                    const includeCharts = document.getElementById('include_charts').checked;
                    
                    // Get month or quarter based on time period
                    let month, quarter;
                    if (timePeriod === 'monthly') {
                        month = document.getElementById('month').value;
                    } else if (timePeriod === 'quarterly') {
                        quarter = document.getElementById('quarter').value;
                    }
                    
                    // Create form and submit for file download
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = window.reportApiEndpoints.export;
                    form.target = '_blank';
                    form.style.display = 'none';
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfToken);
                    
                    // Add form fields
                    const addField = (name, value) => {
                        if (value !== undefined && value !== null) {
                            const field = document.createElement('input');
                            field.type = 'hidden';
                            field.name = name;
                            field.value = value;
                            form.appendChild(field);
                        }
                    };
                    
                    addField('report_type', reportType);
                    addField('time_period', timePeriod);
                    addField('year', year);
                    addField('month', month);
                    addField('quarter', quarter);
                    addField('format', format);
                    addField('include_charts', includeCharts);
                    
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);
                });
                
            } catch (error) {
                console.error('Error initializing reports page:', error);
            }
        });
    </script>
    
    <style>
        select {
            background-color: white !important;
            color: #333 !important;
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 6px;
        }
    </style>
@endpush 