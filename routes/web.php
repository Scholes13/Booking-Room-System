<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityReportController;
use Illuminate\Support\Facades\Route;

// -------------------------------------------------------------------
//                      ROUTE UTAMA
// -------------------------------------------------------------------
Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');

// Route untuk user booking
Route::get('/', [BookingController::class, 'create'])->name('booking.create');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('available.times');

// Route untuk Activity Management (user side)
Route::get('/activity/create', [ActivityController::class, 'create'])->name('activity.create');
Route::post('/activity/store', [ActivityController::class, 'store'])->name('activity.store');
Route::get('/activity', [ActivityController::class, 'create'])->name('activity.index');
Route::get('/activity/calendar', [ActivityController::class, 'calendar'])->name('activity.calendar');
Route::get('/activity/calendar/events', [ActivityController::class, 'calendarEvents'])->name('activity.calendar.events');

// -------------------------------------------------------------------
//                   LOGIN ADMIN / LOGOUT
// -------------------------------------------------------------------
Route::get('/admin/login', [AdminController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

// -------------------------------------------------------------------
//                   ROUTE AREA ADMIN
// -------------------------------------------------------------------
Route::group(['prefix' => 'admin', 'middleware' => \App\Http\Middleware\AdminMiddleware::class], function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Meeting Rooms
    Route::prefix('meeting-rooms')->group(function () {
        Route::get('/', [AdminController::class, 'meetingRooms'])->name('admin.meeting_rooms');
        Route::get('/create', [AdminController::class, 'createMeetingRoom'])->name('admin.meeting_rooms.create');
        Route::post('/', [AdminController::class, 'storeMeetingRoom'])->name('admin.meeting_rooms.store');
        Route::get('/{id}/edit', [AdminController::class, 'editMeetingRoom'])->name('admin.meeting_rooms.edit');
        Route::put('/{id}', [AdminController::class, 'updateMeetingRoom'])->name('admin.meeting_rooms.update');
        Route::delete('/{id}', [AdminController::class, 'deleteMeetingRoom'])->name('admin.meeting_rooms.delete');
    });
    
    // Departments
    Route::prefix('departments')->group(function () {
        Route::get('/', [AdminController::class, 'departments'])->name('admin.departments');
        Route::post('/', [AdminController::class, 'storeDepartment'])->name('admin.departments.store');
        Route::get('/{id}/edit', [AdminController::class, 'editDepartment'])->name('admin.departments.edit');
        Route::put('/{id}', [AdminController::class, 'updateDepartment'])->name('admin.departments.update');
        Route::delete('/{id}', [AdminController::class, 'deleteDepartment'])->name('admin.departments.delete');
    });
    
    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('admin.bookings.index');
        Route::get('/export', [BookingController::class, 'export'])->name('admin.bookings.export');
        Route::get('/statistics', [BookingController::class, 'getStatistics'])->name('admin.bookings.statistics');
        Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('admin.bookings.available-times');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('admin.bookings.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('admin.bookings.update');
        Route::delete('/{id}', [AdminController::class, 'deleteBooking'])->name('admin.bookings.delete');
    });

    // Employees
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('admin.employees');
        Route::get('/create', [EmployeeController::class, 'create'])->name('admin.employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('admin.employees.store');
        Route::get('/export', [EmployeeController::class, 'export'])->name('admin.employees.export');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('admin.employees.edit');
        Route::put('/{id}', [EmployeeController::class, 'update'])->name('admin.employees.update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('admin.employees.delete');
    });

    // Reports (Lama)
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('admin.reports');
        Route::post('/data', [ReportController::class, 'getData'])->name('admin.reports.data');
        Route::post('/export', [ReportController::class, 'export'])->name('admin.reports.export');
        // ...
    });
});

// -------------------------------------------------------------------
//               R O U T E   A R E A   S U P E R A D M I N
// -------------------------------------------------------------------
Route::group(['prefix' => 'superadmin', 'middleware' => \App\Http\Middleware\AdminMiddleware::class], function () {
    Route::get('/dashboard', [AdminController::class, 'superAdminDashboard'])->name('superadmin.dashboard');
    Route::get('/create-admin', [AdminController::class, 'createAdmin'])->name('superadmin.createAdmin');
    Route::post('/create-admin', [AdminController::class, 'storeAdmin'])->name('superadmin.storeAdmin');
});

// -------------------------------------------------------------------
//     R O U T E   A R E A   A D M I N   A C T I V I T Y
// -------------------------------------------------------------------
// => /admin/activity
Route::group(['prefix' => 'admin/activity', 'middleware' => \App\Http\Middleware\AdminMiddleware::class], function () {
    // GET /admin/activity
    Route::get('/', [ActivityReportController::class, 'index'])->name('admin.activity.index');

    // POST /admin/activity/data
    Route::post('/data', [ActivityReportController::class, 'getData'])->name('admin.activity.data');
    
    // POST /admin/activity/detailed
    Route::post('/detailed', [ActivityReportController::class, 'getDetailedData'])->name('admin.activity.detailed');

    // POST /admin/activity/export (opsional)
    Route::post('/export', [ActivityReportController::class, 'export'])->name('admin.activity.export');
});
