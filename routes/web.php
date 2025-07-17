<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MeetingRoomController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ActivityReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ActivityTypeController;
use App\Http\Controllers\AdminBASController;
use App\Domains\Sales\Controllers\SalesMissionController;
use App\Domains\Sales\Controllers\SalesOfficerController;
use App\Http\Controllers\FeedbackSurveyController;
use App\Http\Controllers\TeamAssignmentController;
use Illuminate\Support\Facades\Route;
use App\Domains\Sales\Controllers\SalesMission\FeedbackSurveyController as SalesMissionFeedbackSurveyController;
use App\Domains\Sales\Controllers\SalesMission\SalesAgendaController;
use App\Domains\Sales\Controllers\SalesMission\SalesReportsController;
use App\Domains\Activity\Controllers\Admin\ActivityController as AdminActivityController;

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
Route::get('/admin/login', [LoginController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout');
Route::post('/admin/logout', [LoginController::class, 'logout'])->name('admin.logout.post');

// -------------------------------------------------------------------
//                   ROUTE AREA ADMIN
// -------------------------------------------------------------------
Route::group(['prefix' => 'admin', 'middleware' => \App\Http\Middleware\AdminMiddleware::class], function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard/bookings', [AdminDashboardController::class, 'bookings'])->name('admin.dashboard.bookings');
    
    // Meeting Rooms
    Route::prefix('meeting-rooms')->group(function () {
        Route::get('/', [MeetingRoomController::class, 'index'])->name('admin.meeting_rooms');
        Route::get('/create', [MeetingRoomController::class, 'create'])->name('admin.meeting_rooms.create');
        Route::post('/', [MeetingRoomController::class, 'store'])->name('admin.meeting_rooms.store');
        Route::get('/{id}/edit', [MeetingRoomController::class, 'edit'])->name('admin.meeting_rooms.edit');
        Route::put('/{id}', [MeetingRoomController::class, 'update'])->name('admin.meeting_rooms.update');
        Route::delete('/{id}', [MeetingRoomController::class, 'destroy'])->name('admin.meeting_rooms.delete');
    });
    
    // Departments
    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('admin.departments');
        Route::post('/', [DepartmentController::class, 'store'])->name('admin.departments.store');
        Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('admin.departments.edit');
        Route::put('/{id}', [DepartmentController::class, 'update'])->name('admin.departments.update');
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('admin.departments.delete');
    });
    
    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('admin.bookings.index');
        Route::get('/export', [BookingController::class, 'export'])->name('admin.bookings.export');
        Route::get('/statistics', [BookingController::class, 'getStatistics'])->name('admin.bookings.statistics');
        Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('admin.bookings.available-times');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('admin.bookings.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('admin.bookings.update');
        Route::delete('/{id}', [AdminBookingController::class, 'deleteBooking'])->name('admin.bookings.delete');
    });

    // Activities with CRM Integration
    Route::prefix('activities')->group(function () {
        // CRM API endpoints for Sales Mission activities
        Route::get('/company-suggestions', [AdminActivityController::class, 'getCompanySuggestions'])->name('admin.activities.company-suggestions');
        Route::get('/search-companies', [AdminActivityController::class, 'searchCompanies'])->name('admin.activities.search-companies');
        Route::get('/company-history/{companyId}', [AdminActivityController::class, 'getCompanyVisitHistory'])->name('admin.activities.company-history');
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
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'superAdminDashboard'])->name('superadmin.dashboard');

    // User Management Routes
    Route::resource('users', SuperAdminUserController::class)->names([
        'index' => 'superadmin.users.index',
        'create' => 'superadmin.users.create',
        'store' => 'superadmin.users.store',
        'show' => 'superadmin.users.show',
        'edit' => 'superadmin.users.edit',
        'update' => 'superadmin.users.update',
        'destroy' => 'superadmin.users.destroy',
    ]);
    
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
        
        // Sales Mission Activities Management
        Route::get('/sales-mission', [SalesMissionController::class, 'superAdminIndex'])->name('superadmin.activities.sales_mission');
    });
    
    // Meeting Rooms Routes - mirip dengan admin routes tapi dengan prefix superadmin
    Route::prefix('meeting-rooms')->group(function () {
        Route::get('/', [MeetingRoomController::class, 'index'])->name('superadmin.meeting_rooms');
        Route::get('/create', [MeetingRoomController::class, 'create'])->name('superadmin.meeting_rooms.create');
        Route::post('/', [MeetingRoomController::class, 'store'])->name('superadmin.meeting_rooms.store');
        Route::get('/{id}/edit', [MeetingRoomController::class, 'edit'])->name('superadmin.meeting_rooms.edit');
        Route::put('/{id}', [MeetingRoomController::class, 'update'])->name('superadmin.meeting_rooms.update');
        Route::delete('/{id}', [MeetingRoomController::class, 'destroy'])->name('superadmin.meeting_rooms.delete');
    });
    
    // Departments Routes
    Route::prefix('departments')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('superadmin.departments');
        Route::post('/', [DepartmentController::class, 'store'])->name('superadmin.departments.store');
        Route::get('/{id}/edit', [DepartmentController::class, 'edit'])->name('superadmin.departments.edit');
        Route::put('/{id}', [DepartmentController::class, 'update'])->name('superadmin.departments.update');
        Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('superadmin.departments.delete');
    });
    
    // Bookings Routes
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('superadmin.bookings.index');
        Route::get('/export', [BookingController::class, 'export'])->name('superadmin.bookings.export');
        Route::get('/statistics', [BookingController::class, 'getStatistics'])->name('superadmin.bookings.statistics');
        Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('superadmin.bookings.available-times');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('superadmin.bookings.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('superadmin.bookings.update');
        Route::delete('/{id}', [AdminBookingController::class, 'deleteBooking'])->name('superadmin.bookings.delete');
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
    
    // Activity Types Routes
    Route::prefix('activity-types')->group(function () {
        Route::get('/', [ActivityTypeController::class, 'index'])->name('superadmin.activity-types.index');
        Route::get('/create', [ActivityTypeController::class, 'create'])->name('superadmin.activity-types.create');
        Route::post('/', [ActivityTypeController::class, 'store'])->name('superadmin.activity-types.store');
        Route::get('/{activityType}/edit', [ActivityTypeController::class, 'edit'])->name('superadmin.activity-types.edit');
        Route::put('/{activityType}', [ActivityTypeController::class, 'update'])->name('superadmin.activity-types.update');
        Route::delete('/{activityType}', [ActivityTypeController::class, 'destroy'])->name('superadmin.activity-types.destroy');
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
        Route::get('/create', [MeetingRoomController::class, 'create'])->name('bas.meeting_rooms.create');
        Route::post('/', [MeetingRoomController::class, 'store'])->name('bas.meeting_rooms.store');
        Route::get('/{id}/edit', [MeetingRoomController::class, 'edit'])->name('bas.meeting_rooms.edit');
        Route::put('/{id}', [MeetingRoomController::class, 'update'])->name('bas.meeting_rooms.update');
        Route::delete('/{id}', [MeetingRoomController::class, 'destroy'])->name('bas.meeting_rooms.delete');
    });
    
    // Departments Routes
    Route::prefix('departments')->group(function () {
        Route::get('/', [AdminBASController::class, 'departments'])->name('bas.departments');
        Route::post('/', [AdminBASController::class, 'storeDepartment'])->name('bas.departments.store');
        Route::get('/{id}/edit', [AdminBASController::class, 'editDepartment'])->name('bas.departments.edit');
        Route::put('/{id}', [AdminBASController::class, 'updateDepartment'])->name('bas.departments.update');
        Route::delete('/{id}', [AdminBASController::class, 'deleteDepartment'])->name('bas.departments.delete');
    });
    
    // Activity Types Routes
    Route::prefix('activity-types')->group(function () {
        Route::get('/', [ActivityTypeController::class, 'index'])->name('bas.activity-types.index');
        Route::get('/create', [ActivityTypeController::class, 'create'])->name('bas.activity-types.create');
        Route::post('/', [ActivityTypeController::class, 'store'])->name('bas.activity-types.store');
        Route::get('/{activityType}/edit', [ActivityTypeController::class, 'edit'])->name('bas.activity-types.edit');
        Route::put('/{activityType}', [ActivityTypeController::class, 'update'])->name('bas.activity-types.update');
        Route::delete('/{activityType}', [ActivityTypeController::class, 'destroy'])->name('bas.activity-types.destroy');
    });
    
    // Bookings Routes
    Route::prefix('bookings')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('bas.bookings.index');
        Route::get('/export', [BookingController::class, 'export'])->name('bas.bookings.export');
        Route::get('/statistics', [BookingController::class, 'getStatistics'])->name('bas.bookings.statistics');
        Route::get('/available-times', [BookingController::class, 'getAvailableTimes'])->name('bas.bookings.available-times');
        Route::get('/{id}/edit', [BookingController::class, 'edit'])->name('bas.bookings.edit');
        Route::put('/{id}', [BookingController::class, 'update'])->name('bas.bookings.update');
        Route::delete('/{id}', [AdminBookingController::class, 'deleteBooking'])->name('bas.bookings.delete');
        Route::post('/{booking}/approve', [AdminBASController::class, 'approveBooking'])->name('bas.bookings.approve');
        Route::post('/{booking}/reject', [AdminBASController::class, 'rejectBooking'])->name('bas.bookings.reject');
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
    
    // AI Parser Routes
    Route::post('/parse-activity', [AdminBASController::class, 'parseActivity'])->name('bas.parse-activity');
});

