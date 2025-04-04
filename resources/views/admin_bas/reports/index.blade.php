@extends('admin_bas.layout')

@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Reports</h1>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 mb-6">
        <a href="{{ route('bas.reports') }}" class="py-3 px-6 font-medium text-sm rounded-t-lg bg-primary text-white">
            Booking Reports
        </a>
        <a href="{{ route('bas.activity.index') }}" class="py-3 px-6 font-medium text-sm text-gray-600 hover:text-primary">
            Activity Reports
        </a>
    </div>

    <!-- Include Filter Card -->
    @include('admin_bas.reports.partials.filter-card')

    <!-- Loading State -->
    <div id="loading" class="hidden">
        <div class="flex justify-center items-center p-8">
            <i class="fas fa-circle-notch fa-spin text-3xl text-primary"></i>
        </div>
    </div>

    <!-- Report Content -->
    <div id="report_content" class="bg-white rounded-lg shadow-sm p-6">
        <!-- Will be populated with report data -->
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-chart-line text-4xl mb-3"></i>
            <p>Select report type and time period to view data</p>
        </div>
    </div>
</div>

<!-- Include Export Modal -->
@include('admin_bas.reports.partials.export-modal')

@endsection

@push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Our application scripts -->
    <script src="{{ asset('js/admin/reports/filter-manager.js') }}"></script>
    <script src="{{ asset('js/admin/reports/report-generator.js') }}"></script>
    <script src="{{ asset('js/admin/reports/export-manager.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                // Inisialisasi Chart.js defaults
                Chart.defaults.font.family = "'Poppins', sans-serif";
                Chart.defaults.plugins.tooltip.padding = 10;
                Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
                Chart.defaults.plugins.tooltip.titleColor = '#fff';
                Chart.defaults.plugins.tooltip.bodyColor = '#fff';
                Chart.defaults.plugins.tooltip.borderWidth = 0;
                Chart.defaults.plugins.tooltip.borderRadius = 4;
        
                // Pastikan filterManager sudah tersedia
                const filterManager = window.filterManager; 
                // Inisialisasi ReportGenerator (untuk load & display report)
                const reportGenerator = new ReportGenerator(filterManager);

                // Inisialisasi ExportManager (untuk handle export modal)
                const exportManager = new ExportManager(filterManager);

                // Simpan instance untuk debugging
                window.reportManagers = {
                    filter: filterManager,
                    report: reportGenerator,
                    export: exportManager
                };
            } catch (error) {
                console.error('Error initializing managers:', error);
            }
        });
    </script>
@endpush

<style>
    select {
        background-color: white !important;
        color: #333 !important;
        border: 1px solid #ccc;
        padding: 8px;
        border-radius: 6px;
    }
</style> 