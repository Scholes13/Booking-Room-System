<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Lead\DashboardController;
use App\Http\Controllers\Lead\LeadWorksheetController;

/*
|--------------------------------------------------------------------------
| Lead Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the lead role. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group.
|
*/

Route::group(['prefix' => 'lead', 'middleware' => ['auth', 'is.lead'], 'as' => 'lead.'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Lead Worksheets Routes
    Route::get('/worksheets', [LeadWorksheetController::class, 'index'])->name('worksheets.index');
    Route::get('/worksheets/{worksheet}/edit', [LeadWorksheetController::class, 'edit'])->name('worksheets.edit');
    Route::put('/worksheets/{worksheet}', [LeadWorksheetController::class, 'update'])->name('worksheets.update');
}); 