// -------------------------------------------------------------------
//                   ROUTE AREA SALES MISSION
// -------------------------------------------------------------------
Route::group(['prefix' => 'sales', 'middleware' => \App\Http\Middleware\SalesMissionMiddleware::class], function() {
    Route::get('/dashboard', [SalesMissionController::class, 'dashboard'])->name('sales_mission.dashboard');
    
    // Activities Management Routes
    Route::prefix('activities')->group(function () {
        Route::get('/', [SalesMissionController::class, 'activitiesIndex'])->name('sales_mission.activities.index');
        Route::get('/export', [SalesMissionController::class, 'exportActivities'])->name('sales_mission.activities.export');
        Route::get('/{activity}/edit', [SalesMissionController::class, 'editActivity'])->name('sales_mission.activities.edit');
        Route::put('/{activity}', [SalesMissionController::class, 'updateActivity'])->name('sales_mission.activities.update');
        Route::delete('/{activity}', [SalesMissionController::class, 'destroyActivity'])->name('sales_mission.activities.destroy');
        Route::get('/calendar', [SalesMissionController::class, 'activitiesCalendar'])->name('sales_mission.activities.calendar');
        Route::get('/json', [SalesMissionController::class, 'activitiesJson'])->name('sales_mission.activities.json');
    });
    
    // Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [SalesMissionController::class, 'reports'])->name('sales_mission.reports');
        Route::post('/data', [SalesMissionController::class, 'getReportData'])->name('sales_mission.reports.data');
        Route::post('/export', [SalesMissionController::class, 'exportReport'])->name('sales_mission.reports.export');
        
        // New Agenda Routes
        Route::get('/agenda', [SalesAgendaController::class, 'index'])->name('sales_mission.reports.agenda');
        Route::post('/agenda/generate', [SalesAgendaController::class, 'generateAgenda'])->name('sales_mission.reports.agenda.generate');
        Route::post('/agenda/export', [SalesAgendaController::class, 'exportAgenda'])->name('sales_mission.reports.agenda.export');

        // New Survey Reports Routes
        Route::get('/surveys', [SalesReportsController::class, 'surveyReports'])->name('sales_mission.reports.surveys');
        Route::post('/surveys/data', [SalesReportsController::class, 'getSurveyReportData'])->name('sales_mission.reports.surveys.data');
        Route::post('/surveys/export', [SalesReportsController::class, 'exportSurveyReport'])->name('sales_mission.reports.surveys.export');
    });
    
    // Teams Management
    Route::resource('teams', \App\Http\Controllers\TeamController::class)->names([
        'index' => 'sales_mission.teams.index',
        'create' => 'sales_mission.teams.create',
        'store' => 'sales_mission.teams.store',
        'show' => 'sales_mission.teams.show',
        'edit' => 'sales_mission.teams.edit',
        'update' => 'sales_mission.teams.update',
        'destroy' => 'sales_mission.teams.destroy',
    ]);
    
    // Get teams in JSON format for dropdowns and modals
    Route::get('teams-json', [\App\Http\Controllers\TeamController::class, 'getTeamsJson'])
        ->name('sales_mission.teams.json');
    
    // Field Visits (Team Assignments)
    Route::resource('field-visits', \App\Http\Controllers\TeamAssignmentController::class)->parameters([
        'field-visits' => 'fieldVisit'
    ])->names([
        'index' => 'sales_mission.field-visits.index',
        'create' => 'sales_mission.field-visits.create',
        'store' => 'sales_mission.field-visits.store',
        'show' => 'sales_mission.field-visits.show',
        'edit' => 'sales_mission.field-visits.edit',
        'update' => 'sales_mission.field-visits.update',
        'destroy' => 'sales_mission.field-visits.destroy',
    ]);

    // Feedback Survey Routes - Admin side
    Route::prefix('surveys')->name('sales_mission.surveys.')->group(function () {
        Route::get('/', [FeedbackSurveyController::class, 'index'])->name('index');
        Route::get('/{survey}', [FeedbackSurveyController::class, 'show'])->name('show');
        Route::get('/generate/{teamAssignment}', [FeedbackSurveyController::class, 'generateSurvey'])->name('generate');
    });

    // Feedback Surveys (yang diakses dari admin area /sales)
    Route::get('/surveys/{survey_token}', [SalesMissionFeedbackSurveyController::class, 'viewSurveyFromAdmin'])
        ->name('surveys.view.admin'); // Contoh nama rute, sesuaikan


    // Daily Schedule View for Admin
    Route::get('/daily-schedule', [TeamAssignmentController::class, 'adminDailySchedule'])->name('sales_mission.daily_schedule'); // Nama rute yang diinginkan
});

