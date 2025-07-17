<?php

namespace App\Domains\Booking\Controllers;

use App\Http\Controllers\Controller;
use App\Domains\Booking\Services\BookingService;
use App\Models\MeetingRoom;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

abstract class BaseBookingController extends Controller
{
    protected BookingService $bookingService;
    
    public function __construct(BookingService $bookingService)
    {
        $this->bookingService = $bookingService;
    }
    
    /**
     * Display a listing of bookings
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $bookings = $this->bookingService->getFilteredBookings($request->all());
        $meetingRooms = MeetingRoom::all();
        $departments = Department::all();
        
        return view($this->getViewPath() . '.index', compact('bookings', 'meetingRooms', 'departments'));
    }
    
    /**
     * Show the form for creating a new booking
     *
     * @return View
     */
    public function create(): View
    {
        $meetingRooms = MeetingRoom::all();
        $departments = Department::all();
        
        return view($this->getViewPath() . '.create', compact('meetingRooms', 'departments'));
    }
    
    /**
     * Store a newly created booking
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $this->validateBookingData($request);
        
        try {
            $booking = $this->bookingService->create($validatedData);
            
            return redirect()->route($this->getIndexRouteName())
                ->with('success', 'Booking berhasil dibuat.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Show the form for editing the specified booking
     *
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        $booking = $this->findBookingOrFail($id);
        $meetingRooms = MeetingRoom::all();
        $departments = Department::all();
        $employees = \App\Models\Employee::with('department')->orderBy('name', 'asc')->get();
        
        return view($this->getViewPath() . '.edit', compact('booking', 'meetingRooms', 'departments', 'employees'));
    }
    
    /**
     * Update the specified booking
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $booking = $this->findBookingOrFail($id);
        $validatedData = $this->validateBookingData($request, $booking->id);
        
        try {
            $this->bookingService->update($booking, $validatedData);
            
            return redirect()->route($this->getIndexRouteName())
                ->with('success', 'Booking berhasil diupdate.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Remove the specified booking
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $booking = $this->findBookingOrFail($id);
        
        try {
            $this->bookingService->delete($booking);
            
            return redirect()->route($this->getIndexRouteName())
                ->with('success', 'Booking berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Get available times for a specific date and room
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAvailableTimes(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'exclude_booking_id' => 'nullable|exists:bookings,id'
        ]);
        
        try {
            $availableTimes = $this->bookingService->getAvailableTimes(
                $request->date,
                $request->meeting_room_id,
                $request->exclude_booking_id
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
     * Get booking statistics
     *
     * @return JsonResponse
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->bookingService->getStatistics();
            
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Export bookings data
     *
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        // This will be implemented based on specific role requirements
        // Each role-specific controller can override this method
        return $this->handleExport($request);
    }
    
    /**
     * Validate booking data
     *
     * @param Request $request
     * @param int|null $excludeId
     * @return array
     */
    protected function validateBookingData(Request $request, int $excludeId = null): array
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'description' => 'nullable|string',
            'booking_type' => 'required|in:internal,external',
            'external_description' => 'required_if:booking_type,external|nullable|string',
            'status' => 'nullable|in:pending,approved,rejected'
        ];
        
        return $request->validate($rules);
    }
    
    /**
     * Find booking or fail
     *
     * @param int $id
     * @return \App\Models\Booking
     */
    protected function findBookingOrFail(int $id)
    {
        return \App\Models\Booking::findOrFail($id);
    }
    
    /**
     * Handle export functionality - to be implemented by child classes
     *
     * @param Request $request
     * @return mixed
     */
    protected function handleExport(Request $request)
    {
        throw new \Exception('Export functionality not implemented for this role.');
    }
    
    // Abstract methods that must be implemented by child classes
    
    /**
     * Get the view path for this role
     *
     * @return string
     */
    abstract protected function getViewPath(): string;
    
    /**
     * Get the index route name for this role
     *
     * @return string
     */
    abstract protected function getIndexRouteName(): string;
    
    /**
     * Get additional data specific to the role
     * This can be overridden by child classes to add role-specific data
     *
     * @return array
     */
    protected function getRoleSpecificData(): array
    {
        return [];
    }
}