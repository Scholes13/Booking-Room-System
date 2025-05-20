@extends('sales_mission.layout')

@section('title', 'Activity Log Detail')
@section('header', 'Activity Log Detail')
@section('description', 'Detailed information about activity log')

@section('content')
<div class="mb-6">
    <a href="{{ route('sales_mission.logs.index') }}" class="text-amber-600 hover:text-amber-800 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
        </svg>
        Back to logs
    </a>
</div>

<div class="bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="border-b border-gray-200 px-6 py-4 bg-gray-50">
        <h2 class="text-lg font-medium text-gray-900">Activity Log #{{ $log->id }}</h2>
    </div>
    
    <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Basic Information -->
        <div class="col-span-1 md:col-span-2">
            <div class="bg-amber-50 border-l-4 border-amber-500 p-4 mb-4 rounded">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-800">
                            This record shows {{ ucfirst($log->action) }} action on {{ ucfirst(str_replace('_', ' ', $log->module)) }} module.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Admin Information</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="block text-sm font-medium text-gray-500">Admin</span>
                    <span class="block mt-1 text-gray-900">{{ $log->user->name ?? 'Unknown' }}</span>
                </div>
                
                <div>
                    <span class="block text-sm font-medium text-gray-500">Role</span>
                    <span class="block mt-1 text-gray-900">{{ ucfirst($log->user->role ?? 'Unknown') }}</span>
                </div>
                
                <div>
                    <span class="block text-sm font-medium text-gray-500">IP Address</span>
                    <span class="block mt-1 text-gray-900">{{ $log->ip_address }}</span>
                </div>
                
                <div>
                    <span class="block text-sm font-medium text-gray-500">User Agent</span>
                    <span class="block mt-1 text-xs text-gray-900 break-words">{{ $log->user_agent ?? 'Not recorded' }}</span>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Activity Details</h3>
            
            <div class="space-y-4">
                <div>
                    <span class="block text-sm font-medium text-gray-500">Date & Time</span>
                    <span class="block mt-1 text-gray-900">{{ $log->created_at->format('d M Y, H:i:s') }}</span>
                </div>
                
                <div>
                    <span class="block text-sm font-medium text-gray-500">Action</span>
                    <span class="px-3 py-1 inline-flex mt-1 text-sm leading-5 font-medium rounded-full 
                        @if($log->action == 'create') bg-green-100 text-green-800
                        @elseif($log->action == 'update') bg-amber-100 text-amber-800
                        @elseif($log->action == 'delete') bg-red-100 text-red-800
                        @elseif($log->action == 'export') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        <span class="flex items-center">
                            @if($log->action == 'create')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            @elseif($log->action == 'update')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            @elseif($log->action == 'delete')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            @elseif($log->action == 'export')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            @endif
                            {{ ucfirst($log->action) }}
                        </span>
                    </span>
                </div>
                
                <div>
                    <span class="block text-sm font-medium text-gray-500">Module</span>
                    <span class="block mt-1 text-gray-900">{{ ucfirst(str_replace('_', ' ', $log->module)) }}</span>
                </div>
                
                <div>
                    <span class="block text-sm font-medium text-gray-500">Description</span>
                    <span class="block mt-1 text-gray-900">{{ $log->description }}</span>
                </div>
            </div>
        </div>
    </div>
    
    @if($log->properties)
    <div class="px-6 py-4">
        <h3 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-200">Additional Data</h3>
        
        <div class="bg-gray-50 p-4 rounded-lg overflow-x-auto">
            <pre class="text-xs text-gray-800">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        </div>
    </div>
    @endif
</div>
@endsection 