// Public Feedback Survey Routes (no auth required)
Route::prefix('feedback')->name('sales_mission.surveys.public.')->group(function () {
    Route::get('/survey/{identifier}', [SalesMissionFeedbackSurveyController::class, 'publicSurvey'])->name('form');
    Route::post('/survey/{identifier}', [SalesMissionFeedbackSurveyController::class, 'submitFeedback'])->name('submit');
    Route::get('/thank-you', [SalesMissionFeedbackSurveyController::class, 'thankYou'])->name('thank_you');

    Route::get('/sales-blitz', [SalesMissionFeedbackSurveyController::class, 'showSalesBlitzForm'])->name('sales_blitz_form');
    Route::post('/sales-blitz', [SalesMissionFeedbackSurveyController::class, 'submitSalesBlitzForm'])->name('sales_blitz_submit');
    Route::get('/view/{identifier}', [SalesMissionFeedbackSurveyController::class, 'publicViewFeedback'])->name('view_feedback');
});

// Public Field Visits Routes
Route::prefix('field-visits')->name('public.field-visits.')->group(function () {
    Route::get('/', [TeamAssignmentController::class, 'publicIndex'])->name('index');
    Route::get('/calendar-data', [TeamAssignmentController::class, 'calendarData'])->name('calendar-data');
    Route::get('/{fieldVisit}', [TeamAssignmentController::class, 'publicDetail'])->name('detail');
});

