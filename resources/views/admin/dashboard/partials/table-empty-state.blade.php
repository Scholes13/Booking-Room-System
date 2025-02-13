<tr>
    <td colspan="8" class="px-6 py-4 text-center text-gray-400">
        <div class="flex flex-col items-center justify-center py-8">
            @include('admin.dashboard.partials.icons.empty-state')
            <p class="text-lg font-medium mt-4">Belum ada booking</p>
            <p class="text-sm text-gray-500 mt-1">Silakan tambahkan booking baru</p>
            <a href="{{ route('admin.bookings.create') }}" 
               class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-300">
                Tambah Booking
            </a>
        </div>
    </td>
</tr>