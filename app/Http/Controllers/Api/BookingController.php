<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Employee;
use App\Models\Department;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    /**
     * Update booking via AJAX
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Validate input
            $validated = $request->validate([
                'nama' => 'required|exists:employees,name',
                'department' => 'required|exists:departments,name',
                'meeting_room_id' => 'required|exists:meeting_rooms,id',
                'date' => 'required|date',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'booking_type' => 'required|in:internal,external',
                'external_description' => 'nullable|required_if:booking_type,external',
                'description' => 'nullable|string'
            ]);

            // Get the booking
            $booking = Booking::findOrFail($id);

            // Check for time conflicts
            $existingBookings = Booking::where('meeting_room_id', $validated['meeting_room_id'])
                ->where('date', $validated['date'])
                ->where('id', '!=', $id)
                ->get();

            $newStartTime = Carbon::createFromFormat('H:i', $validated['start_time']);
            $newEndTime = Carbon::createFromFormat('H:i', $validated['end_time']);

            foreach ($existingBookings as $existingBooking) {
                $existingStartTime = Carbon::parse($existingBooking->start_time);
                $existingEndTime = Carbon::parse($existingBooking->end_time);

                if ($newStartTime < $existingEndTime && $newEndTime > $existingStartTime) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Waktu yang Anda pilih bertabrakan dengan jadwal yang sudah ada.',
                        'errors' => [
                            'time_conflict' => ['Waktu yang Anda pilih bertabrakan dengan jadwal yang sudah ada.']
                        ]
                    ], 422);
                }
            }

            // Store old data for logging
            $oldData = $booking->toArray();

            // Update the booking
            $booking->update([
                'nama' => $validated['nama'],
                'department' => $validated['department'],
                'meeting_room_id' => $validated['meeting_room_id'],
                'date' => $validated['date'],
                'start_time' => $newStartTime->format('H:i:s'),
                'end_time' => $newEndTime->format('H:i:s'),
                'description' => $validated['description'],
                'booking_type' => $validated['booking_type'],
                'external_description' => $validated['external_description'],
            ]);

            // Log the activity
            ActivityLogService::logUpdate(
                'bookings',
                "Memperbarui booking: {$booking->nama}",
                [
                    'old_data' => $oldData,
                    'new_data' => $booking->fresh()->toArray()
                ]
            );

            // Return updated booking with relationships
            $updatedBooking = Booking::with(['meetingRoom', 'user'])
                ->find($booking->id);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil diperbarui!',
                'data' => [
                    'booking' => $this->formatBookingForResponse($updatedBooking)
                ]
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete booking via AJAX
     */
    public function destroy($id): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($id);
            $bookingData = $booking->toArray();
            
            $booking->delete();

            // Log the activity
            ActivityLogService::logDelete(
                'bookings',
                "Menghapus booking: {$bookingData['nama']}",
                $bookingData
            );

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dihapus!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus booking: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get available times for a specific room and date
     */
    public function getAvailableTimes(Request $request): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'exclude_booking_id' => 'nullable|exists:bookings,id'
        ]);

        $query = Booking::where('meeting_room_id', $request->meeting_room_id)
            ->where('date', $request->date);

        // Exclude current booking if editing
        if ($request->exclude_booking_id) {
            $query->where('id', '!=', $request->exclude_booking_id);
        }

        $bookings = $query->get()->map(function($booking) {
            return [
                'id' => $booking->id,
                'start' => $booking->start_time,
                'end' => $booking->end_time,
                'nama' => $booking->nama
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }

    /**
     * Validate booking time slot
     */
    public function validateTimeSlot(Request $request): JsonResponse
    {
        $request->validate([
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'exclude_booking_id' => 'nullable|exists:bookings,id'
        ]);

        $query = Booking::where('meeting_room_id', $request->meeting_room_id)
            ->where('date', $request->date);

        if ($request->exclude_booking_id) {
            $query->where('id', '!=', $request->exclude_booking_id);
        }

        $existingBookings = $query->get();
        $newStartTime = Carbon::createFromFormat('H:i', $request->start_time);
        $newEndTime = Carbon::createFromFormat('H:i', $request->end_time);

        $conflicts = [];
        foreach ($existingBookings as $booking) {
            $existingStartTime = Carbon::parse($booking->start_time);
            $existingEndTime = Carbon::parse($booking->end_time);

            if ($newStartTime < $existingEndTime && $newEndTime > $existingStartTime) {
                $conflicts[] = [
                    'id' => $booking->id,
                    'nama' => $booking->nama,
                    'start_time' => $booking->start_time,
                    'end_time' => $booking->end_time
                ];
            }
        }

        return response()->json([
            'success' => empty($conflicts),
            'available' => empty($conflicts),
            'conflicts' => $conflicts,
            'message' => empty($conflicts) ? 'Time slot is available' : 'Time slot conflicts with existing bookings'
        ]);
    }

    /**
     * Format booking data for API response
     */
    private function formatBookingForResponse($booking): array
    {
        return [
            'id' => $booking->id,
            'nama' => $booking->nama,
            'department' => $booking->department,
            'date' => $booking->date,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'description' => $booking->description,
            'booking_type' => $booking->booking_type,
            'external_description' => $booking->external_description,
            'dynamic_status' => $booking->dynamic_status,
            'meeting_room' => $booking->meetingRoom ? [
                'id' => $booking->meetingRoom->id,
                'name' => $booking->meetingRoom->name
            ] : null,
            'user' => $booking->user ? [
                'id' => $booking->user->id,
                'name' => $booking->user->name,
                'department' => $booking->user->department ? [
                    'id' => $booking->user->department->id,
                    'name' => $booking->user->department->name
                ] : null
            ] : null,
            'created_at' => $booking->created_at,
            'updated_at' => $booking->updated_at
        ];
    }
}