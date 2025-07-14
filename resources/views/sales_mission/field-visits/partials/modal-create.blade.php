<form id="createFieldVisitForm" action="{{ route('sales_mission.field-visits.store') }}" method="POST" class="space-y-6">
    @csrf
    
    <!-- Search Form -->
    <div class="mb-4 p-3 bg-gray-50 rounded-md border border-gray-200">
        <form method="GET" action="{{ route('sales_mission.field-visits.create') }}" class="grid grid-cols-1 md:grid-cols-2 gap-3 items-end">
            <input type="hidden" name="format" value="modal"> <!-- Keep modal format on search -->
            <div>
                <label for="search_team_modal" class="block text-xs font-medium text-gray-600 mb-1">Search Team</label>
                <input type="text" name="search_team" id="search_team_modal" value="{{ $searchTeamValue ?? '' }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-xs" placeholder="Team name...">
            </div>
            <div>
                <label for="search_activity_modal" class="block text-xs font-medium text-gray-600 mb-1">Search Activity</label>
                <input type="text" name="search_activity" id="search_activity_modal" value="{{ $searchActivityValue ?? '' }}" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-xs" placeholder="Company/Activity...">
            </div>
            <div class="md:col-span-2 flex items-center space-x-2">
                <button type="submit" class="px-3 py-1.5 bg-amber-500 text-white rounded-md hover:bg-amber-600 transition-colors text-xs w-full">Search</button>
                <a href="{{ route('sales_mission.field-visits.create', ['format' => 'modal']) }}" class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 transition-colors text-xs text-center w-full">Reset</a>
            </div>
        </form>
    </div>
    
    <div id="formErrors" class="hidden bg-red-50 text-red-700 p-4 rounded-lg mb-6">
        <div class="font-medium">Please fix the following errors:</div>
        <ul class="mt-2 list-disc list-inside text-sm" id="errorList"></ul>
    </div>
    
    <!-- Notes -->
    <div class="space-y-2">
        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
        <textarea 
            id="notes" 
            name="notes" 
            rows="3"
            class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm"
        >{{ old('notes') }}</textarea>
    </div>
    
    <!-- Team Selection -->
    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Select Team <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-48 overflow-y-auto">
            @foreach($teams as $team)
            <div class="border border-gray-200 rounded-lg p-4 card-hover transition-all">
                <div class="flex items-start">
                    <input 
                        type="radio" 
                        id="team_{{ $team->id }}" 
                        name="team_id" 
                        value="{{ $team->id }}" 
                        class="mt-1 w-4 h-4 text-amber-600 focus:ring-amber-500"
                        {{ old('team_id') == $team->id ? 'checked' : '' }}
                        required
                    >
                    <label for="team_{{ $team->id }}" class="ml-3 flex-1 cursor-pointer">
                        <div class="font-medium text-gray-900">{{ $team->name }}</div>
                        <div class="text-sm text-gray-500 mt-1">{{ $team->description ?? 'No description available' }}</div>
                        <div class="flex flex-wrap gap-1 mt-2">
                            <span class="inline-flex text-xs font-semibold px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                                {{ $team->members->count() }} members
                            </span>
                        </div>
                    </label>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- Activity Selection -->
    <div class="space-y-2">
        <label class="block text-sm font-medium text-gray-700">Select Activity <span class="text-red-500">*</span></label>
        <div class="grid grid-cols-1 gap-4 max-h-48 overflow-y-auto">
            @foreach($activities as $activity)
            <div class="border border-gray-200 rounded-lg p-4 card-hover transition-all">
                <div class="flex items-start">
                    <input 
                        type="radio" 
                        id="activity_{{ $activity->id }}" 
                        name="activity_id" 
                        value="{{ $activity->id }}" 
                        class="mt-1 w-4 h-4 text-amber-600 focus:ring-amber-500"
                        {{ old('activity_id') == $activity->id ? 'checked' : '' }}
                        required
                    >
                    <label for="activity_{{ $activity->id }}" class="ml-3 flex-1 cursor-pointer">
                        <div class="font-medium text-gray-900">{{ $activity->salesMissionDetail->company_name }}</div>
                        <div class="text-sm text-gray-500 mt-1">
                            <span class="font-medium">PIC:</span> {{ $activity->salesMissionDetail->company_pic }}
                            @if($activity->salesMissionDetail->company_position)
                                ({{ $activity->salesMissionDetail->company_position }})
                            @endif
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            <span class="font-medium">Location:</span> {{ $activity->city }}, {{ $activity->province }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            <span class="font-medium">Date:</span> 
                            {{ $activity->start_datetime ? \Carbon\Carbon::parse($activity->start_datetime)->format('d M Y H:i') : 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">
                            <span class="font-medium">Status:</span> {{ ucfirst($activity->status) }}
                        </div>
                    </label>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="flex justify-end space-x-3 pt-4">
        <button type="button" onclick="closeModal('createModal')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
            Cancel
        </button>
        <button type="button" onclick="submitCreateForm()" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors">
            Assign Team
        </button>
    </div>
</form>

<script>
    // Submit the create form with AJAX
    function submitCreateForm() {
        const form = document.getElementById('createFieldVisitForm');
        const formData = new FormData(form);
        
        // Clear previous errors
        document.getElementById('formErrors').classList.add('hidden');
        document.getElementById('errorList').innerHTML = '';
        
        // Submit the form with fetch API
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message and reload page
                alert(data.message);
                window.location.href = data.redirect;
            } else {
                // Show error messages
                const errorList = document.getElementById('errorList');
                document.getElementById('formErrors').classList.remove('hidden');
                
                if (typeof data.message === 'string') {
                    const li = document.createElement('li');
                    li.textContent = data.message;
                    errorList.appendChild(li);
                } else if (data.errors) {
                    for (const field in data.errors) {
                        data.errors[field].forEach(error => {
                            const li = document.createElement('li');
                            li.textContent = error;
                            errorList.appendChild(li);
                        });
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error submitting form:', error);
            alert('An error occurred while processing your request. Please try again.');
        });
    }
</script> 