// Simple redirect for easier access to public field visits
Route::get('/public/field-visits', function() {
    return redirect()->route('public.field-visits.index');
});

// -------------------------------------------------------------------
//                   ROUTE AREA SALES OFFICER
// -------------------------------------------------------------------
Route::group(['prefix' => 'officer', 'middleware' => \App\Http\Middleware\SalesOfficerMiddleware::class], function() {
    Route::get('/dashboard', [SalesOfficerController::class, 'dashboard'])->name('sales_officer.dashboard');
    
    // Activities Management Routes
    Route::prefix('activities')->group(function () {
        Route::get('/', [SalesOfficerController::class, 'activitiesIndex'])->name('sales_officer.activities.index');
        Route::get('/create', [SalesOfficerController::class, 'createActivity'])->name('sales_officer.activities.create');
        Route::post('/', [SalesOfficerController::class, 'storeActivity'])->name('sales_officer.activities.store');
        Route::get('/{activity}/edit', [SalesOfficerController::class, 'editActivity'])->name('sales_officer.activities.edit');
        Route::put('/{activity}', [SalesOfficerController::class, 'updateActivity'])->name('sales_officer.activities.update');
        Route::delete('/{activity}', [SalesOfficerController::class, 'destroyActivity'])->name('sales_officer.activities.destroy');
    });
    
    // Calendar Routes
    Route::get('/calendar', [SalesOfficerController::class, 'calendar'])->name('sales_officer.calendar');
    Route::get('/calendar/events', [SalesOfficerController::class, 'calendarEvents'])->name('sales_officer.calendar.events');
    
    // Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('/', [SalesOfficerController::class, 'reports'])->name('sales_officer.reports.index');
        Route::post('/data', [SalesOfficerController::class, 'getReportData'])->name('sales_officer.reports.data');
        Route::post('/export', [SalesOfficerController::class, 'exportReport'])->name('sales_officer.reports.export');
    });
    
    // Contacts Routes
    Route::prefix('contacts')->group(function () {
        Route::get('/', [SalesOfficerController::class, 'contactsIndex'])->name('sales_officer.contacts.index');
        Route::get('/create', [SalesOfficerController::class, 'createContact'])->name('sales_officer.contacts.create');
        Route::post('/', [SalesOfficerController::class, 'storeContact'])->name('sales_officer.contacts.store');
        Route::get('/mission/{id}', [SalesOfficerController::class, 'getSalesMissionContact'])->name('sales_officer.contacts.mission');
        Route::get('/{contact}/edit', [SalesOfficerController::class, 'editContact'])->name('sales_officer.contacts.edit');
        Route::put('/{contact}', [SalesOfficerController::class, 'updateContact'])->name('sales_officer.contacts.update');
        Route::delete('/{contact}', [SalesOfficerController::class, 'destroyContact'])->name('sales_officer.contacts.destroy');
        Route::get('/{contact}', [SalesOfficerController::class, 'viewContact'])->name('sales_officer.contacts.show');
        
        // Contact Person Management
        Route::post('/{contact}/pic', [SalesOfficerController::class, 'storePIC'])->name('sales_officer.contacts.store_pic');
        Route::get('/edit-pic/{id}', [SalesOfficerController::class, 'editPIC'])->name('sales_officer.contacts.edit_pic');
        Route::put('/update-pic/{id}', [SalesOfficerController::class, 'updatePIC'])->name('sales_officer.contacts.update_pic');
        Route::delete('/delete-pic/{id}', [SalesOfficerController::class, 'destroyPIC'])->name('sales_officer.contacts.destroy_pic');
        
        // Division Management
        Route::post('/{contact}/division', [SalesOfficerController::class, 'storeDivision'])->name('sales_officer.contacts.store_division');
        Route::get('/edit-division/{id}', [SalesOfficerController::class, 'editDivision'])->name('sales_officer.contacts.edit_division');
        Route::put('/update-division/{id}', [SalesOfficerController::class, 'updateDivision'])->name('sales_officer.contacts.update_division');
        Route::delete('/delete-division/{id}', [SalesOfficerController::class, 'destroyDivision'])->name('sales_officer.contacts.destroy_division');
    });
    
    // API Routes for activity form
    Route::get('/api/companies', [SalesOfficerController::class, 'getCompanies'])
        ->name('sales_officer.api.companies');
    Route::get('/api/company/{company_id}/divisions', [SalesOfficerController::class, 'getCompanyDivisions'])
        ->name('sales_officer.api.company.divisions');
    Route::get('/api/company/{company_id}/pics', [SalesOfficerController::class, 'getCompanyPICs'])
        ->name('sales_officer.api.company.pics');
    Route::get('/api/division/{division_id}/pics', [SalesOfficerController::class, 'getDivisionPICs'])
        ->name('sales_officer.api.division.pics');
    Route::get('/api/company/{company_id}/follow-up-history', [SalesOfficerController::class, 'getCompanyFollowUpHistory'])
        ->name('sales_officer.api.company.follow-up-history');
});

