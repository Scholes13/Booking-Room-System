<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// Halaman kalender
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

// Route untuk user booking
Route::get('/', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('available.times');

// Route untuk admin: login & logout
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// Route untuk area admin (dengan AdminMiddleware)
Route::group(['prefix' => 'admin', 'middleware' => \App\Http\Middleware\AdminMiddleware::class], function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Meeting Rooms Management
    Route::prefix('meeting-rooms')->group(function () {
        Route::get('/', [AdminController::class, 'meetingRooms'])->name('admin.meeting_rooms');
        Route::post('/', [AdminController::class, 'storeMeetingRoom'])->name('admin.meeting_rooms.store');
        Route::delete('/{id}', [AdminController::class, 'deleteMeetingRoom'])->name('admin.meeting_rooms.delete');
    });
    
    // Departments Management
    Route::prefix('departments')->group(function () {
        Route::get('/', [AdminController::class, 'departments'])->name('admin.departments');
        Route::post('/', [AdminController::class, 'storeDepartment'])->name('admin.departments.store');
        Route::get('/{id}/edit', [AdminController::class, 'editDepartment'])->name('admin.departments.edit');
        Route::put('/{id}', [AdminController::class, 'updateDepartment'])->name('admin.departments.update');
        Route::delete('/{id}', [AdminController::class, 'deleteDepartment'])->name('admin.departments.delete');
    });
    
    // Bookings Management
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('admin.bookings.index');
        Route::get('/export', [BookingController::class, 'export'])->name('admin.bookings.export');
        Route::get('/statistics', [BookingController::class, 'getStatistics'])->name('admin.bookings.statistics');
        Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('admin.bookings.available-times');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('admin.bookings.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('admin.bookings.update');
        Route::delete('/{id}', [BookingController::class, 'delete'])->name('admin.bookings.delete');
    });
    
    // Employees Management
    // Di dalam group admin middleware
Route::prefix('employees')->group(function () {
    Route::get('/', [EmployeeController::class, 'index'])->name('admin.employees');
    Route::get('/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
    Route::post('/', [EmployeeController::class, 'store'])->name('admin.employees.store');
    Route::get('/export', [EmployeeController::class, 'export'])->name('admin.employees.export');
    Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
    Route::put('/{id}', [EmployeeController::class, 'update'])->name('admin.employees.update');
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('admin.employees.delete');

    });

    // Reports Management
    Route::prefix('reports')->group(function () {
        // Basic Report Routes
        Route::get('/', [ReportController::class, 'index'])->name('admin.reports');
        Route::post('/data', [ReportController::class, 'getData'])->name('admin.reports.data');
        Route::post('/export', [ReportController::class, 'export'])->name('admin.reports.export');
        
        // Room Usage Reports
        Route::prefix('rooms')->group(function () {
            Route::get('/usage', [ReportController::class, 'getRoomsUsage'])->name('admin.reports.rooms-usage');
            Route::get('/availability', [ReportController::class, 'getRoomsAvailability'])->name('admin.reports.rooms-availability');
            Route::get('/statistics', [ReportController::class, 'getRoomsStatistics'])->name('admin.reports.rooms-statistics');
        });
        
        // Department Reports
        Route::prefix('departments')->group(function () {
            Route::get('/usage', [ReportController::class, 'getDepartmentsUsage'])->name('admin.reports.departments-usage');
            Route::get('/statistics', [ReportController::class, 'getDepartmentsStatistics'])->name('admin.reports.departments-statistics');
        });
        
        // Booking Reports
        Route::prefix('bookings')->group(function () {
            Route::get('/summary', [ReportController::class, 'getBookingsSummary'])->name('admin.reports.bookings-summary');
            Route::get('/trends', [ReportController::class, 'getBookingsTrends'])->name('admin.reports.bookings-trends');
            Route::get('/detailed', [ReportController::class, 'getDetailedBookings'])->name('admin.reports.bookings-detailed');
        });

        // Time-based Reports
        Route::prefix('time')->group(function () {
            Route::get('/daily', [ReportController::class, 'getDailyReport'])->name('admin.reports.daily');
            Route::get('/weekly', [ReportController::class, 'getWeeklyReport'])->name('admin.reports.weekly');
            Route::get('/monthly', [ReportController::class, 'getMonthlyReport'])->name('admin.reports.monthly');
        });

        // Export Routes
        Route::prefix('export')->group(function () {
            Route::post('/rooms', [ReportController::class, 'exportRoomsReport'])->name('admin.reports.export.rooms');
            Route::post('/departments', [ReportController::class, 'exportDepartmentsReport'])->name('admin.reports.export.departments');
            Route::post('/bookings', [ReportController::class, 'exportBookingsReport'])->name('admin.reports.export.bookings');
        });
    });
});