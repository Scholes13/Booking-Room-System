<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Booking;

Route::get('/bookings', function (Request $request) {
    $bookings = Booking::with('meetingRoom')->get();
    $events = $bookings->map(function($booking) {
        return [
            'title' => $booking->start_time . ' - ' . $booking->end_time,
            'start' => $booking->date . 'T' . $booking->start_time,
            'end'   => $booking->date . 'T' . $booking->end_time,
        ];
    });
    return response()->json($events);
});
