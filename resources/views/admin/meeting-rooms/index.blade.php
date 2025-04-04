@php
    $layout = isset($isSuperAdmin) && $isSuperAdmin ? 'superadmin.layout' : 'admin.layout';
@endphp

@extends($layout)

@section('title', 'Ruang Meeting')

@section('content')
<div class="flex flex-col gap-6 h-full">
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="flex items-center justify-between">
        <h1 class="text-dark text-2xl font-bold leading-tight tracking-[-0.015em]">Ruang Meeting</h1>
        <a href="{{ route('admin.meeting_rooms.create') }}" class="flex cursor-pointer items-center justify-center overflow-hidden rounded-full h-10 px-4 bg-primary text-white gap-2 text-sm font-bold leading-normal tracking-[0.015em]">
            <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px" fill="currentColor" viewBox="0 0 256 256">
                <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z"></path>
            </svg>
            Tambah Ruang
        </a>
    </div>

    <div class="flex flex-col gap-6 bg-white rounded-lg p-6">
        <!-- Search Bar -->
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input type="search" id="searchRooms" class="block w-full p-3 ps-10 text-sm text-dark border border-border rounded-md bg-gray-50 focus:ring-primary focus:border-primary" placeholder="Cari ruang meeting">
        </div>

        <!-- Rooms Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-sm text-dark">
                    <tr class="border-b border-border">
                        <th scope="col" class="px-4 py-3">No</th>
                        <th scope="col" class="px-4 py-3">Nama Ruang</th>
                        <th scope="col" class="px-4 py-3">Kapasitas</th>
                        <th scope="col" class="px-4 py-3">Deskripsi</th>
                        <th scope="col" class="px-4 py-3">Fasilitas</th>
                        <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="roomsTableBody">
                    @foreach($rooms as $index => $room)
                    <tr class="border-b border-border room-row">
                        <td class="px-4 py-4">{{ $index + 1 }}</td>
                        <td class="px-4 py-4 font-medium">{{ $room->name }}</td>
                        <td class="px-4 py-4">{{ $room->capacity ?? 'Tidak diatur' }}</td>
                        <td class="px-4 py-4">{{ $room->description }}</td>
                        <td class="px-4 py-4">
                            @if(!empty($room->facilities))
                                @php
                                    $facilities = json_decode($room->facilities);
                                @endphp
                                @if(is_array($facilities) && count($facilities) > 0)
                                    <div class="flex flex-wrap gap-1">
                                    @foreach($facilities as $facility)
                                        @php
                                            // Memperbaiki tampilan nama fasilitas
                                            $facilityText = $facility;
                                            if (strtolower($facility) === 'wifi') {
                                                $facilityText = 'Wi-Fi';
                                            } elseif (strtolower($facility) === 'ac') {
                                                $facilityText = 'AC';
                                            } else {
                                                $facilityText = ucfirst($facility);
                                            }
                                        @endphp
                                        <span class="inline-block px-2 py-1 bg-gray-100 text-xs rounded-full">{{ $facilityText }}</span>
                                    @endforeach
                                    </div>
                                @else
                                    -
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex flex-col items-end space-y-1">
                                <a href="{{ route('admin.meeting_rooms.edit', $room->id) }}" class="text-accent font-medium text-sm hover:underline">Edit</a>
                                <button type="button" class="delete-room text-danger font-medium text-sm hover:underline" data-id="{{ $room->id }}">Hapus</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if(count($rooms) == 0)
                    <tr class="border-b border-border">
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                            Belum ada ruang meeting. Silahkan tambahkan ruang meeting baru.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal (Hidden by default) -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-md w-full">
        <h3 class="text-lg font-bold mb-4">Konfirmasi Penghapusan</h3>
        <p class="text-gray-600 mb-6">Apakah Anda yakin ingin menghapus ruang meeting ini? Tindakan ini tidak dapat dibatalkan.</p>
        <div class="flex justify-end gap-3">
            <button id="cancelDelete" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700">Batal</button>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-danger text-white rounded-lg">Hapus</button>
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
        
        searchInput.addEventListener('keyup', function() {
            const searchText = this.value.toLowerCase();
            
            roomRows.forEach(row => {
                const roomName = row.children[1].textContent.toLowerCase();
                const roomCapacity = row.children[2].textContent.toLowerCase();
                const roomDesc = row.children[3].textContent.toLowerCase();
                const roomFacilities = row.children[4].textContent.toLowerCase();
                
                if (roomName.includes(searchText) || 
                    roomDesc.includes(searchText) || 
                    roomCapacity.includes(searchText) || 
                    roomFacilities.includes(searchText)) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });

        // Delete room functionality
        const deleteButtons = document.querySelectorAll('.delete-room');
        const deleteModal = document.getElementById('deleteModal');
        const cancelDelete = document.getElementById('cancelDelete');
        const deleteForm = document.getElementById('deleteForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const roomId = this.dataset.id;
                deleteForm.action = `{{ route('admin.meeting_rooms.delete', '') }}/${roomId}`;
                deleteModal.classList.remove('hidden');
                deleteModal.classList.add('flex');
            });
        });
        
        cancelDelete.addEventListener('click', function() {
            deleteModal.classList.add('hidden');
            deleteModal.classList.remove('flex');
        });
    });
</script>
@endpush