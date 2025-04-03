<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\MeetingRoom;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        // Get unique departments from bookings
        $departments = Booking::select('department')->distinct()->pluck('department');
        
        // Get all room names
        $rooms = MeetingRoom::pluck('name');
        
        return view('public.calendar.index', compact('departments', 'rooms'));
    }

    public function events(Request $request)
    {
        try {
            // Parse tanggal dari request
            $start = $request->query('start') 
                ? Carbon::parse($request->query('start')) 
                : Carbon::now()->startOfMonth();
            $end = $request->query('end') 
                ? Carbon::parse($request->query('end')) 
                : Carbon::now()->endOfMonth();

            // Query dasar untuk booking
            $query = Booking::with('meetingRoom')
                ->whereBetween('date', [
                    $start->format('Y-m-d'),
                    $end->format('Y-m-d')
                ]);

            // Filter berdasarkan department jika ada
            if ($request->has('department') && $request->department !== '') {
                $query->where('department', $request->department);
            }

            // Filter berdasarkan ruangan jika ada
            if ($request->has('room') && $request->room !== '') {
                $query->whereHas('meetingRoom', function ($q) use ($request) {
                    $q->where('name', $request->room);
                });
            }

            $bookings = $query->get();

            $events = $bookings->map(function($booking) {
                // Pastikan format tanggal dan waktu valid
                $startDateTime = Carbon::parse($booking->date . ' ' . $booking->start_time);
                $endDateTime = Carbon::parse($booking->date . ' ' . $booking->end_time);

                return [
                    'id' => $booking->id,
                    'title' => $booking->nama ?? 'Untitled Booking',
                    'start' => $startDateTime->toIso8601String(),
                    'end' => $endDateTime->toIso8601String(),
                    'extendedProps' => [
                        'room_name'   => $booking->meetingRoom ? $booking->meetingRoom->name : 'Undefined Room',
                        'description' => $booking->description,
                        'created_by'  => $booking->nama,
                        'department'  => $booking->department,
                        'booking_type' => $booking->booking_type,
                        'external_description' => $booking->external_description
                    ]
                ];
            });

            return response()->json($events);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => 'Failed to load calendar events',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