// Fonnte WhatsApp Testing
Route::get('/test-fonnte-page', [App\Http\Controllers\TestController::class, 'fontneTestPage']);
Route::post('/test-fonnte', [App\Http\Controllers\TestController::class, 'testFonnte']);
Route::post('/test-fonnte-link', [App\Http\Controllers\TestController::class, 'testFontneLink']);

// Add a test route at the bottom of the file
Route::get('/generate-test-sales-mission', function() {
    $department = \App\Models\Department::first();
    if (!$department) {
        $department = \App\Models\Department::create([
            'name' => 'Sales Department',
            'code' => 'SALES'
        ]);
    }
    
    // Create sample activity
    $activity = \App\Models\Activity::create([
        'name' => 'John Doe',
        'department_id' => $department->id,
        'activity_type' => 'Sales Mission',
        'description' => 'Meeting with PT ABC for product presentation',
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'start_datetime' => now()->subDays(5),
        'end_datetime' => now()->subDays(5)->addHours(2),
    ]);
    
    // Create sample sales mission detail
    \App\Models\SalesMissionDetail::create([
        'activity_id' => $activity->id,
        'company_name' => 'PT ABC Technology',
        'company_pic' => 'Michael Johnson',
        'company_contact' => '081234567890',
        'company_address' => 'Jl. Sudirman No. 123, Jakarta Pusat'
    ]);
    
    return "Created test sales mission data. <a href='/sales/reports?debug=1'>View Reports</a>";
});

