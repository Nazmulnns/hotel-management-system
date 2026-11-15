<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomImageController;
use App\Http\Controllers\RoomMaintenanceController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
no login needed
*/

//Login
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

//Browse the hotel
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/facilities', [FacilityController::class, 'getAllFacilities']);
    Route::get('/room-types', [RoomTypeController::class, 'getAllRoomTypes']);
    Route::get('/rooms', [RoomController::class, 'getRooms']);
    Route::get('/rooms/{id}', [RoomController::class, 'findRoomById'])->whereNumber('id');
    Route::get('/services', [ServiceController::class, 'getServices']);
    Route::get('/services/{id}', [ServiceController::class, 'findServiceById'])->whereNumber('id');
    Route::get('/reviews', [ReviewController::class, 'getReviews']);
});

//Online booking form (step 1: guest, step 2: booking)
Route::middleware('throttle:10,1')->group(function () {
    Route::post('/guests', [GuestController::class, 'createGuest']);
    Route::post('/bookings', [BookingController::class, 'createBooking']);
});

/*
login needed
*/

Route::middleware('auth:sanctum')->group(function () {

    //Every staff member
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    //Room Maintenances: see and report
    Route::get('/room-maintenances', [RoomMaintenanceController::class, 'getMaintenances']);
    Route::get('/room-maintenances/{id}', [RoomMaintenanceController::class, 'findMaintenanceById'])->whereNumber('id');
    Route::get('/rooms/{id}/maintenances', [RoomMaintenanceController::class, 'getRoomMaintenances'])->whereNumber('id');
    Route::post('/rooms/{id}/maintenances', [RoomMaintenanceController::class, 'reportMaintenance'])->whereNumber('id');

    //Support Requests: see and update
    Route::get('/support-requests', [SupportRequestController::class, 'getSupportRequests']);
    Route::get('/support-requests/{id}', [SupportRequestController::class, 'findSupportRequestById'])->whereNumber('id');
    Route::patch('/support-requests/{id}', [SupportRequestController::class, 'updateSupportRequest'])->whereNumber('id');

    //Manager and Receptionist: front desk
    Route::middleware('role:manager,receptionist')->group(function () {
        //Guests
        Route::get('/guests', [GuestController::class, 'getGuests']);
        Route::get('/guests/{id}', [GuestController::class, 'findGuestById'])->whereNumber('id');
        Route::post('/guests/{id}/support-requests', [SupportRequestController::class, 'createSupportRequest'])->whereNumber('id');

        //Bookings
        Route::get('/bookings', [BookingController::class, 'getBookings']);
        Route::get('/bookings/{id}', [BookingController::class, 'findBookingById'])->whereNumber('id');
        Route::patch('/bookings/{id}/status', [BookingController::class, 'updateBookingStatus'])->whereNumber('id');

        //Reviews
        Route::post('/bookings/{id}/review', [ReviewController::class, 'addReview'])->whereNumber('id');

        //Discounts: see only
        Route::get('/discounts', [DiscountController::class, 'getDiscounts']);
        Route::get('/discounts/{id}', [DiscountController::class, 'findDiscountById'])->whereNumber('id');

        //Invoices
        Route::get('/invoices', [InvoiceController::class, 'getInvoices']);
        Route::get('/invoices/{id}', [InvoiceController::class, 'findInvoiceById'])->whereNumber('id');
        Route::post('/bookings/{id}/invoice', [InvoiceController::class, 'generateInvoice'])->whereNumber('id');
        Route::patch('/invoices/{id}/status', [InvoiceController::class, 'updateInvoiceStatus'])->whereNumber('id');
    });

    //Manager and Housekeeping: fix rooms
    Route::middleware('role:manager,housekeeping')->group(function () {
        Route::put('/room-maintenances/{id}', [RoomMaintenanceController::class, 'updateMaintenance'])->whereNumber('id');
    });

    //Manager only
    Route::middleware('role:manager')->group(function () {
        //Users
        Route::get('/users', [UserController::class, 'getUsers']);
        Route::get('/users/{id}', [UserController::class, 'findUserById'])->whereNumber('id');
        Route::post('/users', [UserController::class, 'createUser']);
        Route::patch('/users/{id}', [UserController::class, 'updateUser'])->whereNumber('id');
        Route::patch('/users/{id}/deactivate', [UserController::class, 'deactivateUser'])->whereNumber('id');
        Route::patch('/users/{id}/activate', [UserController::class, 'activateUser'])->whereNumber('id');

        //Facilities
        Route::post('/facilities', [FacilityController::class, 'addNewFacility']);
        Route::put('/facilities/{id}', [FacilityController::class, 'updateFacility']);
        Route::delete('/facilities/{facilityId}', [FacilityController::class, 'deleteFacilityById']);

        //Room Types
        Route::post('/room-types', [RoomTypeController::class, 'addNewRoomType']);
        Route::put('/room-types/{id}', [RoomTypeController::class, 'updateRoomType']);
        Route::delete('/room-types/{id}', [RoomTypeController::class, 'deleteRoomTypeById']);

        //Rooms
        Route::post('/rooms', [RoomController::class, 'createRoom']);
        Route::put('/rooms/{id}', [RoomController::class, 'updateRoom']);
        Route::delete('/rooms/{id}', [RoomController::class, 'deleteRoom']);

        //Room Images
        Route::post('/rooms/{id}/images', [RoomImageController::class, 'addNewRoomImages']);
        Route::delete('/room-images/{id}', [RoomImageController::class, 'deleteRoomImageById']);

        //Services
        Route::post('/services', [ServiceController::class, 'createNewService']);
        Route::put('/services/{id}', [ServiceController::class, 'updateService']);
        Route::delete('/services/{id}', [ServiceController::class, 'deleteService']);

        //Discounts
        Route::post('/discounts', [DiscountController::class, 'addNewDiscount']);
        Route::put('/discounts/{id}', [DiscountController::class, 'updateDiscount'])->whereNumber('id');
        Route::delete('/discounts/{id}', [DiscountController::class, 'deleteDiscountById'])->whereNumber('id');
    });
});