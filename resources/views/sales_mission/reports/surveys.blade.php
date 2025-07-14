@extends('sales_mission.layout')

@section('title', 'Survey Reports')
@section('header', 'Survey Reports')
@section('description', 'View and analyze survey responses from both Sales Blitz and Field Visit surveys')

@section('content')
<div class="space-y-6">
    <!-- Filter Card -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Filters</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Survey Type -->
            <div>
                <label for="survey_type" class="block text-sm font-medium text-gray-700 mb-1">Survey Type</label>
                <select id="survey_type" class="w-full rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
                    <option value="">All Types</option>
                    <option value="sales_blitz">Sales Blitz</option>
                    <option value="field_visit">Field Visit</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" class="w-full rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" class="w-full rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
            </div>

            <!-- Team -->
            <div>
                <label for="team_id" class="block text-sm font-medium text-gray-700 mb-1">Team</label>
                <select id="team_id" class="w-full rounded-lg border-gray-300 focus:border-sales focus:ring focus:ring-sales/20">
                    <option value="">All Teams</option>
                    @foreach(\App\Models\Team::orderBy('name')->get() as $team)
                        <option value="{{ $team->id }}">{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-4 flex justify-end space-x-3">
            <button type="button" id="viewReport" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sales hover:bg-sales-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sales transition-colors duration-200">
                <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                </svg>
                View Report
            </button>
            <button type="button" id="exportReport" class="inline-flex items-center px-4 py-2 border border-sales text-sm font-medium rounded-md text-sales bg-white hover:bg-sales-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sales transition-colors duration-200">
                <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Export to Excel
            </button>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading" class="hidden">
        <div class="flex justify-center items-center p-8">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-sales"></div>
        </div>
    </div>

    <!-- Report Content -->
    <div id="report_content" class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Surveys Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 mb-1">Total Surveys</h3>
                        <p class="text-3xl font-bold text-gray-900 mb-2 transition-all duration-300" id="total_surveys">-</p>
                    </div>
                    <div class="bg-sales/10 p-3 rounded-full">
                        <svg class="w-8 h-8 text-sales" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-3 border border-gray-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-sales"></div>
                            <span class="text-sm text-gray-600">Blitz</span>
                        </div>
                        <p class="text-xl font-semibold text-gray-900 mt-1" id="blitz_surveys">-</p>
                    </div>
                    <div class="bg-white rounded-lg p-3 border border-gray-100">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                            <span class="text-sm text-gray-600">Field</span>
                        </div>
                        <p class="text-xl font-semibold text-gray-900 mt-1" id="token_surveys">-</p>
                    </div>
                </div>
            </div>

            <!-- Lead Status Distribution Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Lead Status Distribution</h3>
                    <div class="bg-indigo-50 p-2 rounded-full">
                        <svg class="w-5 h-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                </div>
                <div class="h-[180px]">
                    <canvas id="leadStatusChart"></canvas>
                </div>
            </div>

            <!-- Decision Maker Distribution Card -->
            <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm p-6 border border-gray-100 hover:shadow-md transition-shadow duration-300">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Decision Maker Distribution</h3>
                    <div class="bg-purple-50 p-2 rounded-full">
                        <svg class="w-5 h-5 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                </div>
                <div class="h-[180px]">
                    <canvas id="decisionMakerChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Survey Data Table -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-900">Survey Details</h2>
                    <span class="text-sm text-gray-500" id="survey-count"></span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Decision Maker</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Follow Up</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="survey_table_body">
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                Select filters and click "View Report" to see data
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let leadStatusChart = null;
    let decisionMakerChart = null;

    // Initialize charts
    function initializeCharts() {
        if (leadStatusChart) leadStatusChart.destroy();
        if (decisionMakerChart) decisionMakerChart.destroy();

        const ctx1 = document.getElementById('leadStatusChart').getContext('2d');
        const ctx2 = document.getElementById('decisionMakerChart').getContext('2d');

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'right',
                    labels: { 
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            size: 11
                        }
                    }
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true
            },
            cutout: '60%'
        };

        leadStatusChart = new Chart(ctx1, {
            type: 'doughnut',
            data: { labels: [], datasets: [{ data: [], backgroundColor: [] }] },
            options: chartOptions
        });

        decisionMakerChart = new Chart(ctx2, {
            type: 'doughnut',
            data: { labels: [], datasets: [{ data: [], backgroundColor: [] }] },
            options: chartOptions
        });
    }

    // Initialize charts on load
    initializeCharts();

    // Function to update charts
    function updateCharts(statistics) {
        // Update Lead Status Chart
        const leadStatusData = statistics.status_lead;
        leadStatusChart.data.labels = Object.keys(leadStatusData);
        leadStatusChart.data.datasets[0].data = Object.values(leadStatusData);
        leadStatusChart.data.datasets[0].backgroundColor = generateColors(Object.keys(leadStatusData).length);
        leadStatusChart.update();

        // Update Decision Maker Chart
        const decisionMakerData = statistics.decision_maker_status;
        decisionMakerChart.data.labels = Object.keys(decisionMakerData);
        decisionMakerChart.data.datasets[0].data = Object.values(decisionMakerData);
        decisionMakerChart.data.datasets[0].backgroundColor = generateColors(Object.keys(decisionMakerData).length);
        decisionMakerChart.update();
    }

    // Function to generate colors
    function generateColors(count) {
        const colors = [
            '#4F46E5', '#7C3AED', '#EC4899', '#EF4444', '#F59E0B',
            '#10B981', '#3B82F6', '#6366F1', '#8B5CF6', '#D946EF'
        ];
        return Array(count).fill().map((_, i) => colors[i % colors.length]);
    }

    // Function to update table
    function updateTable(surveys) {
        const tbody = document.getElementById('survey_table_body');
        tbody.innerHTML = '';

        // Update survey count
        document.getElementById('survey-count').textContent = `${surveys.length} surveys found`;

        surveys.forEach(survey => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition-colors duration-150';
            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="font-medium">${survey.team_name || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    ${survey.visit_date ? new Date(survey.visit_date).toLocaleDateString('en-GB', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }) : '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="flex items-center">
                        <span class="h-2 w-2 ${survey.type === 'sales_blitz' ? 'bg-sales' : 'bg-indigo-500'} rounded-full mr-2"></span>
                        ${survey.type === 'sales_blitz' ? 'Sales Blitz' : 'Field Visit'}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="font-medium text-gray-900">${survey.company_name || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div class="font-medium">${survey.contact_name || '-'}</div>
                    <div class="text-xs text-gray-500">${survey.contact_job_title || ''}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${getStatusClass(survey.status_lead)}">
                        ${survey.status_lead || '-'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="font-medium text-gray-900">${survey.decision_maker_status || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="font-medium text-gray-900">${survey.next_follow_up || '-'}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                    <a href="/feedback/view/${survey.survey_token}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-green-300 text-xs font-medium rounded-md shadow-sm text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.022 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                        View Submission
                    </a>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Function to get status class
    function getStatusClass(status) {
        const classes = {
            'Hot': 'bg-red-100 text-red-800',
            'Warm': 'bg-yellow-100 text-yellow-800',
            'Cold': 'bg-blue-100 text-blue-800',
            'Not Interested': 'bg-gray-100 text-gray-800',
            'Follow-up Required': 'bg-purple-100 text-purple-800',
            'Closed-Won': 'bg-green-100 text-green-800',
            'Closed-Lost': 'bg-red-100 text-red-800'
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    }

    // Function to update statistics with animation
    function updateStatistics(statistics) {
        // Animate the numbers
        animateNumber('total_surveys', statistics.total_surveys);
        animateNumber('blitz_surveys', statistics.blitz_surveys);
        animateNumber('token_surveys', statistics.token_surveys);
    }

    // Function to animate number counting
    function animateNumber(elementId, finalNumber) {
        const element = document.getElementById(elementId);
        const duration = 1000; // Animation duration in milliseconds
        const startNumber = parseInt(element.textContent) || 0;
        const step = Math.ceil((finalNumber - startNumber) / (duration / 16)); // 60fps

        let currentNumber = startNumber;
        const animate = () => {
            currentNumber = Math.min(currentNumber + step, finalNumber);
            element.textContent = currentNumber;

            if (currentNumber < finalNumber) {
                requestAnimationFrame(animate);
            }
        };

        animate();
    }

    // Function to load report data
    async function loadReportData() {
        const loading = document.getElementById('loading');
        loading.classList.remove('hidden');

        try {
            const response = await fetch('{{ route("sales_mission.reports.surveys.data") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    survey_type: document.getElementById('survey_type').value,
                    start_date: document.getElementById('start_date').value,
                    end_date: document.getElementById('end_date').value,
                    team_id: document.getElementById('team_id').value
                })
            });

            if (!response.ok) throw new Error('Failed to fetch report data');

            const data = await response.json();
            updateStatistics(data.statistics);
            updateCharts(data.statistics);
            updateTable(data.surveys);

        } catch (error) {
            console.error('Error loading report data:', error);
            // Show error message using SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Failed to load report data. Please try again.',
                confirmButtonColor: '#f59e0b'
            });
        } finally {
            loading.classList.add('hidden');
        }
    }

    // Event Listeners
    document.getElementById('viewReport').addEventListener('click', loadReportData);

    document.getElementById('exportReport').addEventListener('click', async () => {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("sales_mission.reports.surveys.export") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        // Add filter values
        const filters = {
            survey_type: document.getElementById('survey_type').value,
            start_date: document.getElementById('start_date').value,
            end_date: document.getElementById('end_date').value,
            team_id: document.getElementById('team_id').value
        };

        Object.entries(filters).forEach(([key, value]) => {
            if (value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            }
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    });
});
</script>
@endpush 