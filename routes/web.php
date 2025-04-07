<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AdminBASController;
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
Route::group(['prefix' => 'superadmin', 'middleware' => \App\Http\Middleware\SuperAdminMiddleware::class], function() {
    Route::get('/dashboard', [AdminController::class, 'superAdminDashboard'])->name('superadmin.dashboard');

    // User Management Routes
    Route::get('/users', [AdminController::class, 'users'])->name('superadmin.users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('superadmin.users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('superadmin.users.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('superadmin.users.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('superadmin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('superadmin.users.delete');
    
    // Activity Logs Routes
    Route::prefix('logs')->group(function () {
        Route::get('/', [ActivityLogController::class, 'index'])->name('superadmin.logs.index');
        Route::get('/export', [ActivityLogController::class, 'export'])->name('superadmin.logs.export');
        Route::get('/{id}', [ActivityLogController::class, 'show'])->name('superadmin.logs.show');
    });
    
    // Activities Management Routes - NEW
    Route::prefix('activities')->group(function () {
        Route::get('/', [ActivityController::class, 'index'])->name('superadmin.activities.index');
        Route::get('/create', [ActivityController::class, 'createAdmin'])->name('superadmin.activities.create');
        Route::post('/', [ActivityController::class, 'storeAdmin'])->name('superadmin.activities.store');
        Route::get('/{id}/edit', [ActivityController::class, 'edit'])->name('superadmin.activities.edit');
        Route::put('/{id}', [ActivityController::class, 'update'])->name('superadmin.activities.update');
        Route::delete('/{id}', [ActivityController::class, 'destroy'])->name('superadmin.activities.delete');
        Route::get('/calendar', [ActivityController::class, 'adminCalendar'])->name('superadmin.activities.calendar');
        Route::get('/calendar/events', [ActivityController::class, 'adminCalendarEvents'])->name('superadmin.activities.calendar.events');
    });
    
    // Meeting Rooms Routes - mirip dengan admin routes tapi dengan prefix superadmin
    Route::prefix('meeting-rooms')->group(function () {
        Route::get('/', [AdminController::class, 'meetingRooms'])->name('superadmin.meeting_rooms');
        Route::get('/create', [AdminController::class, 'createMeetingRoom'])->name('superadmin.meeting_rooms.create');
        Route::post('/', [AdminController::class, 'storeMeetingRoom'])->name('superadmin.meeting_rooms.store');
        Route::get('/{id}/edit', [AdminController::class, 'editMeetingRoom'])->name('superadmin.meeting_rooms.edit');
        Route::put('/{id}', [AdminController::class, 'updateMeetingRoom'])->name('superadmin.meeting_rooms.update');
        Route::delete('/{id}', [AdminController::class, 'deleteMeetingRoom'])->name('superadmin.meeting_rooms.delete');
    });
    
    // Departments Routes
    Route::prefix('departments')->group(function () {
        Route::get('/', [AdminController::class, 'departments'])->name('superadmin.departments');
        Route::post('/', [AdminController::class, 'storeDepartment'])->name('superadmin.departments.store');
        Route::get('/{id}/edit', [AdminController::class, 'editDepartment'])->name('superadmin.departments.edit');
        Route::put('/{id}', [AdminController::class, 'updateDepartment'])->name('superadmin.departments.update');
        Route::delete('/{id}', [AdminController::class, 'deleteDepartment'])->name('superadmin.departments.delete');
    });
    
    // Bookings Routes
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('superadmin.bookings.index');
        Route::get('/export', [BookingController::class, 'export'])->name('superadmin.bookings.export');
        Route::get('/statistics', [BookingController::class, 'getStatistics'])->name('superadmin.bookings.statistics');
        Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('superadmin.bookings.available-times');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('superadmin.bookings.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('superadmin.bookings.update');
        Route::delete('/{id}', [AdminController::class, 'deleteBooking'])->name('superadmin.bookings.delete');
    });

    // Employees Routes
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('superadmin.employees');
        Route::get('/create', [EmployeeController::class, 'create'])->name('superadmin.employees.create');
        Route::post('/', [EmployeeController::class, 'store'])->name('superadmin.employees.store');
        Route::get('/export', [EmployeeController::class, 'export'])->name('superadmin.employees.export');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('superadmin.employees.edit');
        Route::put('/{id}', [EmployeeController::class, 'update'])->name('superadmin.employees.update');
        Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name('superadmin.employees.delete');
    });

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('superadmin.reports');
        Route::post('/data', [ReportController::class, 'getData'])->name('superadmin.reports.data');
        Route::post('/export', [ReportController::class, 'export'])->name('superadmin.reports.export');
    });
    
    // Activity Reports
    Route::prefix('activity')->group(function () {
        Route::get('/', [ActivityReportController::class, 'index'])->name('superadmin.activity.index');
        Route::post('/data', [ActivityReportController::class, 'getData'])->name('superadmin.activity.data');
        Route::post('/detailed', [ActivityReportController::class, 'getDetailedData'])->name('superadmin.activity.detailed');
        Route::post('/export', [ActivityReportController::class, 'export'])->name('superadmin.activity.export');
    });
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

