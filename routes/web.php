<?php

use App\Mail\ReviewRequest;
use App\Mail\SupportRequestReceived;
use App\Models\Booking;
use App\Models\RoomMaintenance;
use App\Models\SupportRequest;
use App\Notifications\MaintenanceReported;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//mail preview for review request
Route::get('/mail-preview/review-request', fn () => new ReviewRequest(
    Booking::where('status', 'checked_out')->with('guest')->firstOrFail()
));

//mail previous for recieving support request
Route::get('/mail-preview/support-request', fn () => new SupportRequestReceived(
    SupportRequest::with(['guest', 'room'])->latest()->firstOrFail()
));

Route::get('/notification-preview/maintenance', fn () => (new MaintenanceReported(
    RoomMaintenance::with('room')->latest()->firstOrFail()
))->toMail(null));