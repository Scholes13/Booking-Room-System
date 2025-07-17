<?php

namespace App\Domains\Booking\Services;

use App\Shared\Services\BaseService;
use App\Shared\Enums\UserRole;
use App\Models\Booking;
use App\Models\MeetingRoom;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingService extends BaseService
{
    /**
     * Create a new booking
     *
     * @param array $data
     * @return Booking
     */
    public function create(array $data): Booking
    {
        $this->validatePermission(UserRole::getBookingManagerRoles());
        
        // Validate time availability
        if (!$this->isTimeSlotAvailable($data['date'], $data['start_time'], $data['end_time'], $data['meeting_room_id'])) {
            throw new \Exception('Time slot is not available for the selected room.');
        }
        
        $booking = Booking::create($data);
        
        $this->logActivity(
            'create', 
            'bookings', 
            "Created booking for {$booking->nama} in room {$booking->meetingRoom->name}",
            ['booking_id' => $booking->id]
        );
        
        return $booking;
    }
    
    /**
     * Update existing booking
     *
     * @param Booking $booking
     * @param array $data
     * @return Booking
     */
    public function update(Booking $booking, array $data): Booking
    {
        $this->validatePermission(UserRole::getBookingManagerRoles());
        
        // Validate time availability (excluding current booking)
        if (isset($data['date'], $data['start_time'], $data['end_time'], $data['meeting_room_id'])) {
            if (!$this->isTimeSlotAvailable(
                $data['date'], 
                $data['start_time'], 
                $data['end_time'], 
                $data['meeting_room_id'], 
                $booking->id
            )) {
                throw new \Exception('Time slot is not available for the selected room.');
            }
        }
        
        $oldData = $booking->toArray();
        $booking->update($data);
        
        $this->logActivity(
            'update', 
            'bookings', 
            "Updated booking for {$booking->nama}",
            ['booking_id' => $booking->id, 'old_data' => $oldData, 'new_data' => $data]
        );
        
        return $booking;
    }
    
    /**
     * Delete booking
     *
     * @param Booking $booking
     * @return bool
     */
    public function delete(Booking $booking): bool
    {
        $this->validatePermission(UserRole::getBookingManagerRoles());
        
        $bookingData = $booking->toArray();
        $result = $booking->delete();
        
        if ($result) {
            $this->logActivity(
                'delete', 
                'bookings', 
                "Deleted booking for {$bookingData['nama']}",
                ['deleted_booking' => $bookingData]
            );
        }
        
        return $result;
    }
    
    /**
     * Get filtered bookings based on request parameters
     *
     * @param array $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getFilteredBookings(array $filters = [])
    {
        $query = Booking::with(['meetingRoom', 'user']);
        
        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->where('date', '>=', $filters['start_date']);
        }
        
        if (!empty($filters['end_date'])) {
            $query->where('date', '<=', $filters['end_date']);
        }
        
        // Filter by meeting room
        if (!empty($filters['meeting_room_id'])) {
            $query->where('meeting_room_id', $filters['meeting_room_id']);
        }
        
        // Filter by department
        if (!empty($filters['department'])) {
            $query->where('department', 'like', '%' . $filters['department'] . '%');
        }
        
        // Filter by booking type
        if (!empty($filters['booking_type'])) {
            $query->where('booking_type', $filters['booking_type']);
        }
        
        // Filter by status
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        // Search by name
        if (!empty($filters['search'])) {
            $query->where('nama', 'like', '%' . $filters['search'] . '%');
        }
        
        return $query->orderBy('date', 'desc')
                    ->orderBy('start_time', 'desc')
                    ->paginate($filters['per_page'] ?? 15);
    }
    
    /**
     * Get available time slots for a specific date and room
     *
     * @param string $date
     * @param int $roomId
     * @param int|null $excludeBookingId
     * @return array
     */
    public function getAvailableTimes(string $date, int $roomId, int $excludeBookingId = null): array
    {
        $bookedTimes = Booking::where('date', $date)
            ->where('meeting_room_id', $roomId)
            ->when($excludeBookingId, function ($query, $excludeBookingId) {
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->get(['start_time', 'end_time']);
        
        // Define working hours (can be moved to config)
        $workingHours = [
            'start' => '08:00',
            'end' => '17:00'
        ];
        
        $availableSlots = [];
        $currentTime = Carbon::createFromFormat('H:i', $workingHours['start']);
        $endTime = Carbon::createFromFormat('H:i', $workingHours['end']);
        
        while ($currentTime->lt($endTime)) {
            $slotStart = $currentTime->format('H:i');
            $slotEnd = $currentTime->copy()->addHour()->format('H:i');
            
            $isAvailable = true;
            foreach ($bookedTimes as $booking) {
                if ($this->timeSlotsOverlap($slotStart, $slotEnd, $booking->start_time, $booking->end_time)) {
                    $isAvailable = false;
                    break;
                }
            }
            
            if ($isAvailable) {
                $availableSlots[] = [
                    'start' => $slotStart,
                    'end' => $slotEnd,
                    'label' => $slotStart . ' - ' . $slotEnd
                ];
            }
            
            $currentTime->addHour();
        }
        
        return $availableSlots;
    }
    
    /**
     * Get booking statistics
     *
     * @return array
     */
    public function getStatistics(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();
        
        return [
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::whereDate('date', $today)->count(),
            'this_month_bookings' => Booking::where('date', '>=', $thisMonth)->count(),
            'room_usage' => $this->calculateRoomUsage(),
            'most_used_room' => $this->getMostUsedRoom(),
            'booking_by_type' => $this->getBookingsByType(),
            'upcoming_bookings' => $this->getUpcomingBookings()
        ];
    }
    
    /**
     * Check if time slot is available
     *
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int $roomId
     * @param int|null $excludeBookingId
     * @return bool
     */
    public function isTimeSlotAvailable(string $date, string $startTime, string $endTime, int $roomId, int $excludeBookingId = null): bool
    {
        $conflictingBookings = Booking::where('date', $date)
            ->where('meeting_room_id', $roomId)
            ->when($excludeBookingId, function ($query, $excludeBookingId) {
                return $query->where('id', '!=', $excludeBookingId);
            })
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    // New booking starts during existing booking
                    $q->where('start_time', '<=', $startTime)
                      ->where('end_time', '>', $startTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // New booking ends during existing booking
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>=', $endTime);
                })->orWhere(function ($q) use ($startTime, $endTime) {
                    // New booking completely contains existing booking
                    $q->where('start_time', '>=', $startTime)
                      ->where('end_time', '<=', $endTime);
                });
            })
            ->exists();
        
        return !$conflictingBookings;
    }
    
    /**
     * Check if two time slots overlap
     *
     * @param string $start1
     * @param string $end1
     * @param string $start2
     * @param string $end2
     * @return bool
     */
    private function timeSlotsOverlap(string $start1, string $end1, string $start2, string $end2): bool
    {
        return ($start1 < $end2) && ($end1 > $start2);
    }
    
    /**
     * Calculate room usage statistics
     *
     * @return array
     */
    private function calculateRoomUsage(): array
    {
        return MeetingRoom::withCount(['bookings' => function ($query) {
            $query->where('date', '>=', Carbon::now()->startOfMonth());
        }])->get()->map(function ($room) {
            return [
                'room_name' => $room->name,
                'booking_count' => $room->bookings_count,
                'usage_percentage' => $this->calculateUsagePercentage($room->bookings_count)
            ];
        })->toArray();
    }
    
    /**
     * Get most used room
     *
     * @return array|null
     */
    private function getMostUsedRoom(): ?array
    {
        $room = MeetingRoom::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->first();
        
        if (!$room) {
            return null;
        }
        
        return [
            'name' => $room->name,
            'booking_count' => $room->bookings_count
        ];
    }
    
    /**
     * Get bookings grouped by type
     *
     * @return array
     */
    private function getBookingsByType(): array
    {
        return Booking::selectRaw('booking_type, COUNT(*) as count')
            ->groupBy('booking_type')
            ->pluck('count', 'booking_type')
            ->toArray();
    }
    
    /**
     * Get upcoming bookings
     *
     * @param int $limit
     * @return Collection
     */
    private function getUpcomingBookings(int $limit = 5): Collection
    {
        return Booking::with('meetingRoom')
            ->where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit($limit)
            ->get();
    }
    
    /**
     * Calculate usage percentage (simplified)
     *
     * @param int $bookingCount
     * @return float
     */
    private function calculateUsagePercentage(int $bookingCount): float
    {
        // Simplified calculation - can be improved based on actual working hours
        $maxPossibleBookings = 30; // Assuming 30 possible bookings per month
        return min(100, ($bookingCount / $maxPossibleBookings) * 100);
    }
    
    /**
     * Public method to log activity (accessible from controllers)
     *
     * @param string $action
     * @param string $module
     * @param string $description
     * @param array|null $properties
     * @return mixed
     */
    public function logActivity($action, $module, $description, $properties = null)
    {
        return parent::logActivity($action, $module, $description, $properties);
    }
}