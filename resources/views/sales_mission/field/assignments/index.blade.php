@extends('sales_mission.layout')

@section('title', 'Team Assignments')

@section('header', 'Field Management - Team Assignments')

@section('description', 'Manage field team assignments for company visits.')

@section('actions')
<div class="flex space-x-3">
    <a href="{{ route('sales_mission.field.assignments.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white font-semibold rounded-lg shadow hover:bg-amber-600 focus:ring-2 focus:ring-amber-300">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mr-2" viewBox="0 0 256 256">
            <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm40,112H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32a8,8,0,0,1,0,16Z"></path>
        </svg>
        Create New Assignment
    </a>
</div>
@endsection

@section('content')
<div class="bg-white shadow-md rounded-lg overflow-hidden">
    <div class="p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Team Assignments List</h3>
            
            <!-- Filter & Search -->
            <div class="flex items-center gap-3">
                <form action="{{ route('sales_mission.field.assignments') }}" method="GET" class="flex items-center">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="text-gray-500" viewBox="0 0 256 256">
                                <path d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"></path>
                            </svg>
                        </div>
                        <input type="search" name="search" id="searchInput" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full pl-10 p-2.5" placeholder="Search assignments..." value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="ml-2 px-4 py-2.5 text-sm font-medium text-white bg-primary rounded-lg border border-amber-600 hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300">Search</button>
                </form>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                    <tr>
                        <th scope="col" class="px-4 py-3">Team Name</th>
                        <th scope="col" class="px-4 py-3">Members</th>
                        <th scope="col" class="px-4 py-3">Target Companies</th>
                        <th scope="col" class="px-4 py-3">Assigned Date</th>
                        <th scope="col" class="px-4 py-3">Status</th>
                        <th scope="col" class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Empty state when no data available -->
                    <tr class="bg-white border-b">
                        <td colspan="6" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="text-gray-300 mb-2" viewBox="0 0 256 256">
                                    <path d="M125.18,156.94a64,64,0,1,0-82.36,0,100.23,100.23,0,0,0-39.49,32,12,12,0,0,0,19.35,14.2,76,76,0,0,1,122.64,0,12,12,0,0,0,19.36-14.2A100.33,100.33,0,0,0,125.18,156.94ZM44,108a40,40,0,1,1,40,40A40,40,0,0,1,44,108Zm206.1,97.67a12,12,0,0,1-16.78-2.57A76.31,76.31,0,0,0,172,172a12,12,0,0,1,0-24,40,40,0,1,0-14.85-77.16,12,12,0,1,1-8.92-22.28A64,64,0,0,1,236,108a63.91,63.91,0,0,1-24.82,50.94,99.92,99.92,0,0,1,36.32,29.93A12,12,0,0,1,250.1,205.67Z"></path>
                                </svg>
                                <p class="text-gray-500 text-base">No team assignments found</p>
                                <a href="{{ route('sales_mission.field.assignments.create') }}" class="mt-3 text-primary hover:text-amber-700 font-medium">
                                    Create your first team assignment
                                </a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Data rows will be added here when available -->
                    {{-- Example row (commented out for now)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">Team Alpha</td>
                        <td class="px-4 py-3">John Doe, Jane Smith</td>
                        <td class="px-4 py-3">PT Wijaya Karya, PT Adhi Karya</td>
                        <td class="px-4 py-3">May 20, 2023</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center space-x-2">
                                <a href="{{ route('sales_mission.field.assignments.edit', 1) }}" class="text-primary hover:text-amber-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 256 256">
                                        <path d="M224,120v88a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V48A16,16,0,0,1,48,32h88a8,8,0,0,1,0,16H48V208H208V120a8,8,0,0,1,16,0Zm16-96a8,8,0,0,0-2.34-5.66,8.18,8.18,0,0,0-11.32,0l-96,96a8.1,8.1,0,0,0-1.89,3.06l-8,24a8,8,0,0,0,10.17,10.17l24-8a8.1,8.1,0,0,0,3.06-1.89l96-96A8,8,0,0,0,240,24Z"></path>
                                    </svg>
                                </a>
                                <form action="{{ route('sales_mission.field.assignments.delete', 1) }}" method="POST" class="inline">
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
            {{ $assignments->links() }}
        </div> --}}
    </div>
</div>
@endsection 