// Add a debug route to check user roles
Route::get('/debug-users', function() {
    $users = \App\Models\User::all();
    return $users->map(function($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ];
    });
});

// Add a route to create a test sales mission user
Route::get('/create-sales-user', function() {
    $user = \App\Models\User::where('email', 'sales@example.com')->first();
    if (!$user) {
        $user = \App\Models\User::create([
            'name' => 'Test Sales User',
            'email' => 'sales@example.com',
            'password' => bcrypt('password'),
            'role' => 'sales_mission'
        ]);
        return "Sales Mission user created successfully.";
    }
    return "Sales Mission user already exists.";
});

// Add a route to create a test sales officer user
Route::get('/create-officer-user', function() {
    $user = \App\Models\User::where('email', 'officer@example.com')->first();
    if (!$user) {
        $user = \App\Models\User::create([
            'name' => 'Test Sales Officer',
            'email' => 'officer@example.com',
            'password' => bcrypt('password'),
            'role' => 'sales_officer'
        ]);
        return "Sales Officer user created successfully.";
    }
    return "Sales Officer user already exists.";
});


// -------------------------------------------------------------------
//                   API ROUTES FOR DASHBOARD
// -------------------------------------------------------------------
Route::group(['prefix' => 'api', 'middleware' => \App\Http\Middleware\AdminMiddleware::class], function() {
    // Employee API routes
    Route::get('/employees', [App\Http\Controllers\Api\EmployeeController::class, 'index'])->name('api.employees.index');
    Route::get('/employees/search', [App\Http\Controllers\Api\EmployeeController::class, 'search'])->name('api.employees.search');
    Route::get('/employees/by-name', [App\Http\Controllers\Api\EmployeeController::class, 'getByName'])->name('api.employees.by-name');
    
    // Dashboard API routes
    Route::get('/dashboard/employees', [App\Http\Controllers\Admin\DashboardController::class, 'getEmployees'])->name('api.dashboard.employees');
    Route::get('/dashboard/employees/search', [App\Http\Controllers\Admin\DashboardController::class, 'searchEmployees'])->name('api.dashboard.employees.search');
    Route::get('/dashboard/employees/by-name', [App\Http\Controllers\Admin\DashboardController::class, 'getEmployeeByName'])->name('api.dashboard.employees.by-name');
    Route::get('/dashboard/departments', [App\Http\Controllers\Admin\DashboardController::class, 'getDepartments'])->name('api.dashboard.departments');
    Route::get('/dashboard/meeting-rooms', [App\Http\Controllers\Admin\DashboardController::class, 'getMeetingRooms'])->name('api.dashboard.meeting-rooms');
    
    // Booking API routes
    Route::put('/bookings/{id}', [App\Http\Controllers\Api\BookingController::class, 'update'])->name('api.bookings.update');
    Route::delete('/bookings/{id}', [App\Http\Controllers\Api\BookingController::class, 'destroy'])->name('api.bookings.destroy');
    Route::get('/bookings/available-times', [App\Http\Controllers\Api\BookingController::class, 'getAvailableTimes'])->name('api.bookings.available-times');
    Route::post('/bookings/validate-time-slot', [App\Http\Controllers\Api\BookingController::class, 'validateTimeSlot'])->name('api.bookings.validate-time-slot');
});

// SuperAdmin API routes
Route::group(['prefix' => 'api', 'middleware' => \App\Http\Middleware\SuperAdminMiddleware::class], function() {
    // Employee API routes for SuperAdmin
    Route::get('/superadmin/employees', [App\Http\Controllers\Api\EmployeeController::class, 'index'])->name('api.superadmin.employees.index');
    Route::get('/superadmin/employees/search', [App\Http\Controllers\Api\EmployeeController::class, 'search'])->name('api.superadmin.employees.search');
    Route::get('/superadmin/employees/by-name', [App\Http\Controllers\Api\EmployeeController::class, 'getByName'])->name('api.superadmin.employees.by-name');
    
    // Booking API routes for SuperAdmin
    Route::put('/superadmin/bookings/{id}', [App\Http\Controllers\Api\BookingController::class, 'update'])->name('api.superadmin.bookings.update');
    Route::delete('/superadmin/bookings/{id}', [App\Http\Controllers\Api\BookingController::class, 'destroy'])->name('api.superadmin.bookings.destroy');
    Route::get('/superadmin/bookings/available-times', [App\Http\Controllers\Api\BookingController::class, 'getAvailableTimes'])->name('api.superadmin.bookings.available-times');
    Route::post('/superadmin/bookings/validate-time-slot', [App\Http\Controllers\Api\BookingController::class, 'validateTimeSlot'])->name('api.superadmin.bookings.validate-time-slot');
});