// -------------------------------------------------------------------
//                   ROUTE AREA ADMIN BAS
// -------------------------------------------------------------------
Route::group(['prefix' => 'bas', 'middleware' => \App\Http\Middleware\AdminBASMiddleware::class], function() {
    Route::get('/dashboard', [AdminBASController::class, 'dashboard'])->name('bas.dashboard');
    
    // Activity Reports Routes for BAS
    Route::prefix('activity')->group(function () {
        Route::get('/', [ActivityReportController::class, 'index'])->name('bas.activity.index');
        Route::post('/data', [ActivityReportController::class, 'getData'])->name('bas.activity.data');
        Route::post('/detailed', [ActivityReportController::class, 'getDetailedData'])->name('bas.activity.detailed');
        Route::post('/export', [ActivityReportController::class, 'export'])->name('bas.activity.export');
    });

    // Activities Management Routes
    Route::prefix('activities')->group(function () {
        Route::get('/', [AdminBASController::class, 'activitiesIndex'])->name('bas.activities.index');
        Route::get('/create', [AdminBASController::class, 'createActivity'])->name('bas.activities.create');
        Route::post('/', [AdminBASController::class, 'storeActivity'])->name('bas.activities.store');
        Route::get('/{activity}/edit', [AdminBASController::class, 'editActivity'])->name('bas.activities.edit');
        Route::put('/{activity}', [AdminBASController::class, 'updateActivity'])->name('bas.activities.update');
        Route::delete('/{activity}', [AdminBASController::class, 'destroyActivity'])->name('bas.activities.destroy');
        Route::get('/calendar', [AdminBASController::class, 'activitiesCalendar'])->name('bas.activities.calendar');
        Route::get('/json', [AdminBASController::class, 'activitiesJson'])->name('bas.activities.json');
    });
    
    // Meeting Rooms Routes
    Route::prefix('meeting-rooms')->group(function () {
        Route::get('/', [AdminBASController::class, 'meetingRooms'])->name('bas.meeting_rooms');
        Route::get('/create', [AdminController::class, 'createMeetingRoom'])->name('bas.meeting_rooms.create');
        Route::post('/', [AdminController::class, 'storeMeetingRoom'])->name('bas.meeting_rooms.store');
        Route::get('/{id}/edit', [AdminController::class, 'editMeetingRoom'])->name('bas.meeting_rooms.edit');
        Route::put('/{id}', [AdminController::class, 'updateMeetingRoom'])->name('bas.meeting_rooms.update');
        Route::delete('/{id}', [AdminController::class, 'deleteMeetingRoom'])->name('bas.meeting_rooms.delete');
    });
    
    // Departments Routes
    Route::prefix('departments')->group(function () {
        Route::get('/', [AdminBASController::class, 'departments'])->name('bas.departments');
        Route::post('/', [AdminBASController::class, 'storeDepartment'])->name('bas.departments.store');
        Route::get('/{id}/edit', [AdminBASController::class, 'editDepartment'])->name('bas.departments.edit');
        Route::put('/{id}', [AdminBASController::class, 'updateDepartment'])->name('bas.departments.update');
        Route::delete('/{id}', [AdminBASController::class, 'deleteDepartment'])->name('bas.departments.delete');
    });
    
    // Bookings Routes
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('bas.bookings.index');
        Route::get('/export', [BookingController::class, 'export'])->name('bas.bookings.export');
        Route::get('/statistics', [BookingController::class, 'getStatistics'])->name('bas.bookings.statistics');
        Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('bas.bookings.available-times');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('bas.bookings.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('bas.bookings.update');
        Route::delete('/{id}', [AdminController::class, 'deleteBooking'])->name('bas.bookings.delete');
        Route::post('/{booking}/approve', [AdminController::class, 'approveBooking'])->name('bas.bookings.approve');
        Route::post('/{booking}/reject', [AdminController::class, 'rejectBooking'])->name('bas.bookings.reject');
    });

    // Employees Routes
    Route::prefix('employees')->group(function () {
        Route::get('/', [AdminBASController::class, 'employees'])->name('bas.employees');
        Route::get('/create', [AdminBASController::class, 'createEmployee'])->name('bas.employees.create');
        Route::post('/', [AdminBASController::class, 'storeEmployee'])->name('bas.employees.store');
        Route::get('/export', [EmployeeController::class, 'export'])->name('bas.employees.export');
        Route::get('/{id}/edit', [AdminBASController::class, 'editEmployee'])->name('bas.employees.edit');
        Route::put('/{id}', [AdminBASController::class, 'updateEmployee'])->name('bas.employees.update');
        Route::delete('/{id}', [AdminBASController::class, 'destroyEmployee'])->name('bas.employees.delete');
    });
    
    // Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [AdminBASController::class, 'reports'])->name('bas.reports');
        Route::post('/data', [ReportController::class, 'getData'])->name('bas.reports.data');
        Route::post('/export', [ReportController::class, 'export'])->name('bas.reports.export');
    });
});
