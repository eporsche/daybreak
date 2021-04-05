<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\EditEmployeeController;
use App\Http\Controllers\TimeTrackingController;
use App\Http\Controllers\CurrentLocationController;
use App\Http\Controllers\LocationCalendarController;
use App\Http\Controllers\LocationSettingsController;
use App\Http\Controllers\LocationInvitationController;
use App\Http\Controllers\ShowEmployeesForAccountController;
use App\Http\Controllers\ShowLocationsForAccountController;

Route::group(['middleware' => 'auth'], function () {

    Route::redirect('/', '/time-tracking', 301);

    Route::put('/current-location', [CurrentLocationController::class, 'update'])
        ->name('current-location.update');

    Route::get('location-invitations/{invitation}', [LocationInvitationController::class, 'accept'])
        ->name('location-invitations.accept');

    Route::get('/time-tracking', [TimeTrackingController::class, 'index'])
        ->name('time-tracking');

    Route::get('/absence', AbsenceController::class)
        ->name('absence');

    Route::get('/report', ReportController::class)
        ->name('report');

    Route::get('/location-settings', [LocationSettingsController::class, 'show'])
        ->name('location-settings');

    Route::get('/location-calendar', [LocationCalendarController::class, 'show'])
        ->name('location-calendar');

    Route::get('/accounts/{account}', [AccountController::class, 'show'])->name('accounts.show');
    Route::get('/accounts/{account}/locations', ShowLocationsForAccountController::class)->name('locations');
    Route::get('/accounts/{account}/employees', ShowEmployeesForAccountController::class)->name('employees');
    Route::get('/accounts/{account}/employees/{employee}', EditEmployeeController::class)->name('employees.edit');
});
