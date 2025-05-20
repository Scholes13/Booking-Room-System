@extends('sales_officer.layout')

@section('title', 'Activities')
@section('header', 'Activities')
@section('description', 'Manage your activities')

@section('content')
<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100">
    <div class="flex justify-between items-center mb-6">
        <h4 class="text-base font-semibold text-gray-800">All Activities</h4>
        <a href="{{ route('sales_officer.activities.create') }}" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
            </svg>
            Create New
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company Name</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JSO Lead Status</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">History Follow Up</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $activity->contact ? $activity->contact->company_name : 'N/A' }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if(str_contains($activity->jso_lead_status, 'Hot'))
                                    bg-red-100 text-red-800
                                @elseif(str_contains($activity->jso_lead_status, 'Cold'))
                                    bg-blue-100 text-blue-800
                                @elseif(str_contains($activity->jso_lead_status, 'Handed'))
                                    bg-green-100 text-green-800
                                @elseif(str_contains($activity->jso_lead_status, 'progress'))
                                    bg-yellow-100 text-yellow-800
                                @elseif(str_contains($activity->jso_lead_status, 'Lost'))
                                    bg-gray-100 text-gray-800
                                @else
                                    bg-purple-100 text-purple-800
                                @endif
                            ">
                                {{ $activity->jso_lead_status }}
                            </span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($activity->next_follow_up)
                                    <span class="bg-gray-100 px-2 py-1 rounded-md">{{ $activity->next_follow_up }}</span>
                                @else
                                    <span class="text-gray-400">Not scheduled</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $activity->city }}, {{ $activity->province }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y') }}</div>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('sales_officer.activities.edit', $activity->id) }}" class="text-blue-600 hover:text-blue-800">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('sales_officer.activities.destroy', $activity->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this activity?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-sm text-center text-gray-500">No activities found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $activities->links() }}
    </div>
</div>
@endsection 