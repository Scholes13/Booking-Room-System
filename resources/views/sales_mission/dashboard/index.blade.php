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
            <p class="text-gray-500 text-xs">Total keseluruhan sales mission yang sudah berjalan</p>
        </div>
    </div>

    <!-- Sales Mission Month yang sudah berjalan -->
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
            <p class="text-gray-500 text-xs">Sales mission bulan {{ now()->format('F Y') }} yang sudah berjalan</p>
        </div>
    </div>

    <!-- Janji temu bulan ini -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
        <div class="flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium">Janji Temu Bulan Ini</p>
                <h3 class="text-3xl font-bold mt-2">{{ $appointmentsThisMonth }}</h3>
            </div>
            <div class="bg-blue-100 p-3 rounded-full text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
        <div class="mt-4">
            <p class="text-gray-500 text-xs">Seluruh janji temu pada bulan {{ now()->format('F Y') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Sales Mission Chart -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 md:col-span-2">
        <h4 class="text-base font-semibold text-gray-800 mb-4">Sales Mission {{ now()->year }}</h4>
        <div id="monthlySalesChart" class="h-80 w-full"></div>
    </div>

    <!-- Top Locations Grid -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Top Provinces -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-base font-semibold text-gray-800">Top 5 Provinsi</h4>
            </div>
            <div class="space-y-4">
                @foreach($provinceData as $province)
                    <div class="flex items-center">
                        <div class="w-full">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $province->province }}</span>
                                <span class="text-sm font-medium text-gray-700">{{ $province->count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-sales h-2 rounded-full" style="width: {{ ($province->count / $provinceData->max('count')) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Cities -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-base font-semibold text-gray-800">Top 5 Kota</h4>
            </div>
            <div class="space-y-4">
                @foreach($cityData as $city)
                    <div class="flex items-center">
                        <div class="w-full">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-gray-700">{{ $city->city }}</span>
                                <span class="text-sm font-medium text-gray-700">{{ $city->count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($city->count / $cityData->max('count')) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
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
                            <button type="button" class="text-blue-600 hover:text-blue-800 show-details-btn" data-id="{{ $mission->id }}" 
                            data-company="{{ $mission->salesMissionDetail->company_name }}"
                            data-pic="{{ $mission->salesMissionDetail->company_pic }}"
                            data-contact="{{ $mission->salesMissionDetail->company_contact }}"
                            data-address="{{ $mission->salesMissionDetail->company_address }}"
                            data-location="{{ $mission->city }}, {{ $mission->province }}"
                            data-start="{{ \Carbon\Carbon::parse($mission->start_datetime)->format('d M Y H:i') }}"
                            data-end="{{ \Carbon\Carbon::parse($mission->end_datetime)->format('d M Y H:i') }}"
                            data-description="{{ $mission->description }}"
                            data-employee="{{ $mission->name }}"
                            data-department="{{ $mission->department->name ?? 'N/A' }}">
                                <i class="fas fa-eye"></i> Detail
                            </button>
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

<!-- Modal for detailed view -->
<div id="detailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-primary p-4 text-white">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold" id="modalTitle">Detail Sales Mission</h3>
                <button id="closeModal" class="text-white hover:text-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="space-y-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-500">Perusahaan</h4>
                    <p class="text-gray-800" id="companyName"></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">PIC</h4>
                        <p class="text-gray-800" id="companyPic"></p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Kontak</h4>
                        <p class="text-gray-800" id="companyContact"></p>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-semibold text-gray-500">Alamat</h4>
                    <p class="text-gray-800" id="companyAddress"></p>
                </div>
                
                <div>
                    <h4 class="text-sm font-semibold text-gray-500">Lokasi</h4>
                    <p class="text-gray-800" id="missionLocation"></p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Mulai</h4>
                        <p class="text-gray-800" id="startTime"></p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500">Selesai</h4>
                        <p class="text-gray-800" id="endTime"></p>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-sm font-semibold text-gray-500">Karyawan</h4>
                    <p class="text-gray-800" id="employeeName"></p>
                </div>
                
                <div>
                    <h4 class="text-sm font-semibold text-gray-500">Departemen</h4>
                    <p class="text-gray-800" id="departmentName"></p>
                </div>
                
                <div>
                    <h4 class="text-sm font-semibold text-gray-500">Deskripsi</h4>
                    <p class="text-gray-800" id="missionDescription"></p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-md text-sm transition-colors" id="closeModalBtn">
                    Tutup
                </button>
            </div>
        </div>
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
        
        // Modal functionality
        const detailsModal = document.getElementById('detailsModal');
        const closeModal = document.getElementById('closeModal');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const showDetailsBtns = document.querySelectorAll('.show-details-btn');
        
        // Show modal with details
        showDetailsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const company = this.getAttribute('data-company');
                const pic = this.getAttribute('data-pic');
                const contact = this.getAttribute('data-contact');
                const address = this.getAttribute('data-address');
                const location = this.getAttribute('data-location');
                const start = this.getAttribute('data-start');
                const end = this.getAttribute('data-end');
                const description = this.getAttribute('data-description');
                const employee = this.getAttribute('data-employee');
                const department = this.getAttribute('data-department');
                
                // Fill modal with data
                document.getElementById('companyName').textContent = company;
                document.getElementById('companyPic').textContent = pic;
                document.getElementById('companyContact').textContent = contact;
                document.getElementById('companyAddress').textContent = address;
                document.getElementById('missionLocation').textContent = location;
                document.getElementById('startTime').textContent = start;
                document.getElementById('endTime').textContent = end;
                document.getElementById('employeeName').textContent = employee;
                document.getElementById('departmentName').textContent = department;
                document.getElementById('missionDescription').textContent = description;
                
                // Show modal
                detailsModal.classList.remove('hidden');
                detailsModal.classList.add('flex');
            });
        });
        
        // Close modal functions
        const hideModal = () => {
            detailsModal.classList.add('hidden');
            detailsModal.classList.remove('flex');
        };
        
        closeModal.addEventListener('click', hideModal);
        closeModalBtn.addEventListener('click', hideModal);
        
        // Close modal when clicking outside
        detailsModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal();
            }
        });
        
        // Close modal on ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !detailsModal.classList.contains('hidden')) {
                hideModal();
            }
        });
    });
</script>
@endpush 