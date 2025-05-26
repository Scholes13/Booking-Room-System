@extends('sales_mission.layout')

@section('title', 'Teams Management')
@section('header', 'Teams Management')
@section('description', 'Create and manage teams for field visits')

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
    
    .badge-gray {
        @apply bg-gray-100 text-gray-800;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-6">
    <!-- Header with Create Button -->
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Teams List</h2>
        <a href="{{ route('sales_mission.teams.create') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create New Team
        </a>
    </div>

    <!-- Teams Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">Team Name</th>
                        <th scope="col" class="px-5 py-3.5">Members</th>
                        <th scope="col" class="px-5 py-3.5">Description</th>
                        <th scope="col" class="px-5 py-3.5">Created By</th>
                        <th scope="col" class="px-5 py-3.5">Created At</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teams as $team)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900">{{ $team->name }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1">
                                <span class="badge badge-blue">{{ $team->members ? $team->members->count() : 0 }} members</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-500 line-clamp-2">{{ $team->description ?? 'No description available' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-500">{{ $team->creator ? $team->creator->name : 'N/A' }}</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-gray-500">{{ $team->created_at->format('d M Y') }}</div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="{{ route('sales_mission.teams.show', $team) }}" class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </a>
                                <a href="{{ route('sales_mission.teams.edit', $team) }}" class="px-3 py-1.5 text-xs font-medium bg-amber-50 text-amber-700 rounded-md hover:bg-amber-100 transition-colors inline-flex items-center">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </a>
                                <form action="{{ route('sales_mission.teams.destroy', $team) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" onclick="return confirm('Are you sure you want to delete this team?')">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No Teams Found</h3>
                                <p class="text-gray-500 text-sm mb-4">Start by creating your first team</p>
                                <a href="{{ route('sales_mission.teams.create') }}" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Create New Team
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-5 py-4">
            {{ $teams->links() }}
        </div>
    </div>
</div>
@endsection 