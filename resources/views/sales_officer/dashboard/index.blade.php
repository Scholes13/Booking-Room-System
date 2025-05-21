@extends('sales_officer.layout')

@section('title', 'Dashboard')
@section('header', 'Sales Officer Dashboard')
@section('description', 'Overview and statistics')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Current Month Activities -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium">Activities This Month</p>
                <h3 class="text-3xl font-bold mt-2">{{ $currentMonthActivities }}</h3>
            </div>
            <div class="bg-primary/10 p-3 rounded-full text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-gray-500 text-xs">All activities in {{ now()->format('F Y') }}</p>
        </div>
    </div>

    <!-- Total Sales Missions -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Sales Missions</p>
                <h3 class="text-3xl font-bold mt-2">{{ $totalSalesMissions }}</h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-gray-500 text-xs">Total sales missions across all time</p>
        </div>
    </div>

    <!-- Upcoming Activities Card -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium">Upcoming Activities</p>
                <h3 class="text-3xl font-bold mt-2">0</h3>
            </div>
            <div class="bg-green-100 p-3 rounded-full text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-gray-500 text-xs">Scheduled for next 30 days</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6">
    <!-- Activities Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <h4 class="text-base font-semibold text-gray-800 mb-4">Activities {{ now()->year }}</h4>
        <div id="monthlyChart" class="h-80 w-full"></div>
    </div>
</div>

<!-- Recent Activities -->
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mt-6">
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-base font-semibold text-gray-800">Recent Activities</h4>
        <a href="#" class="text-primary hover:text-primary-dark text-sm font-medium">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentActivities as $activity)
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->name }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $activity->activity_type }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $activity->city }}, {{ $activity->province }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-sm text-center text-gray-500">No recent activities found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Activities Chart
        var chartData = @json($chartData);
        
        var options = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Activities',
                data: chartData.map(item => item.count)
            }],
            xaxis: {
                categories: chartData.map(item => item.month),
                axisBorder: {
                    show: false
                }
            },
            colors: ['#10b981'], // Green color for Sales Officer
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: '#f3f4f6'
            }
        };
        
        var chart = new ApexCharts(document.querySelector("#monthlyChart"), options);
        chart.render();
    });
</script>
@endpush
@endsection 