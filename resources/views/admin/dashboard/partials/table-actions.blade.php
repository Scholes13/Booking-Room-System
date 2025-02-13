<div class="flex gap-2">
    <a href="{{ route('admin.bookings.edit', $booking->id) }}" 
       class="flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg shadow hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:scale-105"
       aria-label="Edit booking">
        @include('admin.dashboard.partials.icons.edit')
        <span>Edit</span>
    </a>

    <form action="{{ route('admin.bookings.delete', $booking->id) }}" 
          method="POST" 
          class="delete-form"
          data-booking-id="{{ $booking->id }}">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="flex items-center gap-1 px-3 py-1.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg shadow hover:from-red-700 hover:to-red-800 transition-all duration-300 transform hover:scale-105"
                aria-label="Hapus booking">
            @include('admin.dashboard.partials.icons.delete')
            <span>Hapus</span>
        </button>
    </form>
</div>