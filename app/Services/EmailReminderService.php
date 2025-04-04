<?php

namespace App\Services;

use App\Mail\BookingReminderMail;
use App\Mail\ActivityReminderMail;
use App\Models\Booking;
use App\Models\Activity;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailReminderService
{
    /**
     * Kirim email reminder untuk booking yang akan datang dalam 1 jam
     */
    public function sendBookingReminders()
    {
        $oneHourFromNow = now()->addHour();
        
        // Cari semua booking yang akan dimulai dalam 1 jam
        $bookings = Booking::with('meetingRoom')
            ->whereDate('date', now()->toDateString())
            ->whereRaw('TIME_TO_SEC(TIMEDIFF(start_time, ?)) BETWEEN 3540 AND 3660', [now()->format('H:i:s')])
            ->get();
        
        $sentCount = 0;
        
        foreach ($bookings as $booking) {
            // Cari karyawan berdasarkan nama di booking
            $employee = Employee::where('name', $booking->nama)->first();
            
            // Jika tidak menemukan karyawan atau tidak memiliki email, lanjutkan ke booking berikutnya
            if (!$employee || !$employee->email) {
                Log::info("Tidak dapat mengirim reminder untuk booking ID {$booking->id}: Email karyawan tidak ditemukan");
                continue;
            }
            
            try {
                Mail::to($employee->email)->send(new BookingReminderMail($booking));
                $sentCount++;
                
                Log::info("Berhasil mengirim reminder booking ke {$employee->email} untuk booking ID {$booking->id}");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim reminder booking: " . $e->getMessage());
            }
        }
        
        return $sentCount;
    }
    
    /**
     * Kirim email reminder untuk aktivitas yang akan datang dalam 1 jam
     */
    public function sendActivityReminders()
    {
        $oneHourFromNow = now()->addHour();
        
        // Cari semua aktivitas yang akan dimulai dalam 1 jam
        $activities = Activity::with('department')
            ->whereDate('activity_date', now()->toDateString())
            ->whereRaw('TIME_TO_SEC(TIMEDIFF(start_time, ?)) BETWEEN 3540 AND 3660', [now()->format('H:i:s')])
            ->get();
        
        $sentCount = 0;
        
        foreach ($activities as $activity) {
            // Cari karyawan berdasarkan name di activity
            $employee = Employee::where('name', $activity->name)->first();
            
            // Jika tidak menemukan karyawan atau tidak memiliki email, lanjutkan ke aktivitas berikutnya
            if (!$employee || !$employee->email) {
                Log::info("Tidak dapat mengirim reminder untuk aktivitas ID {$activity->id}: Email karyawan tidak ditemukan");
                continue;
            }
            
            try {
                Mail::to($employee->email)->send(new ActivityReminderMail($activity));
                $sentCount++;
                
                Log::info("Berhasil mengirim reminder aktivitas ke {$employee->email} untuk aktivitas ID {$activity->id}");
            } catch (\Exception $e) {
                Log::error("Gagal mengirim reminder aktivitas: " . $e->getMessage());
            }
        }
        
        return $sentCount;
    }
} 