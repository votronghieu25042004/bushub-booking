<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\ConductorController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AiAssistantController;

/*
|--------------------------------------------------------------------------
| Web Routes - BusHub Central Terminal Bus Booking System
|--------------------------------------------------------------------------
*/

// 1. Fast Login & Role Switcher
Route::post('auth/fast-login', [AuthController::class, 'fastLogin'])->name('auth.fastLogin');
Route::get('auth/fast-login/{role}', [AuthController::class, 'fastLoginGet'])->name('auth.fastLoginGet');

// 1.1 OTP & Quick Authentication
Route::post('auth/send-otp', [AuthController::class, 'sendOtp'])->name('auth.sendOtp');
Route::post('auth/verify-otp', [AuthController::class, 'verifyOtp'])->name('auth.verifyOtp');

// 2. Schedules Route (Lá»‹ch trĂ¬nh toĂ n bá»™ cĂ¡c tuyáº¿n xe)
Route::get('schedules', [TripController::class, 'schedules'])->name('schedules.index');

// 2. Customer Routes (Segment-based Trip Searching & Booking)
Route::get('/', [TripController::class, 'home'])->name('home');
Route::get('trips', [TripController::class, 'search'])->name('trips.index');
Route::get('trips/{id}', [TripController::class, 'show'])->name('trips.show');
Route::post('trips/{id}/book', [TripController::class, 'book'])->name('trips.book');
Route::get('booking/ticket/{code}', [TripController::class, 'ticket'])->name('booking.ticket');

// Customer Reviews
Route::get('booking/{code}/review', [ReviewController::class, 'show'])->name('reviews.show');
Route::post('booking/{code}/review', [ReviewController::class, 'store'])->name('reviews.store');

// 3. Conductor / LÆ¡ Xe Operations
Route::get('staff/manifest', [ConductorController::class, 'dashboard'])->name('staff.manifest');
Route::get('conductor', [ConductorController::class, 'dashboard'])->name('conductor.dashboard');
Route::get('staff', [ConductorController::class, 'dashboard']);

Route::post('conductor/trips/{id}/status', [ConductorController::class, 'updateTripStatus'])->name('conductor.trip.status');
Route::post('conductor/trips/{id}/stop', [ConductorController::class, 'updateCurrentStop'])->name('conductor.trip.stop');
Route::post('conductor/trips/{id}/roadside-ticket', [ConductorController::class, 'createRoadsideTicket'])->name('conductor.ticket.create');
Route::post('conductor/checkin/{id}', [ConductorController::class, 'checkinManual'])->name('conductor.checkin');
Route::post('conductor/bookings/{id}/checkin', [ConductorController::class, 'checkinManual']);
Route::post('conductor/bookings/{id}/change-seat', [ConductorController::class, 'changeSeat'])->name('conductor.changeSeat');
Route::post('conductor/trips/{id}/expense', [ConductorController::class, 'storeExpense'])->name('conductor.expense.store');
Route::post('conductor/trips/{id}/expenses', [ConductorController::class, 'storeExpense']);
Route::post('conductor/trips/{id}/incident', [ConductorController::class, 'reportIncident'])->name('conductor.incident.store');
Route::post('conductor/trips/{id}/lost-found', [ConductorController::class, 'storeLostFound'])->name('conductor.lostFound.store');
Route::post('conductor/trips/{id}/close', [ConductorController::class, 'closeTrip'])->name('conductor.trip.close');

// 3.5 Driver / TĂ i Xáº¿ Operations
Route::get('driver', [DriverController::class, 'index'])->name('driver.index');
Route::post('driver/trips/{id}/accept', [DriverController::class, 'acceptTrip'])->name('driver.trip.accept');
Route::post('driver/trips/{id}/reject', [DriverController::class, 'rejectTrip'])->name('driver.trip.reject');
Route::post('driver/trips/{id}/verify-vehicle', [DriverController::class, 'verifyVehicle'])->name('driver.trip.verifyVehicle');
Route::post('driver/trips/{id}/report-vehicle-issue', [DriverController::class, 'reportVehicleIssue'])->name('driver.trip.reportVehicleIssue');
Route::post('driver/trips/{id}/report-incident', [DriverController::class, 'reportIncident'])->name('driver.trip.reportIncident');
Route::post('driver/trips/{id}/report-delay', [DriverController::class, 'reportDelay'])->name('driver.trip.reportDelay');
Route::post('driver/trips/{id}/start', [DriverController::class, 'startTrip'])->name('driver.trip.start');
Route::post('driver/trips/{id}/finish', [DriverController::class, 'finishTrip'])->name('driver.trip.finish');
Route::post('driver/trips/{id}/stop', [DriverController::class, 'updateCurrentStop'])->name('driver.trip.stop');
Route::post('driver/trips/{id}/close', [DriverController::class, 'closeTrip'])->name('driver.trip.close');

// 4. QR Scanner Operations
Route::get('scanner', [ScannerController::class, 'index'])->name('scanner.index');
Route::post('scanner/verify', [ScannerController::class, 'verify'])->name('scanner.verify');
Route::post('scanner/checkin', [ScannerController::class, 'checkin'])->name('scanner.checkin');

// 5. Admin Portal
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('routes', [AdminController::class, 'routes'])->name('admin.routes');
    Route::post('routes', [AdminController::class, 'storeRoute'])->name('admin.routes.store');

    Route::get('trips', [AdminController::class, 'trips'])->name('admin.trips');
    Route::post('trips', [AdminController::class, 'storeTrip'])->name('admin.trips.store');
    Route::post('trips/{id}/price', [AdminController::class, 'updateTripPrice'])->name('admin.trips.price');
    Route::post('trips/{id}/assign-driver', [AdminController::class, 'assignDriver'])->name('admin.trips.assignDriver');
    Route::post('trips/{id}/status', [AdminController::class, 'updateTripStatus'])->name('admin.trips.updateStatus');

    Route::post('counter-booking', [AdminController::class, 'counterBook'])->name('admin.counterBooking');

    Route::get('buses', [AdminController::class, 'buses'])->name('admin.buses');
    Route::post('buses', [AdminController::class, 'storeBus'])->name('admin.buses.store');
    Route::post('buses/{id}/toggle-status', [AdminController::class, 'toggleBusStatus'])->name('admin.buses.toggleStatus');
    Route::delete('buses/{id}', [AdminController::class, 'deleteBus'])->name('admin.buses.delete');

    Route::get('drivers', [AdminController::class, 'drivers'])->name('admin.drivers');
    Route::post('drivers', [AdminController::class, 'storeDriver'])->name('admin.drivers.store');

    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('customers', [AdminController::class, 'customers'])->name('admin.customers');
    Route::post('users/staff', [AdminController::class, 'storeStaff'])->name('admin.users.storeStaff');

    Route::get('finance', [AdminController::class, 'finance'])->name('admin.finance');
    Route::post('closings/{id}/audit', [AdminController::class, 'auditTripClosing'])->name('admin.closings.audit');
});

// AI Chatbot Assistant & Auto Notification
Route::post('ai/chat', [AiAssistantController::class, 'chat'])->name('ai.chat');
Route::post('api/ai/chat', [AiAssistantController::class, 'chat']);
Route::post('ai/auto-notify', [AiAssistantController::class, 'autoNotify'])->name('ai.autoNotify');
Route::post('api/ai/auto-notify', [AiAssistantController::class, 'autoNotify']);
Route::post('ai/custom-notify', [AiAssistantController::class, 'customNotify'])->name('ai.customNotify');
Route::post('api/ai/custom-notify', [AiAssistantController::class, 'customNotify']);