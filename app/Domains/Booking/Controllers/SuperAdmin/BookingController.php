<?php

namespace App\Domains\Booking\Controllers\SuperAdmin;

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
        return 'superadmin.bookings.index';
    }

    /**
     * Get the view path prefix
     *
     * @return string
     */
    protected function getViewPath(): string
    {
        return 'superadmin.bookings';
    }

    /**
     * Display a listing of all bookings with advanced filters
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $filters = $request->only([
            'start_date', 'end_date', 'meeting_room_id', 
            'department', 'booking_type', 'status', 'search', 'per_page'
        ]);
        
        $bookings = $this->bookingService->getFilteredBookings($filters);
        $statistics = $this->bookingService->getStatistics();
        
        return view('superadmin.bookings.index', compact('bookings', 'statistics', 'filters'));
    }

    /**
     * Show the form for creating a new booking
     *
     * @return \Illuminate\View\View
     */
    public function create(): \Illuminate\View\View
    {
        $meetingRooms = $this->getMeetingRooms();
        return view('superadmin.bookings.create', compact('meetingRooms'));
    }

    /**
     * Store a newly created booking
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $this->validateBookingData($request);
        
        try {
            $booking = $this->bookingService->create($validated);
            
            $this->bookingService->logActivity(
                'create',
                'bookings',
                "SuperAdmin created booking for {$booking->nama}",
                ['booking_id' => $booking->id, 'created_by' => 'superadmin']
            );
            
            return redirect()
                ->route('superadmin.bookings.index')
                ->with('success', 'Booking created successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified booking
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $booking = $this->findBookingOrFail($id);
        return view('superadmin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id): \Illuminate\View\View
    {
        $booking = $this->findBookingOrFail($id);
        $meetingRooms = $this->getMeetingRooms();
        
        return view('superadmin.bookings.edit', compact('booking', 'meetingRooms'));
    }

    /**
     * Update the specified booking
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $booking = $this->findBookingOrFail($id);
        $validated = $this->validateBookingData($request);
        
        try {
            $updatedBooking = $this->bookingService->update($booking, $validated);
            
            $this->bookingService->logActivity(
                'update',
                'bookings',
                "SuperAdmin updated booking for {$updatedBooking->nama}",
                ['booking_id' => $updatedBooking->id, 'updated_by' => 'superadmin']
            );
            
            return redirect()
                ->route('superadmin.bookings.index')
                ->with('success', 'Booking updated successfully.');
                
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified booking
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id): \Illuminate\Http\RedirectResponse
    {
        $booking = $this->findBookingOrFail($id);
        
        try {
            $this->bookingService->delete($booking);
            
            $this->bookingService->logActivity(
                'delete',
                'bookings',
                "SuperAdmin deleted booking for {$booking->nama}",
                ['deleted_booking_id' => $id, 'deleted_by' => 'superadmin']
            );
            
            return redirect()
                ->route('superadmin.bookings.index')
                ->with('success', 'Booking deleted successfully.');
                
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Export bookings to Excel (SuperAdmin exclusive)
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $filters = $request->only([
            'start_date', 'end_date', 'meeting_room_id', 
            'department', 'booking_type', 'status'
        ]);
        
        $this->bookingService->logActivity(
            'export',
            'bookings',
            'SuperAdmin exported booking data',
            ['filters' => $filters, 'exported_by' => 'superadmin']
        );
        
        // Implementation would depend on your Excel export library
        // For example, using Laravel Excel:
        // return Excel::download(new BookingsExport($filters), 'bookings.xlsx');
        
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Bulk approve bookings (SuperAdmin exclusive)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkApprove(Request $request): JsonResponse
    {
        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id'
        ]);
        
        try {
            $approvedCount = 0;
            foreach ($request->booking_ids as $bookingId) {
                $booking = $this->findBookingOrFail($bookingId);
                $this->bookingService->update($booking, ['status' => 'approved']);
                $approvedCount++;
            }
            
            $this->bookingService->logActivity(
                'bulk_approve',
                'bookings',
                "SuperAdmin bulk approved {$approvedCount} bookings",
                ['booking_ids' => $request->booking_ids, 'approved_by' => 'superadmin']
            );
            
            return response()->json([
                'success' => true,
                'message' => "{$approvedCount} bookings approved successfully."
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive booking analytics (SuperAdmin exclusive)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $analytics = $this->bookingService->getStatistics();
            
            // Add SuperAdmin-specific analytics
            $analytics['system_health'] = [
                'total_rooms' => $this->getMeetingRooms()->count(),
                'active_bookings' => $analytics['today_bookings'],
                'utilization_rate' => $this->calculateUtilizationRate($analytics)
            ];
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate system utilization rate
     *
     * @param array $statistics
     * @return float
     */
    private function calculateUtilizationRate(array $statistics): float
    {
        $totalRooms = $this->getMeetingRooms()->count();
        $totalBookings = $statistics['today_bookings'];
        
        if ($totalRooms === 0) {
            return 0;
        }
        
        // Simplified calculation - can be enhanced
        $maxPossibleBookings = $totalRooms * 8; // 8 hours per day
        return min(100, ($totalBookings / $maxPossibleBookings) * 100);
    }
}