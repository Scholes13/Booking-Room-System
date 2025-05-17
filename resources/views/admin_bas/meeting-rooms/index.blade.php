@extends('admin_bas.layout')

@section('title', 'Meeting Rooms')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg shadow-sm mb-2 flex items-center" role="alert">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
        </svg>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Meeting Rooms</h1>
            <p class="text-gray-500 mt-1">View and manage all meeting rooms</p>
        </div>
        <button id="btnAddRoom" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-lg h-10 px-5 bg-[#24448c] text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em] hover:bg-[#1c3670] transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Room
        </button>
    </div>

    <div class="flex flex-col gap-6 bg-white rounded-lg p-6 shadow-sm">
        <!-- Search Bar -->
        <div class="relative w-full md:w-1/2">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input type="search" id="searchRooms" class="block w-full p-3 ps-10 text-sm text-dark border border-gray-200 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" placeholder="Search for meeting rooms...">
        </div>

        <!-- Rooms Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-5 py-3.5">No</th>
                        <th scope="col" class="px-5 py-3.5">Room Name</th>
                        <th scope="col" class="px-5 py-3.5">Capacity</th>
                        <th scope="col" class="px-5 py-3.5">Description</th>
                        <th scope="col" class="px-5 py-3.5">Facilities</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="roomsTableBody">
                    @foreach($rooms as $index => $room)
                    <tr class="border-b border-gray-200 hover:bg-gray-50 room-row">
                        <td class="px-5 py-4">{{ $index + 1 }}</td>
                        <td class="px-5 py-4 font-medium">{{ $room->name }}</td>
                        <td class="px-5 py-4">{{ $room->capacity ?? 'Not set' }}</td>
                        <td class="px-5 py-4">{{ $room->description }}</td>
                        <td class="px-5 py-4">
                            @if(!empty($room->facilities))
                                @php
                                    $facilities = json_decode($room->facilities);
                                @endphp
                                @if(is_array($facilities) && count($facilities) > 0)
                                    <div class="flex flex-wrap gap-1">
                                    @foreach($facilities as $facility)
                                        @php
                                            // Improve facility display names
                                            $facilityText = $facility;
                                            if (strtolower($facility) === 'wifi') {
                                                $facilityText = 'Wi-Fi';
                                            } elseif (strtolower($facility) === 'ac') {
                                                $facilityText = 'AC';
                                            } else {
                                                $facilityText = ucfirst($facility);
                                            }
                                        @endphp
                                        <span class="inline-block px-2 py-1 bg-gray-100 text-xs rounded-md mb-1 mr-1">{{ $facilityText }}</span>
                                    @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <button type="button" class="edit-room px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-700 rounded-md hover:bg-blue-100 transition-colors inline-flex items-center" data-id="{{ $room->id }}" data-name="{{ $room->name }}" data-capacity="{{ $room->capacity }}" data-description="{{ $room->description }}" data-facilities="{{ $room->facilities }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button type="button" class="delete-room px-3 py-1.5 text-xs font-medium bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors inline-flex items-center" data-id="{{ $room->id }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    <!-- No Data Found Message -->
                    <tr id="noDataRow" class="{{ count($rooms) > 0 ? 'hidden' : '' }}">
                        <td colspan="6" class="px-5 py-10 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-16 h-16 text-gray-300 mb-3" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                </svg>
                                <h3 class="text-lg font-medium text-gray-900 mb-1">No Data Found</h3>
                                <p class="text-gray-500 text-sm">Try adding a new meeting room</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Room Modal -->
<div id="addRoomModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full mx-4 transform transition-transform duration-300 scale-100">
        <h3 class="text-xl font-bold text-center mb-4">Add Meeting Room</h3>
        
        <form action="{{ route('bas.meeting_rooms.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Room Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" 
                        placeholder="Enter meeting room name"
                        required
                    />
                </div>
                
                <div class="space-y-2">
                    <label for="capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input 
                        type="number" 
                        id="capacity" 
                        name="capacity" 
                        min="1"
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" 
                        placeholder="Number of people"
                    />
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" 
                    placeholder="Room description, additional information, etc."
                ></textarea>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Facilities</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex items-center">
                        <input id="add-projector" type="checkbox" name="facilities[]" value="projector" class="h-4 w-4 text-[#24448c] focus:ring-[#24448c] rounded">
                        <label for="add-projector" class="ml-2 text-sm text-gray-700">Projector</label>
                    </div>
                    <div class="flex items-center">
                        <input id="add-whiteboard" type="checkbox" name="facilities[]" value="whiteboard" class="h-4 w-4 text-[#24448c] focus:ring-[#24448c] rounded">
                        <label for="add-whiteboard" class="ml-2 text-sm text-gray-700">Whiteboard</label>
                    </div>
                    <div class="flex items-center">
                        <input id="add-ac" type="checkbox" name="facilities[]" value="ac" class="h-4 w-4 text-[#24448c] focus:ring-[#24448c] rounded">
                        <label for="add-ac" class="ml-2 text-sm text-gray-700">Air Conditioning</label>
                    </div>
                    <div class="flex items-center">
                        <input id="add-wifi" type="checkbox" name="facilities[]" value="wifi" class="h-4 w-4 text-[#24448c] focus:ring-[#24448c] rounded">
                        <label for="add-wifi" class="ml-2 text-sm text-gray-700">Wi-Fi</label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelAddRoom" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-[#24448c] text-white rounded-lg font-medium hover:bg-[#1c3670] transition-colors focus:outline-none focus:ring-2 focus:ring-[#24448c]">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Room Modal -->
<div id="editRoomModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-2xl w-full mx-4 transform transition-transform duration-300 scale-100">
        <h3 class="text-xl font-bold text-center mb-4">Edit Meeting Room</h3>
        
        <form id="editRoomForm" action="" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="edit-name" class="block text-sm font-medium text-gray-700">Room Name</label>
                    <input 
                        type="text" 
                        id="edit-name" 
                        name="name" 
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" 
                        placeholder="Enter meeting room name"
                        required
                    />
                </div>
                
                <div class="space-y-2">
                    <label for="edit-capacity" class="block text-sm font-medium text-gray-700">Capacity</label>
                    <input 
                        type="number" 
                        id="edit-capacity" 
                        name="capacity" 
                        min="1"
                        class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" 
                        placeholder="Number of people"
                    />
                </div>
            </div>
            
            <div class="space-y-2">
                <label for="edit-description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea 
                    id="edit-description" 
                    name="description" 
                    rows="4" 
                    class="w-full p-3 text-sm text-gray-700 border border-gray-300 rounded-lg bg-gray-50 focus:ring-[#24448c] focus:border-[#24448c] transition-all shadow-sm" 
                    placeholder="Room description, additional information, etc."
                ></textarea>
            </div>
            
            <div class="space-y-2">
                <label class="block text-sm font-medium text-gray-700">Facilities</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="flex items-center">
                        <input id="edit-projector" type="checkbox" name="facilities[]" value="projector" class="h-4 w-4 text-[#24448c] focus:ring-[#24448c] rounded">
                        <label for="edit-projector" class="ml-2 text-sm text-gray-700">Projector</label>
                    </div>
                    <div class="flex items-center">
                        <input id="edit-whiteboard" type="checkbox" name="facilities[]" value="whiteboard" class="h-4 w-4 text-[#24448c] focus:ring-[#24448c] rounded">
                        <label for="edit-whiteboard" class="ml-2 text-sm text-gray-700">Whiteboard</label>
                    </div>
                    <div class="flex items-center">
                        <input id="edit-ac" type="checkbox" name="facilities[]" value="ac" class="h-4 w-4 text-[#24448c] focus:ring-[#24448c] rounded">
                        <label for="edit-ac" class="ml-2 text-sm text-gray-700">Air Conditioning</label>
                    </div>
                    <div class="flex items-center">
                        <input id="edit-wifi" type="checkbox" name="facilities[]" value="wifi" class="h-4 w-4 text-[#24448c] focus:ring-[#24448c] rounded">
                        <label for="edit-wifi" class="ml-2 text-sm text-gray-700">Wi-Fi</label>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-center gap-3 mt-6">
                <button type="button" id="cancelEditRoom" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-[#24448c] text-white rounded-lg font-medium hover:bg-[#1c3670] transition-colors focus:outline-none focus:ring-2 focus:ring-[#24448c]">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal (Hidden by default) -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4 transform transition-transform duration-300 scale-100">
        <div class="flex justify-center mb-4 text-red-500">
            <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
        </div>
        <h3 class="text-xl font-bold text-center mb-2">Confirm Deletion</h3>
        <p class="text-gray-600 text-center mb-6">Are you sure you want to delete this meeting room? This action cannot be undone.</p>
        <div class="flex justify-center gap-3">
            <button id="cancelDelete" class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-gray-200">
                Cancel
            </button>
            <form id="deleteForm" method="POST" class="flex-1">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full px-4 py-2.5 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    Delete
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.getElementById('searchRooms');
        const roomRows = document.querySelectorAll('.room-row');
        const noDataRow = document.getElementById('noDataRow');
        
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            let visibleCount = 0;
            
            roomRows.forEach(row => {
                const roomName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const roomCapacity = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                const roomDesc = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                const roomFacilities = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
                
                if (roomName.includes(searchText) || 
                    roomDesc.includes(searchText) || 
                    roomCapacity.includes(searchText) || 
                    roomFacilities.includes(searchText)) {
                    row.classList.remove('hidden');
                    visibleCount++;
                } else {
                    row.classList.add('hidden');
                }
            });
            
            // Show/hide "No Data Found" message
            if (visibleCount === 0 && roomRows.length > 0) {
                noDataRow.classList.remove('hidden');
            } else {
                noDataRow.classList.add('hidden');
            }
        });

        // Add Room Modal
        const btnAddRoom = document.getElementById('btnAddRoom');
        const addRoomModal = document.getElementById('addRoomModal');
        const cancelAddRoom = document.getElementById('cancelAddRoom');
        
        btnAddRoom.addEventListener('click', function() {
            addRoomModal.classList.remove('hidden');
            addRoomModal.classList.add('flex');
        });
        
        cancelAddRoom.addEventListener('click', function() {
            addRoomModal.classList.add('hidden');
            addRoomModal.classList.remove('flex');
        });

        // Edit Room Modal
        const editButtons = document.querySelectorAll('.edit-room');
        const editRoomModal = document.getElementById('editRoomModal');
        const cancelEditRoom = document.getElementById('cancelEditRoom');
        const editRoomForm = document.getElementById('editRoomForm');
        
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const roomId = this.dataset.id;
                const roomName = this.dataset.name;
                const roomCapacity = this.dataset.capacity;
                const roomDescription = this.dataset.description;
                const roomFacilities = this.dataset.facilities;
                
                // Set the form action URL
                editRoomForm.action = `{{ route('bas.meeting_rooms.update', '') }}/${roomId}`;
                
                // Fill form fields with current data
                document.getElementById('edit-name').value = roomName;
                document.getElementById('edit-capacity').value = roomCapacity;
                document.getElementById('edit-description').value = roomDescription;
                
                // Reset all checkboxes first
                document.getElementById('edit-projector').checked = false;
                document.getElementById('edit-whiteboard').checked = false;
                document.getElementById('edit-ac').checked = false;
                document.getElementById('edit-wifi').checked = false;
                
                // Check appropriate facilities
                if (roomFacilities) {
                    try {
                        const facilities = JSON.parse(roomFacilities);
                        if (Array.isArray(facilities)) {
                            facilities.forEach(facility => {
                                const checkbox = document.getElementById(`edit-${facility}`);
                                if (checkbox) checkbox.checked = true;
                            });
                        }
                    } catch (e) {
                        console.error('Error parsing facilities JSON:', e);
                    }
                }
                
                // Show modal
                editRoomModal.classList.remove('hidden');
                editRoomModal.classList.add('flex');
            });
        });
        
        cancelEditRoom.addEventListener('click', function() {
            editRoomModal.classList.add('hidden');
            editRoomModal.classList.remove('flex');
        });

        // Delete room functionality
        const deleteButtons = document.querySelectorAll('.delete-room');
        const deleteModal = document.getElementById('deleteModal');
        const cancelDelete = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const roomId = this.dataset.id;
                console.log('Delete clicked for room ID:', roomId);
                
                deleteForm.action = `{{ route('bas.meeting_rooms.delete', '') }}/${roomId}`;
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            });
        });
        
        cancelDelete.addEventListener('click', function() {
            console.log('Delete cancelled');
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        });
    });
</script>
@endpush 