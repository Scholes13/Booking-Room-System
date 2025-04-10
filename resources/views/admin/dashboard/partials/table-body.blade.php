<tbody class="divide-y divide-gray-800 bg-gray-900/30" id="tableBody">
    @php
        // Create a collection of unique bookings based on ID
        $uniqueBookings = collect($bookings)->keyBy('id')->values();
    @endphp
    
    @forelse($uniqueBookings as $booking)
        @php
            // Create Date objects for comparison
            $now = new DateTime();
            $bookingDate = new DateTime($booking->date);
            $startDateTime = clone $bookingDate;
            $startDateTime->setTime(...explode(':', $booking->start_time));
            $endDateTime = clone $bookingDate;
            $endDateTime->setTime(...explode(':', $booking->end_time));
            
            // Determine status
            if ($now >= $startDateTime && $now <= $endDateTime) {
                $status = "Ongoing";
                $statusClass = "bg-red-100 text-red-800";
            } elseif ($now < $startDateTime) {
                $status = "Scheduled";
                $statusClass = "bg-purple-100 text-purple-800";
            } else {
                $status = "Completed";
                $statusClass = "bg-green-100 text-green-800";
            }
        @endphp
        <tr class="hover:bg-gray-50 booking-row" data-id="{{ $booking->id }}" data-endtime="{{ $booking->date }} {{ $booking->end_time }}">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $booking->meetingRoom->name ?? 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->department }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $booking->nama }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 booking-date">{{ $booking->date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <span class="booking-time">{{ $booking->start_time }}</span> - <span class="booking-endtime">{{ $booking->end_time }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                    {{ $status }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex gap-2">
                    <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="text-primary hover:text-primary/80">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="text-danger hover:text-danger/80 delete-booking" data-id="{{ $booking->id }}">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                <div class="flex flex-col items-center py-8">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                    <p class="text-lg font-medium">Tidak ada data booking</p>
                    <p class="text-sm text-gray-500">Silakan tambahkan booking baru</p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
