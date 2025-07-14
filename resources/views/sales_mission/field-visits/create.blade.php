@extends('sales_mission.layout')

@section('title', 'Assign Team to Activity')
@section('header', 'Assign Team to Activity')
@section('description', 'Create a new field visit by assigning a team to an activity')

@push('styles')
<style>
    .card-hover:hover {
        /* @apply bg-gray-50; */ /* Using JS for hover to avoid conflict with selected state */
    }
    
    .card-selected {
        @apply ring-2 ring-amber-500 bg-amber-50 shadow-lg;
    }

    .card-hover-effect {
        @apply bg-gray-50;
    }

    /* Custom scrollbar for selection lists */
    .selection-list::-webkit-scrollbar {
        width: 8px;
    }
    .selection-list::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .selection-list::-webkit-scrollbar-thumb {
        background: #c5c5c5;
        border-radius: 10px;
    }
    .selection-list::-webkit-scrollbar-thumb:hover {
        background: #a0a0a0;
    }
</style>
@endpush

@section('content')
<div class="flex flex-col gap-y-6">
    <!-- Back Button -->
    <div>
        <a href="{{ route('sales_mission.field-visits.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Field Visits
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm leading-5 text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm leading-5 text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm leading-5 font-medium text-red-700">Please fix the following errors:</p>
                <ul class="mt-2 text-sm leading-5 text-red-700 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Form Container -->
    <div class="bg-white rounded-xl shadow-xl overflow-hidden">
        <form action="{{ route('sales_mission.field-visits.store') }}" method="POST" class="divide-y divide-gray-200">
                @csrf
            
            <!-- Top Section: Filters and Notes -->
            <div class="p-6 space-y-6">
                <!-- Search Filters -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Filters</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4 items-end bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <div>
                            <label for="search_team" class="block text-sm font-medium text-gray-700 mb-1">Search Team</label>
                            <input type="text" name="search_team_filter" id="search_team" value="{{ $searchTeamValue ?? '' }}" class="w-full p-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm" placeholder="Enter team name...">
                        </div>
                        <div>
                            <label for="search_activity" class="block text-sm font-medium text-gray-700 mb-1">Search Activity (Company/Name)</label>
                            <input type="text" name="search_activity_filter" id="search_activity" value="{{ $searchActivityValue ?? '' }}" class="w-full p-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm" placeholder="Enter company or activity name...">
                        </div>
                        
                        {{-- Filter Tanggal Baru --}}
                        <div class="md:col-span-1">
                            <label for="filter_date" class="block text-sm font-medium text-gray-700 mb-1">Filter by Activity Date</label>
                            <input type="text" name="filter_date_input" id="filter_date" value="{{ $filterDateValue ?? '' }}" class="flatpickr-date w-full p-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm" placeholder="Select date...">
                        </div>

                        {{-- Filter Lokasi Baru --}}
                        <div class="md:col-span-1">
                            <label for="filter_location" class="block text-sm font-medium text-gray-700 mb-1">Filter by Location (City)</label>
                            <select name="filter_location_input" id="filter_location" class="w-full p-2.5 border border-gray-300 rounded-md shadow-sm focus:ring-amber-500 focus:border-amber-500 text-sm">
                                <option value="">All Cities</option>
                                @isset($cities)
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}" {{ (isset($filterLocationValue) && $filterLocationValue == $city) ? 'selected' : '' }}>
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <div class="md:col-span-2 flex justify-start items-end"> {{-- Ubah md:col-span-2 menjadi 1 jika filter date di atasnya, atau atur grid --}}
                            <button type="button" id="reset_filters" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors text-sm">Reset Filters</button>
                        </div>
                    </div>
                </div>
                
                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="4"
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg focus:ring-amber-500 focus:border-amber-500 transition-all shadow-sm hover:border-gray-400"
                        placeholder="Add any relevant notes for this field visit..."
                    >{{ old('notes') }}</textarea>
                </div>
                </div>
                
            <!-- Middle Section: Team and Activity Selection -->
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Team Selection -->
                    <section>
                        <h3 class="text-xl font-semibold text-gray-800 mb-1">1. Select Team</h3>
                        <p class="text-sm text-gray-500 mb-4">Choose the team to assign for this field visit.</p>
                        <div id="team-list-container" class="grid grid-cols-1 gap-4 max-h-[400px] overflow-y-auto selection-list pr-2 pb-2 border border-gray-200 rounded-lg p-3 bg-slate-50">
                            @include('sales_mission.field-visits.partials._team-list', ['teams' => $teams])
                        </div>
                        @error('team_id')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </section>
                    
                    <!-- Activity Selection -->
                    <section>
                        <h3 class="text-xl font-semibold text-gray-800 mb-1">2. Select Activity</h3>
                        <p class="text-sm text-gray-500 mb-4">Choose the activity or company visit details.</p>
                        <div id="activity-list-container" class="grid grid-cols-1 gap-4 max-h-[400px] overflow-y-auto selection-list pr-2 pb-2 border border-gray-200 rounded-lg p-3 bg-slate-50">
                            @include('sales_mission.field-visits.partials._activity-list', ['activities' => $activities])
                        </div>
                         @error('activity_id')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </section>
                </div>
                </div>
                
            <!-- Bottom Section: Form Actions -->
            <div class="p-6 flex justify-end gap-x-3 bg-gray-50">
                <a href="{{ route('sales_mission.field-visits.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-100 transition-colors">
                        Cancel
                    </a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 focus:ring-4 focus:outline-none focus:ring-amber-300 transition-colors">
                        Create Field Visit
                    </button>
                </div>
            </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr(".flatpickr-date", {
            dateFormat: "Y-m-d",
            locale: "id", // Sesuaikan jika perlu
            allowInput: true,
            // altInput: true, // Opsional, jika ingin format berbeda untuk tampilan
            // altFormat: "d M Y", // Opsional,
        });

        const searchTeamInput = document.getElementById('search_team');
        const searchActivityInput = document.getElementById('search_activity');
        const filterDateInput = document.getElementById('filter_date');
        const filterLocationInput = document.getElementById('filter_location'); // Tetap, karena ID nya sama
        const teamListContainer = document.getElementById('team-list-container');
        const activityListContainer = document.getElementById('activity-list-container');
        const resetFiltersButton = document.getElementById('reset_filters');
        let debounceTimeout;

        function initializeCardSelection(containerSelector, radioSelector, selectedClass) {
            const container = document.querySelector(containerSelector);
            if (!container) return;

            container.addEventListener('click', function(event) {
                const card = event.target.closest('.card-hover');
                if (!card) return;

                const radio = card.querySelector(radioSelector);
                if (!radio) return;

                // Remove selected class from all cards in this container
                container.querySelectorAll('.card-hover').forEach(c => {
                    c.classList.remove(selectedClass);
                    c.classList.remove('card-hover-effect'); // Remove hover effect if any
                });
                
                // Check the radio and add selected class to the clicked card
                radio.checked = true;
                card.classList.add(selectedClass);
            });

            // Add mouseover/mouseout for hover effect to non-selected cards
            container.addEventListener('mouseover', function(event) {
                const card = event.target.closest('.card-hover');
                if (card && !card.classList.contains(selectedClass)) {
                    card.classList.add('card-hover-effect');
                }
            });
            container.addEventListener('mouseout', function(event) {
                const card = event.target.closest('.card-hover');
                if (card) {
                    card.classList.remove('card-hover-effect');
                }
            });

            // Set initial selected state for any pre-selected radio
            const initiallySelectedRadio = container.querySelector(radioSelector + ':checked');
            if (initiallySelectedRadio) {
                const selectedCard = initiallySelectedRadio.closest('.card-hover');
                if (selectedCard) {
                    selectedCard.classList.add(selectedClass);
                }
            }
        }

        function fetchFilteredData() {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                const searchTeam = searchTeamInput.value;
                const searchActivity = searchActivityInput.value;
                const filterDate = filterDateInput.value;
                const filterLocation = filterLocationInput.value; // Ambil nilai dari select

                // Pastikan URLSearchParams selalu menyertakan format=json
                const params = new URLSearchParams({
                    search_team: searchTeam,
                    search_activity: searchActivity,
                    filter_date: filterDate,
                    filter_location: filterLocation,
                    format: 'json' // Penting: selalu minta JSON
                });

                // Bangun URL dengan parameter
                const url = `{{ route('sales_mission.field-visits.create') }}?${params.toString()}`;
                
                console.log('Fetching data from URL:', url);


                fetch(url, {
                    method: 'GET', // GET request
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest', // Header standar untuk AJAX
                        'Accept': 'application/json', // Eksplisit minta JSON
                    }
                })
                .then(response => {
                    console.log('Raw response:', response);
                    if (!response.ok) {
                        // Jika response tidak OK, coba baca sebagai teks untuk melihat error HTML
                        return response.text().then(text => {
                            console.error('Server returned an error page (HTML):', text);
                            throw new Error(`Network response was not ok: ${response.statusText}. Server returned HTML.`);
                        });
                    }
                    // Cek content type sebelum parsing JSON
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json();
                    } else {
                        return response.text().then(text => {
                            console.error('Response was not JSON. Received:', text);
                            throw new Error('Server did not return JSON.');
                        });
                    }
                })
                .then(data => {
                    console.log('Received data:', data);
                    if (data.teams_html !== undefined) {
                        teamListContainer.innerHTML = data.teams_html;
                        initializeCardSelection('#team-list-container', 'input[name="team_id"]', 'card-selected');
                    } else {
                        console.warn('teams_html not found in response');
                    }
                    if (data.activities_html !== undefined) {
                        activityListContainer.innerHTML = data.activities_html;
                        initializeCardSelection('#activity-list-container', 'input[name="activity_id"]', 'card-selected');
                    } else {
                        console.warn('activities_html not found in response');
                    }
                })
                .catch(error => {
                    console.error('Error fetching filtered data:', error);
                    // Anda bisa menambahkan notifikasi error ke pengguna di sini jika diperlukan
                    // Misalnya: activityListContainer.innerHTML = '<p class="text-red-500">Error loading activities.</p>';
                });
            }, 300); // Debounce delay 300ms
        }

        searchTeamInput.addEventListener('input', fetchFilteredData);
        searchActivityInput.addEventListener('input', fetchFilteredData);
        // Untuk Flatpickr, event yang tepat adalah 'onChange'
        flatpickrInstance = filterDateInput._flatpickr; // Akses instance flatpickr
        if(flatpickrInstance) {
            flatpickrInstance.config.onChange.push(function(selectedDates, dateStr, instance) {
                fetchFilteredData(); // Panggil fetchFilteredData saat tanggal berubah
            });
        }
        filterLocationInput.addEventListener('change', fetchFilteredData); // 'change' untuk select dropdown

        resetFiltersButton.addEventListener('click', function() {
            searchTeamInput.value = '';
            searchActivityInput.value = '';
            if(filterDateInput._flatpickr) { // Pastikan flatpickr ada
                filterDateInput._flatpickr.clear(); // Method clear() untuk flatpickr
            }
            filterLocationInput.value = ''; // Reset dropdown ke "All Cities"

            fetchFilteredData(); // Muat ulang data dengan filter kosong
        });

        // Initial card selection setup
        initializeCardSelection('#team-list-container', 'input[name="team_id"]', 'card-selected');
        initializeCardSelection('#activity-list-container', 'input[name="activity_id"]', 'card-selected');
    });
</script>
@endpush 