<?php

namespace App\Domains\Booking\Controllers\Public;

use App\Domains\Booking\Controllers\BaseBookingController;
use App\Domains\Booking\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BookingController extends BaseBookingController
{
    /**
     * Get the index route name for redirects
     *
     * @return string
     */
    protected function getIndexRouteName(): string
    {
        return 'public.bookings.index';
    }

    /**
     * Get the view path prefix
     *
     * @return string
     */
    protected function getViewPath(): string
    {
        return 'public.bookings';
    }

    /**
     * Display a listing of bookings (public view)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        // Public users can only see basic booking information
        $filters = $request->only(['date', 'meeting_room_id']);
        $bookings = $this->bookingService->getFilteredBookings($filters);
        
        return view('public.bookings.index', compact('bookings', 'filters'));
    }

    /**
     * Show the form for creating a new booking
     *
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $meetingRooms = $this->getMeetingRooms();
        return view('public.bookings.create', compact('meetingRooms'));
    }

    /**
     * Store a newly created booking (public submission)
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $this->validateBookingData($request);
        
        // Set default status for public bookings
        $validated['status'] = 'pending';
        $validated['booking_type'] = $validated['booking_type'] ?? 'regular';
        
        try {
            $booking = $this->bookingService->create($validated);
            
            $this->bookingService->logActivity(
                'create',
                'bookings',
                "Public user created booking request for {$booking->nama}",
                ['booking_id' => $booking->id, 'status' => 'pending']
            );
            
            return redirect()
                ->route('public.bookings.index')
                ->with('success', 'Booking request submitted successfully. Please wait for approval.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified booking (public view)
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show(int $id): \Illuminate\View\View
    {
        $booking = $this->findBookingOrFail($id);
        
        // Public users can only view basic information
        return view('public.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking (limited access)
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): \Illuminate\View\View
    {
        $booking = $this->findBookingOrFail($id);
        
        // Only allow editing if booking is still pending
        if ($booking->status !== 'pending') {
            return redirect()
                ->route('public.bookings.index')
                ->withErrors(['error' => 'Cannot edit approved or rejected bookings.']);
        }
        
        $meetingRooms = $this->getMeetingRooms();
        return view('public.bookings.edit', compact('booking', 'meetingRooms'));
    }

    /**
     * Update the specified booking (limited access)
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $booking = $this->findBookingOrFail($id);
        
        // Only allow updating if booking is still pending
        if ($booking->status !== 'pending') {
            return redirect()
                ->route('public.bookings.index')
                ->withErrors(['error' => 'Cannot update approved or rejected bookings.']);
        }
        
        $validated = $this->validateBookingData($request);
        
        try {
            $updatedBooking = $this->bookingService->update($booking, $validated);
            
            $this->bookingService->logActivity(
                'update',
                'bookings',
                "Public user updated booking request for {$updatedBooking->nama}",
                ['booking_id' => $updatedBooking->id]
            );
            
            return redirect()
                ->route('public.bookings.index')
                ->with('success', 'Booking request updated successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified booking (limited access)
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $booking = $this->findBookingOrFail($id);
        
        // Only allow deletion if booking is still pending
        if ($booking->status !== 'pending') {
            return redirect()
                ->route('public.bookings.index')
                ->withErrors(['error' => 'Cannot delete approved or rejected bookings.']);
        }
        
        try {
            $this->bookingService->delete($booking);
            
            $this->bookingService->logActivity(
                'delete',
                'bookings',
                "Public user cancelled booking request for {$booking->nama}",
                ['cancelled_booking_id' => $id]
            );
            
            return redirect()
                ->route('public.bookings.index')
                ->with('success', 'Booking request cancelled successfully.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get available time slots for a specific date and room (AJAX)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAvailableTimes(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'room_id' => 'required|exists:meeting_rooms,id'
        ]);
        
        try {
            $availableTimes = $this->bookingService->getAvailableTimes(
                $request->date,
                $request->room_id
            );
            
            return response()->json([
                'success' => true,
                'data' => $availableTimes
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check room availability for specific time slot (AJAX)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'room_id' => 'required|exists:meeting_rooms,id',
            'exclude_booking_id' => 'nullable|exists:bookings,id'
        ]);
        
        try {
            $isAvailable = $this->bookingService->isTimeSlotAvailable(
                $request->date,
                $request->start_time,
                $request->end_time,
                $request->room_id,
                $request->exclude_booking_id
            );
            
            return response()->json([
                'success' => true,
                'available' => $isAvailable,
                'message' => $isAvailable ? 'Time slot is available' : 'Time slot is not available'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get basic booking statistics for public dashboard
     *
     * @return JsonResponse
     */
    public function getPublicStats(): JsonResponse
    {
        try {
            $stats = $this->bookingService->getStatistics();
            
            // Filter to show only public-appropriate statistics
            $publicStats = [
                'total_rooms' => $this->getMeetingRooms()->count(),
                'today_bookings' => $stats['today_bookings'],
                'upcoming_bookings' => $stats['upcoming_bookings']->take(3) // Limit to 3 for public view
            ];
            
            return response()->json([
                'success' => true,
                'data' => $publicStats
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}