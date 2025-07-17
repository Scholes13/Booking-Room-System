<?php

namespace App\Domains\Booking\Controllers\Admin;

use App\Domains\Booking\Controllers\BaseBookingController;
use App\Domains\Booking\Services\BookingService;
use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BookingController extends BaseBookingController
{
    private ReportExportService $exportService;
    
    public function __construct(BookingService $bookingService, ReportExportService $exportService)
    {
        parent::__construct($bookingService);
        $this->exportService = $exportService;
        
        // Apply admin middleware
        $this->middleware(['auth', 'role:admin,superadmin']);
    }
    
    /**
     * Get the view path for admin role
     *
     * @return string
     */
    protected function getViewPath(): string
    {
        return 'admin.bookings';
    }
    
    /**
     * Get the index route name for admin role
     *
     * @return string
     */
    protected function getIndexRouteName(): string
    {
        return 'admin.bookings.index';
    }
    
    /**
     * Handle export functionality for admin role
     *
     * @param Request $request
     * @return Response
     */
    protected function handleExport(Request $request): Response
    {
        $request->validate([
            'format' => 'required|in:excel,pdf',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'meeting_room_id' => 'nullable|exists:meeting_rooms,id',
            'department' => 'nullable|string',
            'booking_type' => 'nullable|in:internal,external'
        ]);
        
        try {
            // Get filtered bookings for export
            $bookings = $this->bookingService->getFilteredBookings($request->all());
            
            // Prepare export data
            $exportData = $bookings->getCollection()->map(function ($booking) {
                return [
                    'ID' => $booking->id,
                    'Nama' => $booking->nama,
                    'Department' => $booking->department,
                    'Ruang Meeting' => $booking->meetingRoom->name,
                    'Tanggal' => $booking->date,
                    'Jam Mulai' => $booking->start_time,
                    'Jam Selesai' => $booking->end_time,
                    'Tipe Booking' => ucfirst($booking->booking_type),
                    'Status' => ucfirst($booking->status ?? 'approved'),
                    'Deskripsi' => $booking->description,
                    'Dibuat' => $booking->created_at->format('Y-m-d H:i:s')
                ];
            })->toArray();
            
            $headers = [
                'ID', 'Nama', 'Department', 'Ruang Meeting', 'Tanggal', 
                'Jam Mulai', 'Jam Selesai', 'Tipe Booking', 'Status', 
                'Deskripsi', 'Dibuat'
            ];
            
            $filename = 'bookings_export_' . date('Y-m-d_H-i-s') . '.xlsx';
            
            // Log export activity
            $this->bookingService->logActivity(
                'export',
                'bookings',
                'Exported bookings data',
                [
                    'format' => $request->format,
                    'filters' => $request->except(['format']),
                    'total_records' => count($exportData)
                ]
            );
            
            return response()->streamDownload(function () use ($exportData, $headers, $filename) {
                $this->exportService->export($exportData, $headers, $filename);
            }, $filename);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Export gagal: ' . $e->getMessage());
        }
    }
    
    /**
     * Get additional data specific to admin role
     *
     * @return array
     */
    protected function getRoleSpecificData(): array
    {
        return [
            'can_approve' => true,
            'can_export' => true,
            'can_delete' => true,
            'show_all_bookings' => true
        ];
    }
    
    /**
     * Approve booking (admin specific functionality)
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(int $id)
    {
        try {
            $booking = $this->findBookingOrFail($id);
            $this->bookingService->update($booking, ['status' => 'approved']);
            
            return redirect()->back()
                ->with('success', 'Booking berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Reject booking (admin specific functionality)
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(int $id)
    {
        try {
            $booking = $this->findBookingOrFail($id);
            $this->bookingService->update($booking, ['status' => 'rejected']);
            
            return redirect()->back()
                ->with('success', 'Booking berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
    
    /**
     * Get bookings data for dashboard (admin specific)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBookingsForDashboard(Request $request)
    {
        try {
            $filters = [
                'start_date' => $request->start_date ?? now()->startOfMonth()->format('Y-m-d'),
                'end_date' => $request->end_date ?? now()->endOfMonth()->format('Y-m-d'),
                'per_page' => 10
            ];
            
            $bookings = $this->bookingService->getFilteredBookings($filters);
            $statistics = $this->bookingService->getStatistics();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'bookings' => $bookings,
                    'statistics' => $statistics
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}