// AdminBAS API routes
Route::group(['prefix' => 'api', 'middleware' => \App\Http\Middleware\AdminBASMiddleware::class], function() {
    // Employee API routes for AdminBAS
    Route::get('/bas/employees', [App\Http\Controllers\Api\EmployeeController::class, 'index'])->name('api.bas.employees.index');
    Route::get('/bas/employees/search', [App\Http\Controllers\Api\EmployeeController::class, 'search'])->name('api.bas.employees.search');
    Route::get('/bas/employees/by-name', [App\Http\Controllers\Api\EmployeeController::class, 'getByName'])->name('api.bas.employees.by-name');
    
    // Booking API routes for AdminBAS
    Route::put('/bas/bookings/{id}', [App\Http\Controllers\Api\BookingController::class, 'update'])->name('api.bas.bookings.update');
    Route::delete('/bas/bookings/{id}', [App\Http\Controllers\Api\BookingController::class, 'destroy'])->name('api.bas.bookings.destroy');
    Route::get('/bas/bookings/available-times', [App\Http\Controllers\Api\BookingController::class, 'getAvailableTimes'])->name('api.bas.bookings.available-times');
    Route::post('/bas/bookings/validate-time-slot', [App\Http\Controllers\Api\BookingController::class, 'validateTimeSlot'])->name('api.bas.bookings.validate-time-slot');
});

// -------------------------------------------------------------------
//                   CRM TEST ROUTES (DEVELOPMENT ONLY)
// -------------------------------------------------------------------
Route::get('/test-crm', [App\Http\Controllers\CrmTestController::class, 'testCrmSystem'])->name('test.crm');
Route::post('/test-crm/create-mission', [App\Http\Controllers\CrmTestController::class, 'testCreateSalesMission'])->name('test.crm.create');

// Test CRM Integration with Activity Service
Route::get('/test-crm-integration', function() {
    try {
        // Test creating an activity with Sales Mission type
        $activityService = app(\App\Domains\Activity\Services\ActivityService::class);
        
        $testData = [
            'name' => 'Test CRM Integration Activity',
            'description' => 'Testing CRM integration with activity creation',
            'type' => 'meeting',
            'activity_type' => 'Sales Mission',
            'date' => now()->format('Y-m-d'),
            'start_time' => '09:00',
            'end_time' => '11:00',
            'location' => 'Jakarta',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'sales_mission_data' => [
                'company_name' => 'PT Test CRM Integration',
                'company_pic' => 'John Doe',
                'company_position' => 'Manager',
                'company_contact' => '081234567890',
                'company_email' => 'john@testcrm.com',
                'company_address' => 'Jl. Test CRM No. 123, Jakarta'
            ]
        ];
        
        $activity = $activityService->create($testData);
        
        // Check if CRM integration worked
        $salesMissionDetail = \App\Models\SalesMissionDetail::where('activity_id', $activity->id)->first();
        
        if ($salesMissionDetail && $salesMissionDetail->company_id && $salesMissionDetail->company_contact_id) {
            return response()->json([
                'success' => true,
                'message' => 'CRM Integration working successfully!',
                'data' => [
                    'activity_id' => $activity->id,
                    'company_id' => $salesMissionDetail->company_id,
                    'company_name' => $salesMissionDetail->company->name,
                    'contact_id' => $salesMissionDetail->company_contact_id,
                    'contact_name' => $salesMissionDetail->companyContact->name,
                    'visit_type' => $salesMissionDetail->visit_type,
                    'visit_sequence' => $salesMissionDetail->visit_sequence
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'CRM Integration failed - fallback method used',
                'data' => $salesMissionDetail ? $salesMissionDetail->toArray() : null
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error testing CRM integration: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('test.crm.integration');

require __DIR__.'/lead.php';
