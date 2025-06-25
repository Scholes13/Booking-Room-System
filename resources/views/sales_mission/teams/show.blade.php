@extends('sales_mission.layout')

@section('title', 'Team Details')
@section('header', $team->name)
@section('description', 'View team information and assigned activities')

@push('styles')
<style>
    .badge {
        @apply inline-flex text-xs font-semibold px-2.5 py-0.5 rounded-full;
    }
    
    .badge-blue {
        @apply bg-blue-100 text-blue-800;
    }
    
    .badge-green {
        @apply bg-green-100 text-green-800;
    }
    
    .badge-indigo {
        @apply bg-indigo-100 text-indigo-800;
    }
    
    .badge-red {
        @apply bg-red-100 text-red-800;
    }
    
    .badge-amber {
        @apply bg-amber-100 text-amber-800;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-6">
    <!-- Action Buttons -->
    <div class="flex justify-end">
        <div class="space-x-2">
            <a href="{{ route('sales_mission.teams.edit', $team) }}" class="px-3 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Team
            </a>
            <a href="{{ route('sales_mission.field-visits.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Assign to Activity
            </a>
            <form action="{{ route('sales_mission.teams.destroy', $team) }}" method="POST" class="inline-block">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors inline-flex items-center" onclick="return confirm('Are you sure you want to delete this team?')">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete Team
                </button>
            </form>
        </div>
    </div>
    
    <!-- Team Info Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Team Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Team Name</h3>
                        <p class="text-gray-900 font-medium mt-1">{{ $team->name }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Created By</h3>
                        <p class="text-gray-900 mt-1">{{ $team->creator ? $team->creator->name : 'N/A' }}</p>
                    </div>
                </div>
                
                <div>
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Description</h3>
                        <p class="text-gray-900 mt-1">{{ $team->description ?: 'No description provided.' }}</p>
                    </div>
                    
                    <div class="mb-4">
                        <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                        <p class="text-gray-900 mt-1">{{ $team->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Team Members Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Team Members ({{ $team->members->count() }})</h2>
            
            @if($team->members->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($team->members as $member)
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gray-100 rounded-full p-2">
                            <svg class="h-6 w-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                            @if($member->email || $member->phone)
                            <div class="text-xs text-gray-500">
                                {{ $member->email ?? '' }}
                                {{ $member->email && $member->phone ? ' • ' : '' }}
                                {{ $member->phone ?? '' }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <p class="text-gray-500">No team members found.</p>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Team Activities Card -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Assigned Activities ({{ $team->activities->count() }})</h2>
            
            @if($team->activities->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3">Company</th>
                            <th scope="col" class="px-4 py-3">Location</th>
                            <th scope="col" class="px-4 py-3">Date</th>
                            <th scope="col" class="px-4 py-3">Status</th>
                            <th scope="col" class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($team->activities as $activity)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $activity->salesMissionDetail->company_name ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $activity->salesMissionDetail->company_pic ?? '' }} 
                                    {{ $activity->salesMissionDetail->company_position ? '(' . $activity->salesMissionDetail->company_position . ')' : '' }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                {{ $activity->city }}, {{ $activity->province }}
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ $activity->start_datetime ? $activity->start_datetime->format('d M Y') : 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ $activity->start_datetime ? $activity->start_datetime->format('H:i') : '' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusClass = 'badge-gray';
                                    $status = $activity->status;
                                    
                                    if($status === 'scheduled') $statusClass = 'badge-blue';
                                    elseif($status === 'ongoing') $statusClass = 'badge-green';
                                    elseif($status === 'completed') $statusClass = 'badge-indigo';
                                    elseif($status === 'cancelled') $statusClass = 'badge-red';
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                @foreach($activity->teamAssignments as $assignment)
                                    @if($assignment->team_id == $team->id)
                                        <a href="{{ route('sales_mission.field-visits.show', $assignment) }}" class="text-blue-600 hover:underline">View Assignment</a>
                                    @endif
                                @endforeach
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="bg-gray-50 rounded-lg p-6 text-center">
                <p class="text-gray-500">This team has not been assigned to any activities yet.</p>
                <a href="{{ route('sales_mission.field-visits.create') }}" class="inline-flex items-center text-amber-600 hover:text-amber-700 mt-2">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Assign to an activity
                </a>
            </div>
            @endif
        </div>
    </div>
    
    <!-- Back Button -->
    <div class="mt-4">
        <a href="{{ route('sales_mission.teams.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Teams List
        </a>
    </div>
</div>
@endsection 