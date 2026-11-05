<?php

use App\Mail\ReviewRequest;
use App\Models\Booking;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mail-preview/review-request', fn () => new ReviewRequest(
    Booking::where('status', 'checked_out')->with('guest')->firstOrFail()
));
