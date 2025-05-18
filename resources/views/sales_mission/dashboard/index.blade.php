@extends('sales_mission.layout')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('description', 'Ringkasan dan statistik Sales Mission')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Total Sales Mission -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium">Total Sales Mission</p>
                <h3 class="text-3xl font-bold mt-2">{{ $totalSalesMissions }}</h3>
            </div>
            <div class="bg-sales/10 p-3 rounded-full text-sales">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-gray-500 text-xs">Sales mission yang tercatat dalam sistem</p>
        </div>
    </div>

    <!-- Sales Mission Month -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium">Sales Mission Bulan Ini</p>
                <h3 class="text-3xl font-bold mt-2">{{ $thisMonthSalesMissions }}</h3>
            </div>
            <div class="bg-green-100 p-3 rounded-full text-green-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-gray-500 text-xs">Sales mission pada bulan {{ now()->format('F Y') }}</p>
        </div>
    </div>

    <!-- Total Companies -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium">Perusahaan Yang Dikunjungi</p>
                <h3 class="text-3xl font-bold mt-2">{{ $totalCompanies }}</h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-gray-500 text-xs">Jumlah perusahaan yang sudah dikunjungi</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Sales Mission Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
        <h4 class="text-base font-semibold text-gray-800 mb-4">Sales Mission {{ now()->year }}</h4>
        <div id="monthlySalesChart" class="h-80 w-full"></div>
    </div>

    <!-- Top Locations -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-base font-semibold text-gray-800">Top Provinsi</h4>
        </div>
        <div class="space-y-4">
            @foreach($locationData as $location)
                <div class="flex items-center">
                    <div class="w-full">
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $location->province }}</span>
                            <span class="text-sm font-medium text-gray-700">{{ $location->count }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-sales h-2 rounded-full" style="width: {{ ($location->count / $locationData->max('count')) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Sales Missions -->
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mt-6">
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-base font-semibold text-gray-800">Sales Mission Terbaru</h4>
        <a href="{{ route('sales_mission.activities.index') }}" class="text-sales hover:text-amber-800 text-sm font-medium">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perusahaan</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentSalesMissions as $mission)
                    <tr>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $mission->salesMissionDetail->company_name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $mission->salesMissionDetail->company_pic }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $mission->city }}, {{ $mission->province }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($mission->start_datetime)->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                            <a href="{{ route('sales_mission.activities.edit', $mission->id) }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-4 text-sm text-center text-gray-500">Belum ada data sales mission</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Sales Mission Chart
        const chartData = @json($chartData);
        
        const options = {
            chart: {
                type: 'bar',
                height: 310,
                toolbar: {
                    show: false
                }
            },
            series: [{
                name: 'Sales Mission',
                data: chartData.map(item => item.count)
            }],
            xaxis: {
                categories: chartData.map(item => item.month),
            },
            colors: ['#f59e0b'],
            plotOptions: {
                bar: {
                    borderRadius: 3,
                    horizontal: false,
                    columnWidth: '55%',
                }
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: '#f1f1f1',
            },
            stroke: {
                width: 0
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " kunjungan"
                    }
                }
            }
        };

        const chart = new ApexCharts(document.querySelector("#monthlySalesChart"), options);
        chart.render();
    });
</script>
@endpush 