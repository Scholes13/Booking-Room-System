@extends('sales_mission.layout')

@section('title', 'Sales Mission Agenda')
@section('header', 'Generate Sales Mission Agenda')
@section('description', 'Create and export agenda for sales mission teams')

@section('content')
<div class="space-y-6">
    <!-- Agenda Generation Form -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('sales_mission.reports.agenda.generate') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Team Selection -->
            <div>
                <label for="team_id" class="block text-sm font-medium text-gray-700 mb-1">Select Team</label>
                <select id="team_id" name="team_id" required
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-sales focus:border-sales rounded-md">
                    <option value="">Choose a team...</option>
                    @foreach($teams as $team)
                        <option value="{{ $team->id }}">{{ $team->name }} ({{ $team->members->count() }} members)</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Range -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required
                        class="mt-1 block w-full border-gray-300 focus:ring-sales focus:border-sales rounded-md">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input type="date" id="end_date" name="end_date" required
                        class="mt-1 block w-full border-gray-300 focus:ring-sales focus:border-sales rounded-md">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <button type="submit" name="action" value="preview"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-sales hover:bg-sales-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sales">
                    <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Preview
                </button>
                <button type="submit" name="action" value="export"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="h-4 w-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export to Excel
                </button>
            </div>
        </form>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Only update end_date min when start_date changes
    document.getElementById('start_date').addEventListener('change', function() {
        document.getElementById('end_date').min = this.value;
        if (document.getElementById('end_date').value < this.value) {
            document.getElementById('end_date').value = this.value;
        }
    });
});
</script>
@endpush 