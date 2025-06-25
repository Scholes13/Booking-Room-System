@extends('sales_mission.layout')

@section('title', 'Appointments')

@section('header', 'Field Management - Appointments')

@section('description', 'Manage and schedule company visit appointments.')

@section('actions')
<div class="flex space-x-3">
    <a href="{{ route('sales_mission.field.appointments.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white font-semibold rounded-lg shadow hover:bg-amber-600 focus:ring-2 focus:ring-amber-300">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-2" viewBox="0 0 256 256">
            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm40,112H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32a8,8,0,0,1,0,16Z"></path>
        </svg>
        New Appointment
    </a>
</div>
@endsection

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Appointments List</h3>
            
            <!-- Filter & Search -->
            <div class="flex items-center gap-3">
                <form action="{{ route('sales_mission.field.appointments') }}" method="GET" class="flex items-center">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-gray-500" viewBox="0 0 256 256">
                                <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
                            </svg>
                        </div>
                        <input type="search" name="search" id="searchInput" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 p-2.5" placeholder="Search appointments..." value="{{ request('search') }}">
                    </div>
                    
                    <select name="status" id="statusFilter" class="ml-2 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5">
                        <option value="">All Statuses</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Canceled</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    
                    <div class="ml-2">
                        <input type="date" name="start_date" id="startDateFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5 flatpickr-date" placeholder="From date" value="{{ request('start_date') }}">
                    </div>
                    
                    <div class="ml-2">
                        <input type="date" name="end_date" id="endDateFilter" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5 flatpickr-date" placeholder="To date" value="{{ request('end_date') }}">
                    </div>
                    
                    <button type="submit" class="ml-2 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg border border-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300">Filter</button>
                </form>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-3">Company</th>
                        <th scope="col" class="px-4 py-3">Date & Time</th>
                        <th scope="col" class="px-4 py-3">Contact Person</th>
                        <th scope="col" class="px-4 py-3">Team Assigned</th>
                        <th scope="col" class="px-4 py-3">Purpose</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Empty state when no data available -->
                    <tr class="bg-white border-b">
                        <td colspan="7" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="text-gray-300 mb-2" viewBox="0 0 256 256">
                                    <path d="M208,32H184V24a8,8,0,0,0-16,0v8H88V24a8,8,0,0,0-16,0v8H48A16,16,0,0,0,32,48V208a16,16,0,0,0,16,16H208a16,16,0,0,0,16-16V48A16,16,0,0,0,208,32ZM72,48v8a8,8,0,0,0,16,0V48h80v8a8,8,0,0,0,16,0V48h24V80H48V48ZM208,208H48V96H208V208Zm-48-56a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h48A8,8,0,0,1,160,152Zm0-32a8,8,0,0,1-8,8H104a8,8,0,0,1,0-16h48A8,8,0,0,1,160,120Z"></path>
                                </svg>
                                <p class="text-gray-500 text-base">No appointments found</p>
                                <a href="{{ route('sales_mission.field.appointments.create') }}" class="mt-3 text-primary hover:text-amber-700 font-medium">
                                    Schedule your first appointment
                                </a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Data rows will be added here when available -->
                    {{-- Example row (commented out for now)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">PT Wijaya Karya</td>
                        <td class="px-4 py-3">May 28, 2023 - 10:00 AM</td>
                        <td class="px-4 py-3">Budi Santoso</td>
                        <td class="px-4 py-3">Team Alpha</td>
                        <td class="px-4 py-3">Product Presentation</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Confirmed
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('sales_mission.field.appointments.edit', 1) }}" class="text-primary hover:text-amber-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256">
                                        <path d="M224,120v88a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32h88a8,8,0,0,1,0,16H48V208H208V120a8,8,0,0,1,16,0Zm16-96a8,8,0,0,0-2.34-5.66,8.18,8.18,0,0,0-11.32,0l-96,96a8.1,8.1,0,0,0-1.89,3.06l-8,24a8,8,0,0,0,10.17,10.17l24-8a8.1,8.1,0,0,0,3.06-1.89l96-96A8,8,0,0,0,240,24Z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('sales_mission.field.appointments.delete', 1) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 delete-confirm">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256">
                                            <path d="M216,48H176V40a24,24,0,0,0-24-24H104A24,24,0,0,0,80,40v8H40a8,8,0,0,0,0,16h8V208a16,16,0,0,0,16,16H192a16,16,0,0,0,16-16V64h8a8,8,0,0,0,0-16ZM96,40a8,8,0,0,1,8-8h48a8,8,0,0,1,8,8v8H96Zm96,168H64V64H192ZM112,104v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Zm48,0v64a8,8,0,0,1-16,0V104a8,8,0,0,1,16,0Z"></path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    --}}
                </tbody>
            </table>
        </div>
        
        <!-- Pagination will be added here -->
        {{-- <div class="mt-4">
            {{ $appointments->links() }}
        </div> --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize flatpickr for date inputs
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            locale: "id",
            allowInput: true,
            altInput: true,
            altFormat: "d M Y",
            disableMobile: true
        });
    });
</script>
@endpush 