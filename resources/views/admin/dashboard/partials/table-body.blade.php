<tbody class="divide-y divide-gray-800 bg-gray-900/30" id="tableBody">
    @forelse($bookings as $booking)
        <tr class="hover:bg-gray-50 booking-row" data-endtime="{{ $booking->date }} {{ $booking->end_time }}">
            <!-- Kolom Nama menggunakan kelas table-cell-name-wrap untuk membungkus teks -->
            <td class="px-6 py-4 text-sm text-gray-900 table-cell-name-wrap">
                {{ $booking->nama }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->department }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 booking-date">{{ $booking->date }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 booking-time">{{ $booking->start_time }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 booking-endtime">{{ $booking->end_time }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $booking->meetingRoom->name }}</td>
            <td class="px-6 py-4 text-sm text-gray-900">{{ $booking->description }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex gap-2">
                    <a href="{{ route('admin.bookings.edit', $booking->id) }}" class="text-blue-600 hover:text-blue-900">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('admin.bookings.delete', $booking->id) }}" method="POST" class="inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                <div class="flex flex-col items-center py-8">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                    <p class="text-lg font-medium">Tidak ada data booking</p>
                    <p class="text-sm text-gray-400">Silakan tambahkan booking baru</p>
                </div>
            </td>
        </tr>
    @endforelse
</tbody>
