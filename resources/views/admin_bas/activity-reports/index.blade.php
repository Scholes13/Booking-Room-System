@extends('admin_bas.layout')

@section('title', 'Activity Reports')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">Activity Reports</h1>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 mb-6">
        <a href="{{ route('bas.reports') }}" class="py-3 px-6 font-medium text-sm text-gray-600 hover:text-primary">
            Booking Reports
        </a>
        <a href="{{ route('bas.activity.index') }}" class="py-3 px-6 font-medium text-sm rounded-t-lg bg-primary text-white">
            Activity Reports
        </a>
    </div>

    <!-- Include Filter Card (Hanya 2 opsi: employee_activity, department_activity) -->
    @include('admin.activity-reports.partials.filter-card')

    <!-- Loading State -->
    <div id="loading" class="hidden">
        <div class="flex justify-center items-center p-8">
            <i class="fas fa-circle-notch fa-spin text-3xl text-primary"></i>
        </div>
    </div>

    <!-- Report Content -->
    <div id="report_content" class="bg-white rounded-lg shadow-sm p-6">
        <!-- Akan diisi data report -->
        <div class="text-center py-12 text-gray-500">
            <i class="fas fa-chart-line text-4xl mb-3"></i>
            <p>Select report type and time period to view data</p>
        </div>
    </div>
</div>

<!-- (Opsional) Export Modal, jika ingin ekspor data -->
@include('admin.activity-reports.partials.export-modal')

@endsection

@push('scripts')
    <!-- SheetJS for Excel exports -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    
    <!-- Chart.js (untuk chart, jika diperlukan) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Script JS untuk Activity Reports -->
    <script src="{{ asset('js/admin/activityreports/filter-manager.js') }}"></script>
    <script src="{{ asset('js/admin/activityreports/report-generator.js') }}"></script>
    <script src="{{ asset('js/admin/activityreports/export-manager.js') }}"></script>
    <script src="{{ asset('js/admin/activityreports/init.js') }}"></script>
    <!-- (Opsional) Jika ada file chart khusus activity, mis. activity-charts.js -->
    {{-- <script src="{{ asset('js/admin/activityreports/activity-charts.js') }}"></script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                // Atur default Chart.js (opsional)
                Chart.defaults.font.family = "'Poppins', sans-serif";
                Chart.defaults.plugins.tooltip.padding = 10;
                Chart.defaults.plugins.tooltip.backgroundColor = 'rgba(0, 0, 0, 0.8)';
                Chart.defaults.plugins.tooltip.titleColor = '#fff';
                Chart.defaults.plugins.tooltip.bodyColor = '#fff';
                Chart.defaults.plugins.tooltip.borderWidth = 0;
                Chart.defaults.plugins.tooltip.borderRadius = 4;

                // Inisialisasi sudah dilakukan di init.js
                // Tidak perlu menginisialisasi ulang di sini
            } catch (error) {
                console.error('Error initializing chart defaults:', error);
            }
        });
    </script>
@endpush

@push('styles')
<style>
    /* Base Styles */
    select {
        background-color: white !important;
        color: #333 !important;
        border: 1px solid #ccc;
        padding: 8px;
        border-radius: 6px;
    }
    
    /* Scrollbar Styling */
    .scrollbar-thin {
        scrollbar-width: thin;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }
    
    /* Table Improvements */
    .overflow-x-auto {
        position: relative;
        max-height: 600px;
        border-radius: 0.5rem;
    }
    
    table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    thead {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    th {
        box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
    }
    
    tbody tr:hover {
        background-color: rgba(249, 250, 251, 0.5);
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        table {
            display: block;
            width: 100%;
        }
        
        thead, tbody, td, th {
            display: block;
        }
        
        thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }
        
        tr {
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
        
        td {
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50% !important;
            white-space: normal !important;
            text-align: left !important;
        }
        
        td:before {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            content: attr(data-label);
        }
    }
</style>
@endpush 